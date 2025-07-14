<?php
include_once("model/model.php");

class Controller {
    public $model;
    public function __construct()
        {
        $this->model = new Model();
    }
    
public function invoke()
    {
    // Check if session demo is requested
    if (isset($_GET['action']) && $_GET['action'] == 'session_demo') {
        include 'view/session_demo.php';
        return;
    }
    
    if (!isset($_GET['book']))
        {
        // no special book is requested, we'll show a list of all available books
        $books = $this->model->getBookList();
        include 'view/booklist.php';
        }
    else
        {
        // show the requested book
        $book = $this->model->getBook($_GET['book']);
        include 'view/viewbook.php';
        }
    }
}
?>
