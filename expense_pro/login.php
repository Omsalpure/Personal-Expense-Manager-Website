<?php 
include('db.php');
if (isset($_POST['login'])) {
    $user = sanitize($conn, $_POST['username']);
    $res = $conn->query("SELECT * FROM users WHERE username='$user'");
    $u = $res->fetch_assoc();

    if ($u && password_verify($_POST['password'], $u['password'])) {
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['username'] = $u['username'];
        header("Location: index.php");
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<body>
    <div class="auth-container card">
        <h2>Login</h2>
        <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="login" class="btn-primary" style="width:100%">Login</button>
        </form>
        <p>New user? <a href="register.php">Create account</a></p>
    </div>
</body>
</html>