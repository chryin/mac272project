<?php
session_start();
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    $sql = "SELECT id, password, is_admin FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $input_username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if ($input_password === $row['password']) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['is_admin'] = $row['is_admin'];
            mysqli_close($conn);
            header("Location: homepage.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }

    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="../../style.css">
</head>

<body>
    <header>
        <h1>My MAC172 Bank Online Banking</h1>
    </header>

    <section id="welcome">
        <h2>Login</h2>
    </section>

    <div class="account-box">
        <h3>Login</h3>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="loginUsername">Username</label>
                <input type="text" id="loginUsername" name="username" required>
            </div>
            <br>
            <div class="form-group">
                <label for="loginPassword">Password</label>
                <input type="password" id="loginPassword" name="password" required>
            </div>
            <br>
            <button type="submit">Login</button>
        </form>

        <p style="text-align:center; margin-top:10px;">
            <a href="signup.php">Don't have an account? Sign Up</a>
        </p>

        <?php if (!empty($error)): ?>
            <p style="color: red; text-align: center;">
                <?php echo $error; ?>
            </p>
        <?php endif; ?>
    </div>

</body>

</html>