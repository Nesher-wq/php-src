<?php

function getBaseUrl() {
    $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
    
    $baseUrl = str_replace('\\', '/', $baseUrl);
    
    // Zorg dat het eindigt met een slash als het niet de root is
    if ($baseUrl !== '/' && !str_ends_with($baseUrl, '/')) {
        $baseUrl .= '/';
    }
    
    // Voor root deployment, gebruik absolute path
    if ($baseUrl === '/') {
        $baseUrl = '/';
    }
    
    return $baseUrl;
}

$baseUrl = getBaseUrl();

require_once 'connection.php';
require_once 'models/Article.php';
require_once 'controllers/ArticleController.php';

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$urlSegments = explode('/', trim($url, '/'));

$basePathRaw = dirname($_SERVER['SCRIPT_NAME']);
$basePathNormalized = str_replace('\\', '/', $basePathRaw);
$basePath = trim($basePathNormalized, '/');

// Only remove base path if it's not empty (not root)
if (!empty($basePath)) {
    $baseSegments = explode('/', $basePath);
    $urlSegments = array_slice($urlSegments, count($baseSegments));
}

// Use URL segments for action
$action = !empty($urlSegments[0]) ? $urlSegments[0] : 'index';

// Pass URL segments to controller
$controller = new ArticleController($urlSegments);

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