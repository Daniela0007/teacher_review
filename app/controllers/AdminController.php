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


  public function getFeedbacks()
  {
    try{
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (!$data || !isset($data['year'], $data['course'], $data['professor'])) {
            $this->sendResponse(['error' => 'Parametri furnizați invalizi.'], 400);
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