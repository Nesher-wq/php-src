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

    // ✅ FIXED: Return Article objects instead of stdClass
    public static function all() {
        $database = new Connection();
        $db = $database->getConnection();
        
        $query = "SELECT * FROM articles ORDER BY id DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $articles = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $article = new Article($row['title'], $row['content']);
            $article->id = $row['id'];
            $articles[] = $article;
        }
        
        return $articles;
    }

    // ✅ FIXED: Return Article object instead of stdClass
    public static function find($id) {
        $database = new Connection();
        $db = $database->getConnection();
        
        $query = "SELECT * FROM articles WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $article = new Article($row['title'], $row['content']);
            $article->id = $row['id'];
            return $article;
        }
        
        return null;
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

    // ✅ ADD: Update method
    public function update() {
        $database = new Connection();
        $db = $database->getConnection();
        
        $query = "UPDATE articles SET title = :title, content = :content WHERE id = :id";
        $stmt = $db->prepare($query);

        // Sanitize input
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->content = htmlspecialchars(strip_tags($this->content));

        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // ✅ ADD: Delete method
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