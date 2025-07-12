<!DOCTYPE html>
<html>
<head>
    <title>Create Article</title>
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
            box-sizing: border-box;
        }
        textarea { height: 120px; resize: vertical; }
        input[type="submit"] { 
            background-color: #28a745; 
            color: white; 
            padding: 12px 20px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
        }
        .btn-back { 
            background-color: #6c757d; 
            color: white; 
            padding: 12px 15px; 
            text-decoration: none; 
            border-radius: 4px; 
            margin-left: 10px; 
        }
        .error { 
            color: red; 
            margin-bottom: 20px; 
        }
    </style>
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