<?php
$servername = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "myDB";

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $card_number = trim($_POST['card_number'] ?? '');
    $card_type = trim($_POST['card_type'] ?? '');
    $card_expiration = trim($_POST['card_expiration'] ?? '');
    $card_cvv = trim($_POST['card_cvv'] ?? '');
    $card_balance = trim($_POST['card_balance'] ?? '0');

    if ($username === '' || $card_number === '' || $card_type === '' || $card_expiration === '' || $card_cvv === '') {
        $message = 'All fields except balance are required.';
    } else {
        $conn = mysqli_connect($servername, $dbUser, $dbPass, $dbName);
        if (!$conn) {
            $message = 'DB connection error: ' . mysqli_connect_error();
        } else {
            $sql = "INSERT INTO CreditCardsTable (username, card_number, card_type, card_expiration, card_cvv, card_balance)
                    VALUES (?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                      card_number = VALUES(card_number),
                      card_type = VALUES(card_type),
                      card_expiration = VALUES(card_expiration),
                      card_cvv = VALUES(card_cvv),
                      card_balance = VALUES(card_balance)";

            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                // bind types: s = string, d = double
                mysqli_stmt_bind_param($stmt, "sssssd", $username, $card_number, $card_type, $card_expiration, $card_cvv, $card_balance);
                if (mysqli_stmt_execute($stmt)) {
                    $message = 'Credit card saved for user: ' . htmlspecialchars($username);
                } else {
                    $message = 'Insert error: ' . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            } else {
                $message = 'Prepare failed: ' . mysqli_error($conn);
            }
            mysqli_close($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Create Credit Card - My MAC172 Bank</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Create Credit Card</h1>
</header>

<section class="account-box">
    <h3>Assign a Credit Card to a User</h3>

    <?php if ($message !== ''): ?>
        <p><strong><?php echo htmlspecialchars($message); ?></strong></p>
    <?php endif; ?>

    <form method="post" action="createcreditcard.php">
        <div class="form-group">
            <label for="username">Username (must match users in DB or client):</label>
            <input type="text" id="username" name="username" required>
        </div>
        <br>
        <div class="form-group">
            <label for="card_number">Card Number</label>
            <input type="text" id="card_number" name="card_number" required placeholder="1234 5678 9012 3456">
        </div>
        <br>
        <div class="form-group">
            <label for="card_type">Card Type</label>
            <input type="text" id="card_type" name="card_type" required placeholder="Visa / MasterCard">
        </div>
        <br>
        <div class="form-group">
            <label for="card_expiration">Expiration Date</label>
            <input type="date" id="card_expiration" name="card_expiration" required>
        </div>
        <br>
        <div class="form-group">
            <label for="card_cvv">CVV</label>
            <input type="text" id="card_cvv" name="card_cvv" required>
        </div>
        <br>
        <div class="form-group">
            <label for="card_balance">Initial Balance</label>
            <input type="number" step="0.01" id="card_balance" name="card_balance" value="0.00">
        </div>
        <br>
        <div class="form-actions">
            <button type="submit" class="btn-submit">Create / Update Card</button>
        </div>
    </form>

    <p style="margin-top:12px;">
        <a href="creditcards.php">Back to Cards</a>
    </p>

</section>

</body>
</html>