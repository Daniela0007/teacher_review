<?php
require_once __DIR__ . '/../models/AdminModel.php';
require_once __DIR__ . '/BaseController.php';

class AdminController extends BaseController {
  private $adminModel;

  public function __construct() {
    parent::__construct();  
    $this->authorize('admin');
    $this->adminModel = new AdminModel();
  }
  public function index() {
    include __DIR__ . '/../views/admin.php';
  }


  // public function getFeedbacks() {
  //     if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  //         $professorName = $_GET['professorName'] ?? null;
  
  //         if ($professorName) {
  //             $feedbacks = $this->adminModel->getFeedbacks($professorName);
  
  //             if (isset($feedbacks['error']) && $feedbacks['error']) {
  //                 echo json_encode([
  //                     'status' => 'error',
  //                     'message' => 'An error occurred while retrieving feedbacks: ' . $feedbacks['message']
  //                 ]);
  //                 exit;
  //             }
  
  //             if (empty($feedbacks)) {
  //                 // No feedbacks found
  //                 echo json_encode([
  //                     'status' => 'success',
  //                     'message' => 'No feedbacks available for this professor.',
  //                     'feedbacks' => []
  //                 ]);
  //                 exit;
  //             }
  
  //             // Return the feedbacks
  //             echo json_encode([
  //                 'status' => 'success',
  //                 'feedbacks' => $feedbacks
  //             ]);
  //             exit;
  //         } else {
  //             // Invalid or missing professor name
  //             echo json_encode([
  //                 'status' => 'error',
  //                 'message' => 'Professor name is required.'
  //             ]);
  //             exit;
  //         }
  //     } else {
  //         // Invalid request method
  //         echo json_encode([
  //             'status' => 'error',
  //             'message' => 'Invalid request method.'
  //         ]);
  //         exit;
  //     }
  // }


  public function getFeedbacks()
  {
    try{
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (!$data || !isset($data['year'], $data['course'], $data['professor'])) {
            $this->sendResponse(['error' => 'Invalid parameters provided'], 400);
            return;
        }

        $year = $data['year'];
        $course = $data['course'];
        $professor = $data['professor'];
        $type = $data['type'] ?? null; 

        $feedbacks = $this->adminModel->getFeedbacksFilter($year, $course, $professor, $type);

        if (isset($feedbacks['error']) && $feedbacks['error'] === true) {
          throw new Exception($feedbacks['message']);
        }

        $this->sendResponse(['success' => true, 'feedbacks' => $feedbacks]);
    } catch (Exception $e) {
        $this->sendResponse(['error' => $e->getMessage()], 400);
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