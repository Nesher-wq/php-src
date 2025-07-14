<?php
require_once 'models/Article.php';

class ArticleController {
    
    private $urlSegments;
    
    // Accept URL segments in constructor
    public function __construct($urlSegments = []) {
        $this->urlSegments = $urlSegments;
    }

    public function index() {
        global $baseUrl;
        $articles = Article::all();
        include 'views/article/index.php';
    }

    public function create() {
        global $baseUrl;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {            
            $article = new Article($_POST['title'], $_POST['content']);
            
            $result = $article->create();
            
            if ($result) {
                // Clean URL redirect
                header('Location: ' . $baseUrl);
                exit;
            } else {
                $error = "Failed to create article";
            }
        }
        
        include 'views/article/create.php';
    }

    public function edit() {
        global $baseUrl;
        
        // ✅ REQUIRED: Get ID from URL segments
        // URL: /edit/7 → $urlSegments = ['edit', '7']
        $id = !empty($this->urlSegments[1]) ? $this->urlSegments[1] : null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $article = Article::find($id);
            if ($article) {
                $article->title = $_POST['title'];
                $article->content = $_POST['content'];
                $article->id = $id;
                
                if ($article->update()) {
                    header('Location: ' . $baseUrl);
                    exit;
                } else {
                    $error = "Failed to update article";
                }
            }
        } else {
            $article = Article::find($id);
        }
        
        include 'views/article/edit.php';
    }

    public function delete() {
        global $baseUrl;
        
        // ✅ REQUIRED: Get ID from URL segments
        // URL: /delete/7 → $urlSegments = ['delete', '7']
        $id = !empty($this->urlSegments[1]) ? $this->urlSegments[1] : null;
        
        if ($id) {
            $article = Article::find($id);
            if ($article) {
                $article->id = $id;
                $article->delete();
            }
        }
        
        header('Location: ' . $baseUrl);
        exit;
    }
}
?>