<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$current_user_id = (int) $_SESSION['user_id'];
$is_admin = !empty($_SESSION['is_admin']);

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Admin can view any user's thread via ?user_id=X. Regular users only see their own.
$thread_user_id = ($is_admin && isset($_GET['user_id']))
    ? (int) $_GET['user_id']
    : $current_user_id;

// Handle send
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($_POST['message'] ?? ''))) {
    $message = trim($_POST['message']);
    if (strlen($message) <= 1500) {
        $is_admin_reply = $is_admin ? 1 : 0;
        $stmt = $conn->prepare("INSERT INTO tickets (user_id, subject, message, description, is_admin_reply) VALUES (?, ?, ?, ?, ?)");
        $subject = "Chat message";
        $description = "message";
        $stmt->bind_param("isssi", $thread_user_id, $subject, $message, $description, $is_admin_reply);
        $stmt->execute();
        $stmt->close();
    }
    $redirect = $is_admin ? "chat_page.php?user_id=" . $thread_user_id : "chat_page.php";
    header("Location: " . $redirect);
    exit;
}

// Fetch thread (only rows that actually have a chat message)
$stmt = $conn->prepare("
    SELECT message, created_at, is_admin_reply
    FROM tickets
    WHERE user_id = ? AND message IS NOT NULL AND message != ''
    ORDER BY created_at ASC
");
$stmt->bind_param("i", $thread_user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Chat</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: sans-serif;
            max-width: 600px;
            margin: 2em auto;
        }

        .chat-box {
            border: 1px solid #ccc;
            padding: 1em;
            height: 400px;
            overflow-y: auto;
            background: #fafafa;
        }

        .msg {
            margin-bottom: 0.8em;
            padding: 0.5em 0.8em;
            border-radius: 8px;
            max-width: 75%;
        }

        .mine {
            background: #d1e7ff;
            margin-left: auto;
            text-align: right;
        }

        .theirs {
            background: #eee;
        }

        .meta {
            font-size: 0.75em;
            color: #666;
            margin-top: 2px;
        }

        form {
            display: flex;
            gap: 0.5em;
            margin-top: 1em;
        }

        textarea {
            flex: 1;
            resize: none;
            padding: 0.5em;
        }

        button {
            padding: 0.5em 1.2em;
        }
    </style>
</head>

<body>

    <h2>Chat</h2>

    <div class="chat-box" id="chatBox">
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php
            // "mine" = sent by whoever is viewing
            $sent_by_admin = ((int) $row['is_admin_reply']) === 1;
            $is_mine = ($is_admin && $sent_by_admin) || (!$is_admin && !$sent_by_admin);
            $label = $sent_by_admin ? 'Admin' : 'User';
            ?>
            <div class="msg <?= $is_mine ? 'mine' : 'theirs' ?>">
                <div><?= nl2br(htmlspecialchars($row['message'])) ?></div>
                <div class="meta"><?= $label ?> · <?= $row['created_at'] ?></div>
            </div>
        <?php endwhile; ?>
    </div>

    <form method="POST">
        <textarea name="message" rows="2" maxlength="1500" placeholder="Type a message..." required></textarea>
        <button type="submit">Send</button>
    </form>

    <div id="quick-actions">
        <?php if ($is_admin): ?>
            <a href="inbox.php">Back to inbox</a>
        <?php else: ?>
            <a href="homepage.php">Go back to Home page</a>
            <a href="submit_ticket.php">Go back to tickets</a>
        <?php endif; ?>
    </div>

    <script>
        const box = document.getElementById('chatBox');
        box.scrollTop = box.scrollHeight;
    </script>

</body>

</html>