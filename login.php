<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
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
    <form id="loginForm" onsubmit="login(); return false;">
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
    <p style="text-align:center; margin-top:10px;">
        <a href="admin/admin_login.php">Admin Login</a>
    </p>
</div>

<p id="message"></p>
<script src="script.js"></script>
</body>
</html>
