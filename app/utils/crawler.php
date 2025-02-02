<?php

require 'crawler_functions.php';

    try {
        $baseUrl = "https://edu.info.uaic.ro/";

        $result = structureData($baseUrl);

        $structuredData = $result['structuredData'];
        $professorsList = $result['professorsList'];
        
        if (isset($structuredData['unknown'])) {
            unset($structuredData['unknown']);
        }

        $filePathData = __DIR__ . '/data.json';
        saveDataToFile($structuredData, $filePathData);

        echo $structuredData;

        // $filePathProfessors = __DIR__ . '/professors.json';
        // saveDataToFile($professorsList, $filePathProfessors);

        header('Content-Type: application/json');
        echo json_encode($structuredData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'error' => $e->getMessage()
        ]);
    }

function saveDataToFile(array $data, string $filePath): void {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if (file_put_contents($filePath, $json) === false) {
        throw new Exception("Failed to write data to file: $filePath");
    }
}
