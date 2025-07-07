<?php
require_once 'connection.php';

class Article {
    public $id;
    public $title;
    public $content;

    public function __construct($title = null, $content = null) {
        $this->title = $title;
        $this->content = $content;
    }

    public static function all() {
        $articles = [];
        $database = new Connection();
        $db = $database->getConnection();
        
        $req = $db->query('SELECT * FROM articles');
        $articles = $req->fetchAll(PDO::FETCH_OBJ);

        return $articles;
    }

    public static function find($id) {
        $database = new Connection();
        $db = $database->getConnection();
        
        $query = "SELECT * FROM articles WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function create() {
        $database = new Connection();
        $db = $database->getConnection();
        
        $query = "INSERT INTO articles (title, content) VALUES (:title, :content)";
        $stmt = $db->prepare($query);

        // Sanitize input
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->content = htmlspecialchars(strip_tags($this->content));

        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':content', $this->content);

        if ($stmt->execute()) {
            $this->id = $db->lastInsertId();
            return true;
        }
        return false;
    }

    public function update() {
        $database = new Connection();
        $db = $database->getConnection();
        
        $query = "UPDATE articles SET title = :title, content = :content WHERE id = :id";
        $stmt = $db->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->content = htmlspecialchars(strip_tags($this->content));

        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function delete() {
        $database = new Connection();
        $db = $database->getConnection();
        
        $query = "DELETE FROM articles WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}
?>