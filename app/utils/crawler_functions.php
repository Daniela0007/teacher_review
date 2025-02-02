<?php
function cleanCourseData(&$data) {
    foreach ($data as $year => &$yearData) {
        foreach ($yearData['courses'] as &$professors) {
            $professors = array_values($professors);
        }
    }
    return $data;
}

function mergeDuplicateCourses(&$data) {
    $translations = [
        "Mathematics - Differential and integral calculus" => "Matematică - Calcul diferențial și integral",
        "Databases" => "Baze de date",
        "Data structures" => "Structuri de date",
        "Computer Networks" => "Reţele de calculatoare",
        "Computer Architecture and Operating Systems" => "Arhitectura calculatoarelor şi sistemelor de operare",
        "Graph Algorithms" => "Algoritmica grafurilor",
        "Artificial Intelligence" => "Inteligenţă artificială",
        "Development of Physical Systems Using Microprocessors" => "Dezvoltarea sistemelor fizice utilizând microprocesoare",
        "English Language I" => "Limba Engleză I",
        "English Language III" => "Limba Engleză III",
        "Ethics and academic integrity" => "Etică și integritate academică",
        "Formal Languages, Automata and Compilers" => "Limbaje formale, automate si compilatoare",
        "Information Security" => "Securitatea informaţiei",
        "Introduction to Programming" => "Introducere în programare",
        "Logics for Computer Science" => "Logică pentru informatică",
        "Machine Learning" => "Învățare automată",
        "Python Programming" => "Programare în Python",
        "Doctoral School" => "Școala Doctorală"
    ];
  
    foreach ($data as $year => &$yearData) {
        $courses = &$yearData['courses'];
  
        foreach ($translations as $eng => $ro) {
            if (isset($courses[$eng]) && isset($courses[$ro])) {
                $courses[$ro] = array_unique(array_merge($courses[$ro], $courses[$eng]));
  
                unset($courses[$eng]);
            }
        }
    }
    return $data;
}
  
function mapYear($group) {
    if (strpos($group, "I1") === 0) {
        return "Licență anul 1";
    } elseif (strpos($group, "I2") === 0) {
        return "Licență anul 2";
    } elseif (strpos($group, "I3") === 0) {
        return "Licență anul 3";
    } elseif (preg_match('/M.*1/', $group)) {
        return "Master anul 1";
    } elseif (preg_match('/M.*2/', $group)) {
        return "Master anul 2";
    }
    return "unknown";
}

function crawl($url) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36',
    ]);
    $response = curl_exec($ch);
    if ($response === false) {
        throw new Exception('cURL Error: ' . curl_error($ch));
    }
    curl_close($ch);
    return $response;
}

function extractCourses($baseUrl) {
    $content = crawl($baseUrl . "orar/orar_discipline.html");

    $dom = new DOMDocument();
    @$dom->loadHTML($content);
    $xpath = new DOMXPath($dom);

    $courseNodes = $xpath->query("//ul/li/a[contains(@href, 'discipline')]");
    $courses = [];

    foreach ($courseNodes as $courseNode) {
        $courseName = trim($courseNode->textContent);
        if (in_array($courseName, ["Educație fizică", "Physical Education"])) {
            continue;
        }
        
        $courseUrl = $baseUrl . 'orar/' . $courseNode->getAttribute('href');
        $courses[] = [
            'name' => $courseName,
            'url' => $courseUrl
        ];
    }

    return $courses;
}

function extractCourseDetails($courseUrl) {
    $content = crawl($courseUrl);

    $dom = new DOMDocument();
    @$dom->loadHTML($content);
    $xpath = new DOMXPath($dom);

    $professors = [];
    $group = null;

    $headerNodes = $xpath->query("//table/tr/th");
    $professorColumnIndex = -1;

    foreach ($headerNodes as $index => $headerNode) {
        if (stripos(trim($headerNode->textContent), 'Profesor') !== false) {
            $professorColumnIndex = $index;
            break;
        }
    }

    if ($professorColumnIndex === -1) {
        throw new Exception("Couldn't find 'Profesor' column in the table.");
    }

    $groupNodes = $xpath->query("//td/a[contains(@href, '../participanti')]");
    foreach ($groupNodes as $groupNode) {
        if (preg_match('/(I\d|M.*\d)/', $groupNode->nodeValue, $matches)) {
            $group = $matches[0];
            break;
        }
    }

    $rows = $xpath->query("//table/tr");
    foreach ($rows as $row) {
        $cells = $xpath->query(".//td", $row);

        if ($cells->length > $professorColumnIndex) {
            $professorCell = $cells->item($professorColumnIndex);
            if ($professorCell) {
                $professorLink = $xpath->query(".//a[contains(@href, '../participanti')]", $professorCell);
                foreach ($professorLink as $profNode) {
                    $professors[] = trim($profNode->textContent);
                }
            }
        }
    }

    $professors = array_unique($professors);
    $professors = array_values($professors);

    return [
        'group' => $group,
        'listProfs' => $professors
    ];
}

function structureData($baseUrl) {
$courses = extractCourses($baseUrl);
$data = [];
$professorsList = [];

foreach ($courses as $course) {
    $details = extractCourseDetails($course['url']);
    $group = $details['group'];
    $year = $group !== null ? mapYear($group) : "unknown";

    if (!isset($data[$year])) {
        $data[$year] = [
            'courses' => []
        ];
    }

    $data[$year]['courses'][$course['name']] = $details['listProfs'];

    foreach ($details['listProfs'] as $professor) {
        if (!in_array($professor, $professorsList)) {
                 $professorsList[] = $professor;
            }
        }
    }
    $data = mergeDuplicateCourses($data);
    $cleanedData = cleanCourseData($data);
    return [
        'structuredData' => $cleanedData,
        'professorsList' => array_values($professorsList) // Sortăm sau procesăm lista de profesori dacă este necesar
    ];
}