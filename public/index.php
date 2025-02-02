<?php

// spl_autoload_register(function($class) {
//     $file = __DIR__ . '/../app/' . str_replace('\\', '/', $class) . '.php';
//     if (file_exists($file)) {
//         require $file;
//     }
// });

// session_start();

// $controller = isset($_GET['controller']) ? $_GET['controller'] : 'Login';
// $action = isset($_GET['action']) ? $_GET['action'] : 'index';

// $controllerName = $controller . 'Controller'; 
// $controllerPath = '../app/controllers/' . $controllerName . '.php'; 


// if (file_exists($controllerPath)) {
//     require $controllerPath;
//     $ctrl = new $controllerName();
//     if (method_exists($ctrl, $action)) {
//         $ctrl->$action();
//     } else {
//         echo "Action not found!";
//     }
// } else {
//     echo "Controller not found!";
// }

spl_autoload_register(function($class) {
    $file = __DIR__ . '/../app/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

session_start();

$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$path = trim(str_replace($scriptName, '', $requestUri), '/');
$pathParts = explode('/', $path);

$controller = !empty($pathParts[0]) ? ucfirst($pathParts[0]) : 'Login';
$action = isset($pathParts[1]) ? $pathParts[1] : 'index';

if (isset($_GET['controller'])) {
    $controller = ucfirst($_GET['controller']);
}
if (isset($_GET['action'])) {
    $action = $_GET['action'];
}

$controllerName = $controller . 'Controller'; 
$controllerPath = '../app/controllers/' . $controllerName . '.php'; 

if (file_exists($controllerPath)) {
    require $controllerPath;
    $ctrl = new $controllerName();
    if (method_exists($ctrl, $action)) {
        $ctrl->$action();
    } else {
        echo "Action not found!";
    }
} else {
    echo "Controller not found!";
}

