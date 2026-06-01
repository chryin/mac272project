<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$accounts = [];
$transactions = [];
$message = '';
$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die('Database connection failed.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fromAccountId = (int) ($_POST['from_account_id'] ?? 0);
    $recipientAccountNumber = trim($_POST['recipient_account_number'] ?? '');
    $amount = (float) ($_POST['amount'] ?? 0);

    if ($amount > 0 && $recipientAccountNumber !== '') {
        mysqli_begin_transaction($conn);

        $sql = "SELECT account_id, account_number
                FROM AccountsTable
                WHERE account_id = ? AND user_id = ? AND balance >= ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iid", $fromAccountId, $_SESSION['user_id'], $amount);
        mysqli_stmt_execute($stmt);
        $fromAccount = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        $sql = "SELECT account_id, account_number
                FROM AccountsTable
                WHERE account_number = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $recipientAccountNumber);
        mysqli_stmt_execute($stmt);
        $toAccount = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($fromAccount && $toAccount && $fromAccount['account_id'] != $toAccount['account_id']) {
            $sql = "UPDATE AccountsTable SET balance = balance - ? WHERE account_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "di", $amount, $fromAccount['account_id']);
            mysqli_stmt_execute($stmt);

            $sql = "UPDATE AccountsTable SET balance = balance + ? WHERE account_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "di", $amount, $toAccount['account_id']);
            mysqli_stmt_execute($stmt);

            $sql = "INSERT INTO TransactionsTable (account_id, transaction_type, amount, description)
                    VALUES (?, 'transfer_out', ?, ?), (?, 'transfer_in', ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            $fromDescription = 'Sent money from account ' . $fromAccount['account_number'] . ' to account ' . $toAccount['account_number'];
            $toDescription = 'Received money from account ' . $fromAccount['account_number'] . ' to account ' . $toAccount['account_number'];
            mysqli_stmt_bind_param($stmt, "idsids", $fromAccount['account_id'], $amount, $fromDescription, $toAccount['account_id'], $amount, $toDescription);
            mysqli_stmt_execute($stmt);

            mysqli_commit($conn);
            $message = 'Money sent successfully.';
        } else {
            mysqli_rollback($conn);
            $message = 'Transfer could not be completed.';
        }
    }
}

$sql = "SELECT account_id, account_type, account_number, balance
        FROM AccountsTable
        WHERE user_id = ?
        ORDER BY account_type, created_at";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($account = mysqli_fetch_assoc($result)) {
    $accounts[] = $account;
}

$sql = "SELECT transactions.transaction_id, transactions.account_id, transactions.transaction_type,
               transactions.amount, transactions.description, transactions.created_at
        FROM TransactionsTable transactions
        INNER JOIN AccountsTable accounts ON accounts.account_id = transactions.account_id
        WHERE accounts.user_id = ?
        ORDER BY transactions.created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($transaction = mysqli_fetch_assoc($result)) {
    $transactions[] = $transaction;
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Send Money - My MAC172 Bank</title>
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
        <h2>Send Money</h2>
        <p>Transfer funds securely between bank accounts.</p>
    </section>

    <section id="overview">
        <div class="account-box">
            <h3>Send Money to an Account</h3>

            <?php if ($message !== ''): ?>
                <p style="text-align:center;"><strong><?php echo htmlspecialchars($message); ?></strong></p>
            <?php endif; ?>

            <form method="post" action="sendmoney.php">
                <div class="form-group">
                    <label for="from_account_id">From Account</label>
                    <select id="from_account_id" name="from_account_id" required>
                        <option value="">Select an account</option>
                        <?php foreach ($accounts as $account): ?>
                            <option value="<?php echo htmlspecialchars($account['account_id']); ?>">
                                <?php echo htmlspecialchars(ucfirst($account['account_type']) . ' ' . $account['account_number'] . ' - $' . number_format((float) $account['balance'], 2)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <br>

                <div class="form-group">
                    <label for="recipient_account_number">Recipient Account Number</label>
                    <input type="text" id="recipient_account_number" name="recipient_account_number" required placeholder="Enter account number">
                </div>
                <br>

                <div class="form-group">
                    <label for="amount">Amount ($)</label>
                    <input type="number" id="amount" name="amount" required min="0.01" step="0.01" placeholder="Enter amount">
                </div>
                <br>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Send Money</button>
                    <button type="reset" class="btn-reset">Reset</button>
                </div>
            </form>
        </div>
    </section>

    <section id="transactions">
        <h2>Transaction History</h2>

        <?php if (empty($transactions)): ?>
            <p style="text-align:center;">No transactions yet.</p>
        <?php else: ?>
            <table border="1" cellpadding="8" cellspacing="0" style="width: 95%; margin: 0 auto; background-color: white;">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Account ID</th>
                        <th>Transaction Type</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($transaction['transaction_id']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['account_id']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['transaction_type']); ?></td>
                            <td>$<?php echo number_format((float) $transaction['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
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
