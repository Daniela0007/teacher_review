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
    
    if (!isset($questionsData['categories']) || !is_array($questionsData['categories'])) {
        throw new Exception("Invalid JSON structure: 'categories' key not found or not an array.");
    }
    
    $insertStmt = $db->prepare("INSERT INTO Questions (question_text) VALUES (:question_text)");
    
    foreach ($questionsData['categories'] as $category) {
        if (!isset($category['questions']) || !is_array($category['questions'])) {
            throw new Exception("Invalid JSON structure: 'questions' key not found or not an array in category.");
        }
    
        foreach ($category['questions'] as $questionText) {
            $insertStmt->bindValue(':question_text', $questionText);
            $insertStmt->execute();
        }
    }
    
    echo "Questions populated successfully.\n";    

  } catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
