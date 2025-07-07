<?php

$requestUri = $_SERVER['REQUEST_URI'];
$phpSelf = $_SERVER['PHP_SELF'];
$scriptName = $_SERVER['SCRIPT_NAME'];

// ✅ FIXED: Add trailing slash
function getBaseUrl() {
    $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
    
    // Zorg dat het eindigt met een slash als het niet de root is
    if ($baseUrl !== '/' && !str_ends_with($baseUrl, '/')) {
        $baseUrl .= '/';
    }
    
    // Als het de root is, gebruik lege string
    if ($baseUrl === '/') {
        $baseUrl = '';
    }
    
    return $baseUrl;
}

$baseUrl = getBaseUrl();

require_once 'connection.php';
require_once 'models/Article.php';
require_once 'controllers/ArticleController.php';

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