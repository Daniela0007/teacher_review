<?php
require_once __DIR__ . '/../models/Database.php';

try {
    $db = Database::getConnection();

    $sqlFilePath = 'db.sql'; 
    $sqlContent = file_get_contents($sqlFilePath);
    $db->exec($sqlContent);
    echo "Tables created successfully.\n";

    $jsonFilePath = 'questions.json';
    $jsonData = file_get_contents($jsonFilePath);
    $questionsData = json_decode($jsonData, true);

    if (!isset($questionsData['questions']) || !is_array($questionsData['questions'])) {
        throw new Exception("Invalid JSON structure: 'questions' key not found or not an array.");
    }

    $insertStmt = $db->prepare("INSERT INTO Questions (question_text) VALUES (:question_text)");
    foreach ($questionsData['questions'] as $questionText) {
        $insertStmt->bindValue(':question_text', $questionText);
        $insertStmt->execute();
    }
    echo "Questions populated successfully.\n";

  } catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
