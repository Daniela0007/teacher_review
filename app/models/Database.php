<?php

class Database {
    protected static $connection;
    public static function getConnection() {
        if (!self::$connection) {
            self::$connection = new PDO('sqlite:' . __DIR__ . '/../utils/feedback.db');
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$connection;
    }
}

// try {
//   $db = Database::getConnection();
//   $result = $db->query("SELECT * FROM Feedback;");
//   $rows = $result->fetchAll(PDO::FETCH_ASSOC);

//   if (!$rows) {
//       echo "No data found. The script might be pointing to the wrong database file.";
//   } else {
//       print_r($rows);
//   }

//     $db = null;

// } catch (PDOException $e) {
//   echo "Failed to connect to the database: " . $e->getMessage();
// }


