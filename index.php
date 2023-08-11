<?php

use Library\Http\NotFoundException;

try {

    ini_set('display_errors', 1);
    session_start();
    require 'helpers.php';

    // Autoloader
    spl_autoload_register(function ($className) {
        $fileName = str_replace('\\', '/', $className);
        require "src/$fileName.php";
    });

    //  if user try to access a page not authorized, he is redirected to the home page
    $route = $_SERVER['PATH_INFO'] ?? '/';

    $routes = require 'config/routes.php';

    // if route exist
    if (isset($routes[$route])) {
        // if route is a class
        list($controllerName, $method) = $routes[$route];
        $controller = new $controllerName();
        $controller->$method();
    } else {
        // if route is not a class
        throw new NotFoundException("");
    }

    // if route not exist
} catch (NotFoundException $e) {
    // Page 404
    header("HTTP/1.1 404 Not Found");
    require 'src/App/View/404.phtml';
    file_put_contents('logs/application.log', date('d/m/Y H:i:s')." : ".$e->getMessage()."\n", FILE_APPEND);
    exit();
} catch (Error $e) {
    // Error 500
    header("HTTP/1.1 500 Internal Server Error");
    require 'src/App/View/500.phtml';
    file_put_contents('logs/application.log', date('d/m/Y H:i:s')." : ".$e->getMessage()."\n", FILE_APPEND);
    exit();
} catch (Exception $e) {
    // Error 500
    header("HTTP/1.1 500 Internal Server Error");
    require 'src/App/View/500.phtml';
    file_put_contents('logs/application.log', date('d/m/Y H:i:s')." : ".$e->getMessage()."\n", FILE_APPEND);
    exit();
}
