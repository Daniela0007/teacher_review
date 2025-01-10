<?php

spl_autoload_register(function($class) {
    $file = __DIR__ . '/../app/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

session_start();

$controller = isset($_GET['controller']) ? $_GET['controller'] : 'Login';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

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
