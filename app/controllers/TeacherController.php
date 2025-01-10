<?php
require_once __DIR__ . '/../models/TeacherModel.php';
require_once __DIR__ . '/BaseController.php';

class TeacherController extends BaseController {
  private $teacherModel;
  public function __construct() {
    parent::__construct(); 
    $this->authorize('professor'); 
    $this->teacherModel = new TeacherModel();
  }

  public function index() {
    include __DIR__ . '/../views/teacher.php';
  }

  // public function getFeedbacks() {
  //   if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  //     $feedbacks = $this->teacherModel->getFeedbacks($_SESSION['username']);
  //     if ($feedbacks) {
  //       echo json_encode(['status' => 'success', 'feedbacks' => $feedbacks]);
  //       exit;
  //     } else {
  //       echo json_encode(['status' => 'error', 'message' => 'Failed to get feedbacks']);
  //       exit;
  //     }
  //   } else {
  //     echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
  //     exit;
  //   }
  // }

  public function getFeedbacks() {

    try{
      if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $type = $_GET['type'] ?? null; 
        $subject = $_GET['subject'] ?? null;

        $feedbacks = $this->teacherModel->getFeedbacksFilter($_SESSION['username'], $type, $subject);
        
        if (isset($feedbacks['error']) && $feedbacks['error'] === true) {
          throw new Exception($feedbacks['message']);
        }
        $this->sendResponse(['success' => true, 'feedbacks' => $feedbacks]);
      } else {
          echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
          exit;
      }
    } catch (Exception $e) {
        $this->sendResponse(['error' => $e->getMessage()], 400);
    }
  }

  public function getSubjects() {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      $subjects = $this->teacherModel->getSubjects($_SESSION['username']);
      if ($subjects) {
        echo json_encode(['status' => 'success', 'subjects' => $subjects]);
        exit;
      } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to get subjects']);
        exit;
      }
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
      exit;
    }
  }

  private function sendResponse($data, $statusCode = 200)
  {
      header('Content-Type: application/json');
      http_response_code($statusCode);
      echo json_encode($data);
      exit;
  }

}

?>  