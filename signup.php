<?php
session_start();
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $first_name = trim($_POST['firstName'] ?? '');
    $last_name = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $input_username = trim($_POST['username'] ?? '');
    $input_password = $_POST['password'] ?? '';
    $address = trim($_POST['address'] ?? '');

    if (empty($first_name) || empty($last_name) || empty($email) || empty($input_username) || empty($input_password)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $check_sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "ss", $input_username, $email);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_fetch_assoc($check_result)) {
            $error = "Username or email already exists.";
        } else {
            $sql = "INSERT INTO users (first_name, last_name, email, username, password, address) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssssss", $first_name, $last_name, $email, $input_username, $input_password, $address);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_close($conn);
                header("Location: login.php?signup=1");
                exit();
            } else {
                $error = "Error creating account. Please try again.";
            }
        }
    }

    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header>
        <h1>My MAC172 Bank Online Banking</h1>
    </header>

    <section id="welcome">
        <h2 style="text-align:center;">Sign Up</h2>
        <p style="text-align:center;">Create your My MAC172 Bank account</p>
    </section>

    <div class="account-box">
        <h3>Sign Up</h3>

        <form method="POST" action="signup.php">
            <div class="form-group">
                <label for="signupFirstName">First Name</label>
                <input type="text" id="signupFirstName" name="firstName" required placeholder="First Name">
            </div>
            <br>

            <div class="form-group">
                <label for="signupLastName">Last Name</label>
                <input type="text" id="signupLastName" name="lastName" required placeholder="Last Name">
            </div>
            <br>

            <div class="form-group">
                <label for="signupEmail">Email</label>
                <input type="email" id="signupEmail" name="email" required placeholder="Email">
            </div>
            <br>

            <div class="form-group">
                <label for="signupUsername">Username</label>
                <input type="text" id="signupUsername" name="username" required placeholder="Username">
            </div>
            <br>

            <div class="form-group">
                <label for="signupPassword">Password</label>
                <input type="password" id="signupPassword" name="password" required placeholder="Password">
            </div>
            <br>

            <div class="form-group">
                <label for="signupAddress">Address</label>
                <textarea id="signupAddress" name="address" placeholder="Address"></textarea>
            </div>
            <br>

            <button type="submit">Sign Up</button>
        </form>

        <?php if (!empty($error)): ?>
            <p sty/??e="color: red; text-align: center;">
                <?php echo $error; ?>
            </p>
        <?php endif; ?>

    </div>

</body>

</html>