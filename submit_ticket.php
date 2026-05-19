<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT id, subject, status, priority, created_at FROM tickets WHERE user_id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$tickets = [];
while ($row = mysqli_fetch_assoc($result)) {
    $tickets[] = $row;
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Submit Ticket</title>
    <link rel="stylesheet" href="../../style.css">
</head>

<body>
    <header>
        <h1>My MAC172 Bank Online Banking</h1>
    </header>

    <section id="welcome">
        <h2>Submit a Support Ticket</h2>
    </section>

    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <p style="color: green; text-align: center;">Ticket submitted successfully!</p>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <p style="color: red; text-align: center;">There was an error submitting your ticket. Please try again.</p>
    <?php endif; ?>

    <div class="account-box">
        <h3>Report an Issue</h3>
        <form method="POST" action="process_ticket.php">
            <input type="hidden" name="action" value="create">

            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" required>
            </div>
            <br>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="5" required></textarea>
            </div>
            <br>
            <div class="form-group">
                <label for="priority">Priority</label>
                <select id="priority" name="priority">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            <br>
            <button type="submit">Submit Ticket</button>
        </form>
    </div>

    <div class="account-box">
        <h3>Your Tickets</h3>
        <?php if (empty($tickets)): ?>
            <p>You have not submitted any tickets yet.</p>
        <?php else: ?>
            <table border="1" cellpadding="8" cellspacing="0" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Ticket #</th>
                        <th>Subject</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Submitted On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td>
                                <?php echo $ticket['id']; ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($ticket['subject']); ?>
                            </td>
                            <td>
                                <?php echo ucfirst($ticket['priority']); ?>
                            </td>
                            <td>
                                <?php echo ucfirst(str_replace('_', ' ', $ticket['status'])); ?>
                            </td>
                            <td>
                                <?php echo $ticket['created_at']; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</body>

</html>