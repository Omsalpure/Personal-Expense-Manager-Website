<?php 
include('db.php');
if (isset($_POST['register'])) {
    $user = sanitize($conn, $_POST['username']);
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    $check = $conn->query("SELECT * FROM users WHERE username='$user'");
    if ($check->num_rows > 0) {
        $error = "Username already exists!";
    } else {
        $conn->query("INSERT INTO users (username, password) VALUES ('$user', '$pass')");
        header("Location: login.php");
    }
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<body>
    <div class="auth-container card">
        <h2>Create Account</h2>
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
            <button type="submit" name="register" class="btn-primary" style="width:100%">Sign Up</button>
        </form>
        <p>Joined already? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>