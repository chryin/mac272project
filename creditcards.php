<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$cards = [];
$cardError = '';
$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    $cardError = 'Unable to load your cards right now.';
} else {
    $sql = "SELECT cards.card_id, cards.card_number, cards.card_type, cards.card_expiration,
                   cards.card_cvv, cards.card_balance
            FROM CreditCardsTable cards
            INNER JOIN users ON users.username = cards.username
            WHERE users.id = ?
            ORDER BY cards.card_id DESC";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($card = mysqli_fetch_assoc($result)) {
            $cards[] = $card;
        }

        mysqli_stmt_close($stmt);
    } else {
        $cardError = 'Unable to load your cards right now.';
    }

    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Credit & Debit Cards - My MAC172 Bank</title>
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
    <h2>Your Cards</h2>
    <p>Manage your credit and debit cards, view balances, and perform card actions.</p>

    <div id="quick-actions">
        <a href="bankaccounts.php">View Accounts</a>
        <a href="createcreditcard.php">Add New Card</a>
        <a href="contactpage.php">Contact Support</a>
    </div>
</section>

<section id="overview">
    <h2>Card Overview</h2>

    <?php if ($cardError !== ''): ?>
        <p style="color: red; text-align: center;"><?php echo htmlspecialchars($cardError); ?></p>
    <?php elseif (empty($cards)): ?>
        <div class="account-box">
            <p>You do not have any cards yet.</p>
        </div>
    <?php else: ?>
        <?php foreach ($cards as $card): ?>
            <div class="account-box">
                <h3><?php echo htmlspecialchars($card['card_type']); ?> Card</h3>
                <p>Card ID: <?php echo htmlspecialchars($card['card_id']); ?></p>
                <p>Card Number: <?php echo htmlspecialchars($card['card_number']); ?></p>
                <p>Expiration: <?php echo htmlspecialchars(date('m/y', strtotime($card['card_expiration']))); ?></p>
                <p>CVV: <?php echo htmlspecialchars($card['card_cvv']); ?></p>
                <p><strong>Balance:</strong> $<?php echo number_format((float) $card['card_balance'], 2); ?></p>
                <p><a href="sendmoney.php">Make a Payment</a></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<section id="services">
    <h2>Card Services</h2>
    <ul>
        <li>View recent card activity</li>
        <li>Make credit card payments</li>
        <li>Freeze/unfreeze debit card</li>
        <li>Report lost or stolen cards</li>
        <li>Set travel notifications</li>
    </ul>
</section>

<footer>
    <p>2025 MAC172 Online Banking. All Rights Reserved.</p>
    <p><a href="aboutpage.php">About</a> | <a href="contactpage.php">Contact</a> | <a href="termsofservicepage.php">Terms of Service</a></p>
</footer>
</body>
</html>
