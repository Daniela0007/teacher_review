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

        if ($role === 'student') {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'student';
            echo json_encode(['status' => 'success', 'redirect_url' => '?controller=Student&action=index']);
            exit;
        } elseif ($role === 'admin') {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'admin';
            echo json_encode(['status' => 'success', 'redirect_url' => '?controller=Admin&action=index']);
            exit;
        } elseif ($role === 'professor') {
            $_SESSION['username'] = $this->findTeacherName($username);
            $_SESSION['role'] = 'professor';
            echo json_encode(['status' => 'success', 'redirect_url' => '?controller=Teacher&action=index']);
            exit;
        } else {
            $_SESSION['username'] = '';
            echo json_encode(['status' => 'error', 'message' => 'Invalid username or password.']);
            exit;
        }
    }

    public function logout() {
        session_start();
        session_unset();
        session_destroy();

        header('Location: ?controller=Login&action=index');
        exit;
    }

    private function findTeacherName($teacher_mail) {
        $jsonFile = __DIR__ . '/../utils/professors.json'; 
        $teacherList = json_decode(file_get_contents($jsonFile), true);
        if (preg_match('/^([a-zA-Z]+)\.([a-zA-Z]+)@/', $teacher_mail, $matches)) {
            $firstName = ucfirst(strtolower($matches[2]));
            $lastName = ucfirst(strtolower($matches[1]));
            $bestMatch = null;
            $highestSimilarity = 0;

            foreach ($teacherList as $teacher) {
                similar_text("$firstName $lastName", $teacher, $similarity);
                if ($similarity > $highestSimilarity) {
                    $highestSimilarity = $similarity;
                    $bestMatch = $teacher;
                }
            }
            return $bestMatch ?: "No matching teacher found";
        } else {
            return "Invalid email format";
        }
    }
}
