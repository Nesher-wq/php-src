<!DOCTYPE html>
<html>
<head>
    <title>Edit Article</title>

</head>
<body>
    <h1>Edit Article</h1>
    
    <?php if (isset($error)): ?>
        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($article) && $article): ?>
        <form method="post" action="<?php echo $baseUrl; ?>edit/<?php echo $article->id; ?>">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" placeholder="Enter article title" required 
                       value="<?php echo htmlspecialchars($article->title); ?>">
            </div>
            
            <div class="form-group">
                <label for="content">Content:</label>
                <textarea id="content" name="content" placeholder="Enter article content" required><?php echo htmlspecialchars($article->content); ?></textarea>
            </div>
            
            <div>
                <input type="submit" value="Update Article">
                <a href="<?php echo $baseUrl; ?>" class="btn-back">Cancel</a>
            </div>
        </form>
    <?php else: ?>
        <div class="error">
            Article not found!
        </div>
        <p><a href="<?php echo $baseUrl; ?>" class="btn-back">← Back to Articles List</a></p>
    <?php endif; ?>
    
    <hr>
    <p><a href="<?php echo $baseUrl; ?>">← Back to Articles List</a></p>
</body>
</html>