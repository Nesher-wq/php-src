<!DOCTYPE html>
<html>
<head>
    <title>Articles</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { padding: 8px 15px; margin: 2px; text-decoration: none; border-radius: 4px; }
        .btn-edit { background-color: #007bff; color: white; }
        .btn-delete { background-color: #dc3545; color: white; }
        .btn-create { background-color: #28a745; color: white; display: inline-block; margin-bottom: 20px; }
        .btn:hover { opacity: 0.8; }
    </style>
</head>
<body>
    <h1>Articles</h1>
    
    <!-- ✅ Clean URL link -->
    <a href="<?php echo $baseUrl; ?>create" class="btn btn-create">Create New Article</a>
    
    <?php if (!empty($articles) && is_array($articles)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($article->id); ?></td>
                        <td><?php echo htmlspecialchars($article->title); ?></td>
                        <td><?php echo htmlspecialchars(substr($article->content, 0, 100)); ?></td>
                        <td>
                            <!-- ✅ Clean URLs -->
                            <a href="<?php echo $baseUrl; ?>edit/<?php echo $article->id; ?>" class="btn btn-edit">Edit</a>
                            <a href="<?php echo $baseUrl; ?>delete/<?php echo $article->id; ?>" class="btn btn-delete" 
                               onclick="return confirm('Are you sure you want to delete this article?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <p><strong>Total articles: <?php echo count($articles); ?></strong></p>
        
    <?php else: ?>
        <p>No articles found. <a href="<?php echo $baseUrl; ?>create" class="btn btn-create">Create the first article!</a></p>
    <?php endif; ?>
    
    <hr>
    <p><a href="<?php echo $baseUrl; ?>">← Refresh Articles List</a></p>
</body>
</html>