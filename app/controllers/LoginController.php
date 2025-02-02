<?php
require_once __DIR__ . '/../models/UserModel.php';

class LoginController {
    public function index() {
        include __DIR__ . '/../views/login.php';
    }

    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
            exit;
        }
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        $userModel = new UserModel();

        $role = $userModel->getUserRole($username, $password);

        $adminProfessors = [
          "Prof. dr. Alboaie Lenuța",
          "Conf. dr. Arusoaie Andrei"
      ];
      
      if ($role === 'student') {
          $_SESSION['username'] = $username;
          $_SESSION['role'] = 'student';
          echo json_encode(['status' => 'success', 'redirect_url' => 'student']);
          exit;
      } elseif ($role === 'admin') {
          $_SESSION['username'] = $username;
          $_SESSION['role'] = 'admin';
          echo json_encode(['status' => 'success', 'redirect_url' => 'admin']);
          exit;
      } elseif ($role === 'professor') {
          $teacherName = $this->findTeacherName($username);
          //echo $teacherName;
          if (in_array($teacherName, $adminProfessors)) {
              $_SESSION['username'] = $teacherName;
              $_SESSION['role'] = 'admin';
              echo json_encode(['status' => 'success', 'redirect_url' => 'admin']);
              exit;
          } else {
              $_SESSION['username'] = $teacherName;
              $_SESSION['role'] = 'professor';
              echo json_encode(['status' => 'success', 'redirect_url' => 'teacher']);
              exit;
          }
      } else {
          $_SESSION['username'] = '';
          echo json_encode(['status' => 'error', 'message' => 'Nume de utilizator sau parolă invalidă.']);
          exit;
      }
    }

    public function logout() {
        session_start();
        session_unset();
        session_destroy();

        header('Location: login');
        exit;
    }

    private function findTeacherName($teacher_mail) {
      $jsonFile = __DIR__ . '/../utils/professors.json'; 
      $teacherList = json_decode(file_get_contents($jsonFile), true);
  
      if (preg_match('/^([a-zA-Z]+)\.([a-zA-Z]+)@/', $teacher_mail, $matches)) {
          $lastName = ucfirst(strtolower($matches[1]));  
          $firstName = ucfirst(strtolower($matches[2])); 
          $emailName = "$firstName $lastName";  
  
          $bestMatch = null;
          $highestSimilarity = 0;
  
          foreach ($teacherList as $teacher) {
              $cleanTeacherName = preg_replace('/^(Prof\. dr\.|Lect\. dr\.|Conf\. dr\.|Dr\.|Ing\.|Asist\. univ\.|Acad\.)\s*/', '', $teacher);
  
              similar_text($emailName, $cleanTeacherName, $similarity);
  
              if ($similarity > $highestSimilarity) {
                  $highestSimilarity = $similarity;
                  $bestMatch = $teacher;
              }
          }
  
          return $bestMatch ?: "No matching teacher found";
      } else {
          return "Format de email invalid.";
      }
  }
  
}
