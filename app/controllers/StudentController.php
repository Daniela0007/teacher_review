<?php
require_once __DIR__ . '/../models/StudentModel.php';
require_once __DIR__ . '/BaseController.php';

class StudentController extends BaseController {
    private $studentModel;

     public function __construct() {
      	parent::__construct();  
        $this->authorize('student');    
        $this->studentModel = new StudentModel();
        
    }
    
    public function index() {
       $filePath = __DIR__ . '/../utils/data.json';
       $data = [];
       if(file_exists($filePath)){
           $data = json_decode(file_get_contents($filePath), true);
       } else {
           $data = [];
       }
       include __DIR__ . '/../views/student.php';
    }

    public function save() {
       if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          $jsonPayload = file_get_contents('php://input');
          $data = json_decode($jsonPayload, true);

          $requiredFields = ['course', 'professor', 'year', 'negative_feedback', 'positive_feedback', 'feedback_answers','type'];

          foreach ($requiredFields as $field) {
              if (!isset($data[$field]) || empty($data[$field])) {
                http_response_code(400);
                echo json_encode(["error" => "Missing or empty required field: $field."]);
                return;
              }
          }

          if (!is_array($data['feedback_answers']) || empty($data['feedback_answers'])) {
            http_response_code(400);
            echo json_encode(["error" => "Feedback answers must be a non-empty array."]);
            return;
          }

          if ($this->studentModel->reviewExists($_SESSION['username'], $data['professor'], $data['course'], $data['type'])) {
            http_response_code(409); 
            echo json_encode(['error' => 'You have already submitted a review for this course and teacher.']);
            return;
          }
          
          try {
            $this->studentModel->saveFeedback($data, $_SESSION['username']);
            http_response_code(201);
            echo json_encode([
                "success" => true,
                "message" => "Feedback successfully saved.",
            ]);
          } catch (Exception $e) {
            http_response_code(500); 
            echo json_encode(["success" => false, "error" => $e->getMessage(), "mail"=> $_SESSION['username']]);
          }
      }
    }

    public function getQuestions() {
       if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $questions = $this->studentModel->getAllQuestions();
        if ($questions) {
          echo json_encode(['status' => 'success', 'questions' => $questions]);
          exit;
        } else {
          echo json_encode(['status' => 'error', 'message' => 'Failed to get questions']);
          exit;
        }
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
      exit;
    }
    }

    public function getFeedbacks() {
      try {
          if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $feedbacks = $this->studentModel->getFeedbacks($_SESSION['username']);
            http_response_code(200); 
            echo json_encode([
                'status' => 'success',
                'feedbacks' => $feedbacks ?: [] 
            ]);
            exit;
          } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid request method. Only GET requests are allowed.'
            ]);
            exit;
          }
        } catch (Exception $e) {
          http_response_code(500); 
          echo json_encode([
              'status' => 'error',
              'message' => 'An unexpected error occurred. Please try again later.',
              'details' => $e->getMessage() 
          ]);
          exit;
        }
    }

}
