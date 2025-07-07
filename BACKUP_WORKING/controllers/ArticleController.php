<?php
require_once 'models/Article.php';

class ArticleController {

    public function index() {
        global $baseUrl;  // ← Maak beschikbaar voor view
        $articles = Article::all();
        include 'views/article/index.php';
    }

    public function create() {
        global $baseUrl;
        
        // ✅ Debug: Laat altijd zien dat method wordt aangeroepen
        echo "<h4>CONTROLLER CREATE() METHOD CALLED</h4>";
        echo "Request method in controller: " . $_SERVER['REQUEST_METHOD'] . "<br>";
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo "<h4>POST PROCESSING STARTED</h4>";
            echo "POST data received:<br>";
            echo "Title: " . ($_POST['title'] ?? 'NOT SET') . "<br>";
            echo "Content: " . ($_POST['content'] ?? 'NOT SET') . "<br><br>";
            
            $article = new Article($_POST['title'], $_POST['content']);
            
            echo "Article object created:<br>";
            echo "Title: " . $article->title . "<br>";
            echo "Content: " . $article->content . "<br><br>";
            
            $result = $article->create();
            echo "Create result: " . ($result ? 'SUCCESS' : 'FAILED') . "<br><br>";
            
            if ($result) {
                echo "About to redirect...<br>";
                header('Location: ' . $baseUrl . '?action=index');
                exit;
            } else {
                $error = "Failed to create article";
                echo "Error: " . $error . "<br>";
            }
        } else {
            echo "<h4>SHOWING CREATE FORM (GET REQUEST)</h4>";
        }
        
        include 'views/article/create.php';
    }

    public function edit() {
        global $baseUrl;  // ← Maak beschikbaar voor view
        $id = $_GET['id'] ?? null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $article = Article::find($id);
            if ($article) {
                $article->title = $_POST['title'];
                $article->content = $_POST['content'];
                $article->id = $id;
                
                if ($article->update()) {
                    header('Location: ' . $baseUrl . '?action=index');
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
        global $baseUrl;  // ← Voor redirect
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            $article = Article::find($id);
            if ($article) {
                $article->id = $id;
                $article->delete();
            }
        }
        
        header('Location: ' . $baseUrl . '?action=index');
        exit;
    }
}
?>