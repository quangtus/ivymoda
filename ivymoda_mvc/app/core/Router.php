<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\core\Router.php

class Router {
    private $routes = [];
    
    public function addRoute($route, $controller, $action) {
        $this->routes[$route] = ['controller' => $controller, 'action' => $action];
    }
    
    public function dispatch($url) {
        if (array_key_exists($url, $this->routes)) {
            $controller = $this->routes[$url]['controller'];
            $action = $this->routes[$url]['action'];
            
            require_once 'app/controllers/' . $controller . '.php';
            $controllerObj = new $controller();
            $controllerObj->$action();
        } else {
            // Không tìm thấy route
            http_response_code(404);
            require_once 'app/views/shared/404.php';
        }
    }
}