<?php
// Start the session
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Session Demo</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .demo-box { background: #f0f8ff; border: 2px solid #4CAF50; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .nav { margin: 20px 0; }
        .nav a { margin-right: 15px; text-decoration: none; color: #2196F3; }
    </style>
</head>
<body>

<h2>PHP Session Demonstration</h2>

<div class="demo-box">
    <?php
    // Set session variables
    $_SESSION["favcolor"] = "green";
    $_SESSION["favanimal"] = "cat";
    echo "Session variables are set.";
    ?>
    
    <h3>Session Information:</h3>
    <p><strong>Session ID:</strong> <?php echo session_id(); ?></p>
    <p><strong>Favorite Color:</strong> <?php echo $_SESSION["favcolor"]; ?></p>
    <p><strong>Favorite Animal:</strong> <?php echo $_SESSION["favanimal"]; ?></p>
    
    <h3>All Session Data:</h3>
    <pre><?php print_r($_SESSION); ?></pre>
</div>

<div class="nav">
    <a href="index.php">← Back to Book List</a>
    <a href="index.php?action=session_demo">Refresh Session Demo</a>
</div>

</body>
</html>
