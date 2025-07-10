<?php
require_once 'models/Article.php';

class ArticleController {

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
                header('Location: ?action=index');
                exit;
            } else {
                $error = "Failed to create article";
            }
        }
        
        include 'views/article/create.php';
    }

    public function edit() {
        global $baseUrl;
        $id = $_GET['id'] ?? null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $article = Article::find($id);
            if ($article) {
                $article->title = $_POST['title'];
                $article->content = $_POST['content'];
                $article->id = $id;
                
                if ($article->update()) {
                    header('Location: ?action=index');
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
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            $article = Article::find($id);
            if ($article) {
                $article->id = $id;
                $article->delete();
            }
        }
        
        header('Location: ?action=index');
        exit;
    }
}
?>