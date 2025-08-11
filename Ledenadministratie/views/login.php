<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen - Rol Applicatie</title>
    <style>
        body {
            font-family: -apple-system, system-ui, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .input-field {
            padding: 12px 16px;
            border: 2px solid #e1e5e9;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        .input-field:focus {
            outline: none;
            border-color: #4a90e2;
        }
        .input-field::placeholder {
            color: #a0a0a0;
        }
        .login-button {
            background-color: #4a90e2;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .login-button:hover {
            background-color: #357abd;
        }
        .login-button:active {
            background-color: #2968a3;
        }
        .message {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-weight: 500;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <?php if (isset($message)): ?>
            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <form class="login-form" method="POST" action="index.php">
            <input type="text" name="username" class="input-field" placeholder="Username" required>
            <input type="password" name="password" class="input-field" placeholder="Password" required>
            <button type="submit" class="login-button">Login</button>
            <?php if (!empty($login_error)): ?>
                <div class="message">
                    <?php echo htmlspecialchars($login_error); ?>
                </div>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
