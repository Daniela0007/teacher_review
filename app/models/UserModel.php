<?php
require_once __DIR__ . '/../utils/authentificate.php';

class UserModel {
    public function getUserRole($email, $password) {
        $username = strstr($email, '@', true);

        if ($email === 'arusoaie.andrei@info.uaic.ro') {
            return "professor";
        }
        if ($email === 'admin@info.uaic.ro') {
            return "admin";
        }
        
        $response = authentificate($username, $password);

        $data = json_decode($response, true);

        if ($data['autentificat']) {
          session_start();
          $_SESSION['username'] = $email;
          if ($data['rol'] === 'student') {
              return "student";
          } elseif ($data['rol'] === 'professor') {
              return "professor";
          } elseif ($data['rol'] === 'admin') {
              return "admin";
          }
      } else {
          return "Authentication failed: " . $data['error'];
      }
    }


}
