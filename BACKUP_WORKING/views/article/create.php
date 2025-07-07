<!DOCTYPE html>
<html>
<head>
    <title>Create Article</title>
</head>
<body>
    <h1>ULTRA SIMPLE FORM TEST</h1>
    
    <h2>Form will submit to: <?php echo $baseUrl; ?>?action=create</h2>
    
    <form method="post" action="<?php echo $baseUrl; ?>?action=create">
        <input type="text" name="title" value="Test Title">
        <textarea name="content">Test Content</textarea>
        <input type="submit" value="SUBMIT NOW">
    </form>
    
    <hr>
    <h3>Current debug:</h3>
    <p>Method: <?php echo $_SERVER['REQUEST_METHOD']; ?></p>
    <p>URL: <?php echo $_SERVER['REQUEST_URI']; ?></p>
    <p>BaseURL: <?php echo $baseUrl; ?></p>
    
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <h2 style="color: red;">POST DETECTED IN VIEW!</h2>
        <pre><?php print_r($_POST); ?></pre>
    <?php endif; ?>
</body>
</html>