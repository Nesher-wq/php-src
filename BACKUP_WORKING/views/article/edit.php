<!DOCTYPE html>
<html>
<head>
    <title>Edit Article</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        form { max-width: 500px; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input[type="text"], textarea { 
            width: 100%; 
            padding: 10px; 
            margin: 5px 0 15px 0; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
            font-family: Arial, sans-serif;
            box-sizing: border-box;
        }
        textarea { 
            height: 120px; 
            resize: vertical; 
        }
        input[type="submit"] { 
            background-color: #007bff; 
            color: white; 
            padding: 12px 20px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 16px;
        }
        input[type="submit"]:hover { 
            background-color: #0056b3; 
        }
        .btn-back { 
            background-color: #6c757d; 
            color: white; 
            padding: 12px 15px; 
            text-decoration: none; 
            border-radius: 4px; 
            margin-left: 10px; 
            display: inline-block;
        }
        .btn-back:hover { 
            background-color: #545b62; 
        }
        .error { 
            color: #dc3545; 
            background-color: #f8d7da; 
            border: 1px solid #f5c6cb; 
            padding: 10px; 
            border-radius: 4px; 
            margin-bottom: 20px; 
        }
        .form-group { 
            margin-bottom: 20px; 
        }
    </style>
</head>
<body>
    <h1>Edit Article</h1>
    
    <?php if (isset($error)): ?>
        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($article) && $article): ?>
        <form method="post" action="<?php echo $baseUrl; ?>/edit?id=<?php echo $article->id; ?>">
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
                <a href="<?php echo $baseUrl; ?>/index" class="btn-back">Cancel</a>
            </div>
        </form>
    <?php else: ?>
        <div class="error">
            Article not found!
        </div>
        <p><a href="<?php echo $baseUrl; ?>/index">← Back to Articles List</a></p>
    <?php endif; ?>
    
    <hr>
    <p><a href="<?php echo $baseUrl; ?>/index">← Back to Articles List</a></p>
</body>
</html>