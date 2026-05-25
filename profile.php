<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$profile = null;
$profileError = '';
$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    $profileError = 'Unable to load your profile right now.';
} else {
    $sql = "SELECT first_name, last_name, email, username, address FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $profile = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$profile) {
            $profileError = 'Profile information could not be found.';
        }
    } else {
        $profileError = 'Unable to load your profile right now.';
    }

    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Your Profile - My MAC172 Bank</title>
    <link rel="stylesheet" href="style.css">

</head>

<body>

    <header>
        <h1>My MAC172 Bank Online Banking</h1>

        <nav>
            <ul>
            <li><a href="homepage.php">Home</a></li>
                <li><a href="bankaccounts.php">Accounts</a></li>
                <li><a href="sendmoney.php">Zelle2.0</a></li>
                <li><a href="creditcards.php">Credit Cards</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="aboutpage.php">About</a></li>
                <li><a href="contactpage.php">Contact</a></li>
                <li><a href="termsofservicepage.php">Terms of Service</a></li>
            </ul>
        </nav>
    </header>

    <section id="welcome">
        <h2>Your Profile</h2>
        <p>Review and manage your personal information.</p>
    </section>

    <section id="overview">
        <h2>Profile Details</h2>

        <div class="account-box">
            <h3>Personal Information</h3>

            <?php if ($profileError !== ''): ?>
                <p style="color: red;"><?php echo htmlspecialchars($profileError); ?></p>
            <?php else: ?>
                <p><strong>First Name:</strong> <span id="profileFirstName"><?php echo htmlspecialchars($profile['first_name']); ?></span></p>
                <p><strong>Last Name:</strong> <span id="profileLastName"><?php echo htmlspecialchars($profile['last_name']); ?></span></p>
                <p><strong>Email:</strong> <span id="profileEmail"><?php echo htmlspecialchars($profile['email']); ?></span></p>
                <p><strong>Username:</strong> <span id="profileUsername"><?php echo htmlspecialchars($profile['username']); ?></span></p>
                <p><strong>Address:</strong> <span id="profileAddress"><?php echo htmlspecialchars($profile['address'] ?: 'No address provided'); ?></span></p>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        <p>2025 MAC172 Online Banking. All Rights Reserved.</p>
        <p>
            <a href="aboutpage.html">About</a> |
            <a href="contactpage.html">Contact</a> |
            <a href="termsofservicepage.html">Terms of Service</a>
        </p>
    </footer>

</body>

</html>
