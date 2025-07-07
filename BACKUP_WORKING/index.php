<?php

$requestUri = $_SERVER['REQUEST_URI'];
$phpSelf = $_SERVER['PHP_SELF'];
$scriptName = $_SERVER['SCRIPT_NAME'];

// Helper functie voor URLs
function getBaseUrl() {
    return dirname($_SERVER['SCRIPT_NAME']);
}

// Maak base URL beschikbaar voor views
$baseUrl = getBaseUrl();

require_once 'connection.php';
require_once 'models/Article.php';
require_once 'controllers/ArticleController.php';

// Gebruik $_GET['action'] voor routing
$action = $_GET['action'] ?? 'index';

$controller = new ArticleController();

switch ($action) {
    case 'index':
    case '':
        $controller->index();
        break;
    case 'create':
        $controller->create();
        break;
    case 'edit':
        $controller->edit();
        break;
    case 'delete':
        $controller->delete();
        break;
    default:
        header('HTTP/1.0 404 Not Found');
        echo 'Pagina niet gevonden';
        break;
}
?>