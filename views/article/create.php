<!DOCTYPE html>
<html>
<head>
    <title>Create Article</title>

</head>
<body>
    <h1>Create New Article</h1>
    
    <?php if (isset($error)): ?>
        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <!-- ✅ Form POST naar hetzelfde Clean URL -->
    <form method="post" action="<?php echo $baseUrl; ?>create">
        <div>
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" placeholder="Enter article title" required>
        </div>
        
        <div>
            <label for="content">Content:</label>
            <textarea id="content" name="content" placeholder="Enter article content" required></textarea>
        </div>
        
        <div>
            <input type="submit" value="Create Article">
            <a href="<?php echo $baseUrl; ?>" class="btn-back">Cancel</a>
        </div>
    </form>
    
    <hr>
    <p><a href="<?php echo $baseUrl; ?>">← Back to Articles List</a></p>
</body>
</html>