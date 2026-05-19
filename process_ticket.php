<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: submit_ticket.php");
    exit();
}

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$action = $_POST['action'] ?? '';
$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['is_admin'] ?? 0;

if ($action === 'create') {
    $subject = trim($_POST['subject'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = $_POST['priority'] ?? '';

    $valid_priorities = ['low', 'medium', 'high'];

    if (empty($subject) || strlen($subject) > 255 || empty($description) || !in_array($priority, $valid_priorities)) {
        mysqli_close($conn);
        header("Location: submit_ticket.php?error=1");
        exit();
    }

    $sql = "INSERT INTO tickets (user_id, subject, description, priority) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "isss", $user_id, $subject, $description, $priority);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_close($conn);
        header("Location: submit_ticket.php?success=1");
        exit();
    } else {
        mysqli_close($conn);
        header("Location: submit_ticket.php?error=1");
        exit();
    }

} elseif ($action === 'update_status') {
    if ($is_admin != 1) {
        mysqli_close($conn);
        header("Location: homepage.php");
        exit();
    }

    $ticket_id = filter_var($_POST['ticket_id'] ?? '', FILTER_VALIDATE_INT);
    $status = $_POST['status'] ?? '';

    $valid_statuses = ['open', 'in_progress', 'resolved', 'closed'];

    if (!$ticket_id || !in_array($status, $valid_statuses)) {
        mysqli_close($conn);
        header("Location: admin_see_ticket.php?error=1");
        exit();
    }

    $sql = "UPDATE tickets SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $status, $ticket_id);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_close($conn);
        header("Location: admin_see_ticket.php?success=1");
        exit();
    } else {
        mysqli_close($conn);
        header("Location: admin_see_ticket.php?error=1");
        exit();
    }

} elseif ($action === 'delete') {
    if ($is_admin != 1) {
        mysqli_close($conn);
        header("Location: homepage.php");
        exit();
    }

    $ticket_id = filter_var($_POST['ticket_id'] ?? '', FILTER_VALIDATE_INT);

    if (!$ticket_id) {
        mysqli_close($conn);
        header("Location: admin_see_ticket.php?error=1");
        exit();
    }

    $sql = "DELETE FROM tickets WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $ticket_id);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_close($conn);
        header("Location: admin_see_ticket.php?deleted=1");
        exit();
    } else {
        mysqli_close($conn);
        header("Location: admin_see_ticket.php?error=1");
        exit();
    }

} else {
    mysqli_close($conn);
    header("Location: homepage.php");
    exit();
}
?>