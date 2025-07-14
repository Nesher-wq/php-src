<html>
<head><title>Book Library</title></head>
<body>

<h2>Book Library</h2>

<p><a href="index.php?action=session_demo">📋 View Session Demo</a></p>

<table>
<tbody>
<tr><td>Title</td><td>Author</td><td>Description</td></tr>
</tbody>
<?php
foreach ($books as $book) {
    print_r($book->title);
    echo '<tr><td><a href="index.php?book=' . $book->title . '">' . $book->title . '</a></td><td>' .
        $book->author . '</td><td>' . $book->description . '</td></tr>';
}
?>
</table>
</body>
</html>
