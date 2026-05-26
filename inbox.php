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

// Handle status/priority update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'])) {
    $ticket_id = (int) $_POST['ticket_id'];
    $new_status = $_POST['status'] ?? '';
    $new_priority = $_POST['priority'] ?? '';

    $allowed_statuses = ['open', 'in_progress', 'closed'];
    $allowed_priorities = ['low', 'medium', 'high'];

    if (in_array($new_status, $allowed_statuses) && in_array($new_priority, $allowed_priorities)) {
        $stmt = $conn->prepare("UPDATE tickets SET status = ?, priority = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ssi", $new_status, $new_priority, $ticket_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: inbox.php");
    exit;
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

// All real tickets (non-chat rows)
$tickets_sql = "
    SELECT t.id, u.username, t.subject, t.description, t.status, t.priority, t.created_at, t.updated_at
    FROM tickets t
    JOIN users u ON u.id = t.user_id
    WHERE t.message IS NULL OR t.message = ''
    ORDER BY t.created_at DESC
";
$tickets_result = mysqli_query($conn, $tickets_sql);
$tickets = [];
while ($row = mysqli_fetch_assoc($tickets_result)) {
    $tickets[] = $row;
}
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

    <div>
        <h3>All Tickets</h3>
        <?php if (count($tickets) === 0): ?>
            <p>No tickets yet.</p>
        <?php else: ?>

            <!-- One form per ticket, placed outside the table -->
            <?php foreach ($tickets as $t): ?>
                <form id="ticket-form-<?= (int) $t['id'] ?>" method="POST" style="display:none;">
                    <input type="hidden" name="ticket_id" value="<?= (int) $t['id'] ?>">
                </form>
            <?php endforeach; ?>

            <div style="max-height: 400px; overflow-y: auto;">
                <table border="1" cellpadding="8" cellspacing="0" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Subject</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $t):
                            $fid = "ticket-form-" . (int) $t['id'];
                            ?>
                            <tr>
                                <td><?= (int) $t['id'] ?></td>
                                <td><?= htmlspecialchars($t['username']) ?></td>
                                <td><?= htmlspecialchars($t['subject']) ?></td>
                                <td><?= htmlspecialchars($t['description']) ?></td>
                                <td>
                                    <select form="<?= $fid ?>" name="status">
                                        <?php foreach (['open', 'in_progress', 'closed'] as $s): ?>
                                            <option value="<?= $s ?>" <?= $t['status'] === $s ? 'selected' : '' ?>>
                                                <?= $s ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <select form="<?= $fid ?>" name="priority">
                                        <?php foreach (['low', 'medium', 'high'] as $p): ?>
                                            <option value="<?= $p ?>" <?= $t['priority'] === $p ? 'selected' : '' ?>>
                                                <?= $p ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><?= $t['created_at'] ?></td>
                                <td><?= $t['updated_at'] ?></td>
                                <td><button form="<?= $fid ?>" type="submit">Save</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>


    <div id="quick-actions">
        <a href="homepage.php">Go back to Home page</a>
    </div>
</body>

</html>