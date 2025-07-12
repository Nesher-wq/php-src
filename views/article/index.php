<!DOCTYPE html>
<html>
<head>
    <title>Articles</title>

</head>
<body>
    <h1>Articles</h1>
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