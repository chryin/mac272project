<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// One row per user who has any chat messages
$sql = "
    SELECT u.id, u.username, MAX(t.created_at) AS last_message_at
    FROM tickets t
    JOIN users u ON u.id = t.user_id
    WHERE t.message IS NOT NULL AND t.message != ''
    GROUP BY u.id, u.username
    ORDER BY last_message_at DESC
";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Inbox</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1>My MAC172 Bank Online Banking</h1>
    </header>

    <section id="welcome">
        <h2>Inbox</h2>
    </section>

    <div class="account-box">
        <h3>Conversations</h3>
        <?php if (mysqli_num_rows($result) === 0): ?>
            <p>No messages yet.</p>
        <?php else: ?>
            <table border="1" cellpadding="8" cellspacing="0" style="width: 100%;">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Last Message</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($row['username']) ?>
                            </td>
                            <td>
                                <?= $row['last_message_at'] ?>
                            </td>
                            <td><a href="chat_page.php?user_id=<?= (int) $row['id'] ?>">Open</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div id="quick-actions">
        <a href="homepage.php">Go back to Home page</a>
    </div>
</body>

</html>