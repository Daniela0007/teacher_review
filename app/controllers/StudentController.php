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
                echo json_encode(["error" => "Câmp obligatoriu lipsă sau gol: $field."]);
                return;
              }
          }

          if (!is_array($data['feedback_answers']) || empty($data['feedback_answers'])) {
            http_response_code(400);
            echo json_encode(["error" => "Răspunsurile feedback-ului trebuie să fie un array nevid."]);
            return;
          }

          $data['negative_feedback'] = htmlspecialchars(strip_tags($data['negative_feedback']));
          $data['positive_feedback'] = htmlspecialchars(strip_tags($data['positive_feedback']));

          if ($this->studentModel->reviewExists($_SESSION['username'], $data['professor'], $data['course'], $data['type'])) {
            http_response_code(409); 
            echo json_encode(['error' => 'Ai trimis deja o recenzie pentru această materie și acest profesor.']);
            return;
          }
          
          try {
            $this->studentModel->saveFeedback($data, $_SESSION['username']);
            http_response_code(201);
            echo json_encode([
                "success" => true,
                "message" => "Feedback salvat cu succes",
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

    // public function getFeedbacks() {
    //   try {
    //       if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    //         $feedbacks = $this->studentModel->getFeedbacks($_SESSION['username']);
    //         http_response_code(200); 
    //         echo json_encode([
    //             'status' => 'success',
    //             'feedbacks' => $feedbacks ?: [] 
    //         ]);
    //         exit;
    //       } else {
    //         http_response_code(405); 
    //         echo json_encode([
    //             'status' => 'error',
    //             'message' => 'Metodă de cerere invalidă. Sunt permise doar cereri de tip GET.'
    //         ]);
    //         exit;
    //       }
    //     } catch (Exception $e) {
    //       http_response_code(500); 
    //       echo json_encode([
    //           'status' => 'error',
    //           'message' => 'A apărut o eroare neașteptată. Te rugăm să încerci din nou mai târziu.',
    //           'details' => $e->getMessage() 
    //       ]);
    //       exit;
    //     }
    // }

    public function getLastReviews() {
      try {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
          $feedbacks = $this->studentModel->getLastReviews($_SESSION['username']);
          http_response_code(200); 
          echo json_encode([
              'status' => 'success',
              'feedbacks' => $feedbacks ?: [] 
          ]);
          exit;
        } else {
          http_response_code(405); 
          echo json_encode([
              'status' => 'error',
              'message' => 'Metodă de cerere invalidă. Sunt permise doar cereri de tip GET.'
          ]);
          exit;
        }
      } catch (Exception $e) {
        http_response_code(500); 
        echo json_encode([
            'status' => 'error',
            'message' => 'A apărut o eroare neașteptată. Te rugăm să încerci din nou mai târziu.',
            'details' => $e->getMessage() 
        ]);
        exit;
      }
    }

}
