<?php
session_start();
require 'config.php';

$message = '';
$savedCard = null;
$cardTypes = [
    'amex' => 'Amex',
    'mastercard' => 'Mastercard',
    'visa' => 'Visa',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $creditUsername = trim($_POST['username'] ?? '');
    $selectedCardType = $_POST['card_type'] ?? '';

    if ($creditUsername === '' || !array_key_exists($selectedCardType, $cardTypes)) {
        $message = 'Username and a valid card type are required.';
    } else {
        $card_type = $cardTypes[$selectedCardType];
        $cardGroups = [];
        for ($i = 0; $i < 4; $i++) {
            $cardGroups[] = (string) random_int(1000, 9999);
        }
        $card_number = implode(' ', $cardGroups);
        $card_expiration = (new DateTimeImmutable())->modify('+3 years')->format('Y-m-d');
        $card_cvv = (string) random_int(100, 999);
        $card_balance = 0.00;

        $conn = mysqli_connect($servername, $username, $password, $dbname);
        if (!$conn) {
            $message = 'DB connection error: ' . mysqli_connect_error();
        } else {
            $sql = "INSERT INTO CreditCardsTable (username, card_number, card_type, card_expiration, card_cvv, card_balance)
                    VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                // bind types: s = string, d = double
                mysqli_stmt_bind_param($stmt, "sssssd", $creditUsername, $card_number, $card_type, $card_expiration, $card_cvv, $card_balance);
                if (mysqli_stmt_execute($stmt)) {
                    $message = 'Credit card saved for user: ' . $creditUsername;
                    $savedCard = [
                        'number' => $card_number,
                        'type' => $card_type,
                        'expiration' => $card_expiration,
                        'cvv' => $card_cvv,
                        'balance' => $card_balance,
                    ];
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

    <?php if ($savedCard !== null): ?>
        <p><strong>Card Number:</strong> <?php echo htmlspecialchars($savedCard['number']); ?></p>
        <p><strong>Card Type:</strong> <?php echo htmlspecialchars($savedCard['type']); ?></p>
        <p><strong>Expiration:</strong> <?php echo htmlspecialchars($savedCard['expiration']); ?></p>
        <p><strong>CVV:</strong> <?php echo htmlspecialchars($savedCard['cvv']); ?></p>
        <p><strong>Balance:</strong> $<?php echo number_format($savedCard['balance'], 2); ?></p>
    <?php endif; ?>

    <form method="post" action="createcreditcard.php">
        <div class="form-group">
            <label for="username">Username (must match users in DB or client):</label>
            <input type="text" id="username" name="username" required>
        </div>
        <br>
        <div class="form-group">
            <label for="card_type">Card Type</label>
            <select id="card_type" name="card_type" required>
                <option value="">Select a card type</option>
                <option value="amex">Amex</option>
                <option value="mastercard">Mastercard</option>
                <option value="visa">Visa</option>
            </select>
        </div>
        <p>Card number, expiration date, CVV, and a $0.00 starting balance are generated automatically. Every card uses a 16-digit number and 3-digit CVV.</p>
        <br>
        <div class="form-actions">
            <button type="submit" class="btn-submit">Create Card</button>
        </div>
    </form>

    <p style="margin-top:12px;">
        <a href="creditcards.php">Back to Cards</a>
    </p>

</section>

</body>
</html>
