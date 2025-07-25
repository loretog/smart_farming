<?php
session_start();
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_email = trim($_POST['username_email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username_email || !$password) {
        $error = 'All fields are required.';
    } else {
        $stmt = $conn->prepare('SELECT userID, username, password_hash FROM users WHERE username = ? OR email = ?');
        $stmt->bind_param('ss', $username_email, $username_email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($userID, $username, $password_hash);
            $stmt->fetch();
            if (password_verify($password, $password_hash)) {
                // Success: set session and redirect
                $_SESSION['userID'] = $userID;
                $_SESSION['username'] = $username;
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Invalid credentials.';
            }
        } else {
            $error = 'Invalid credentials.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Farming</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 400px; margin: 60px auto; background: #fff; padding: 2em; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; }
        form { display: flex; flex-direction: column; gap: 1em; }
        input[type=text], input[type=password] { padding: 0.75em; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #007bff; color: #fff; border: none; padding: 0.75em; border-radius: 4px; font-weight: bold; cursor: pointer; }
        button:hover { background: #0056b3; }
        .register-link { text-align: center; margin-top: 1em; }
        .error { color: #b30000; background: #ffe5e5; padding: 0.5em; border-radius: 4px; margin-bottom: 1em; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post" action="login.php">
            <input type="text" name="username_email" placeholder="Username or Email" required value="<?php echo htmlspecialchars($_POST['username_email'] ?? ''); ?>">
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <div class="register-link">
            Don't have an account? <a href="register.php">Register</a>
        </div>
    </div>
</body>
</html> 