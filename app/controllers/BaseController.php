<?php

class BaseController {
    public function __construct() {
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    protected function isAuthenticated() {
        return isset($_SESSION['username']);
    }

    protected function authorize($requiredRole) {
        session_start();
        if (!$this->isAuthenticated() || $_SESSION['role'] !== $requiredRole) {
            header("Location: ?controller=Login&action=index"); 
            exit();
        }
    }
}
