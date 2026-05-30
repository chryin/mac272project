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
    $accountId = (int) ($_POST['account_id'] ?? 0);
    $amount = (float) ($_POST['amount'] ?? 0);
    $action = $_POST['action'] ?? '';
    $sql = "SELECT account_number FROM AccountsTable WHERE account_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $accountId, $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $sourceAccount = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($amount > 0 && $action === 'deposit') {
        $sql = "UPDATE AccountsTable SET balance = balance + ? WHERE account_id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "dii", $amount, $accountId, $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $sql = "INSERT INTO TransactionsTable (account_id, transaction_type, amount, description)
                    VALUES (?, 'deposit', ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            $description = 'Deposit to account ' . $sourceAccount['account_number'];
            mysqli_stmt_bind_param($stmt, "ids", $accountId, $amount, $description);
            mysqli_stmt_execute($stmt);
            $message = 'Deposit complete.';
        }
    }

    if ($amount > 0 && $action === 'withdraw') {
        $sql = "UPDATE AccountsTable
                SET balance = balance - ?
                WHERE account_id = ? AND user_id = ? AND account_type = 'checking' AND balance >= ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "diid", $amount, $accountId, $_SESSION['user_id'], $amount);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $sql = "INSERT INTO TransactionsTable (account_id, transaction_type, amount, description)
                    VALUES (?, 'withdrawal', ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            $description = 'Withdrawal from account ' . $sourceAccount['account_number'];
            mysqli_stmt_bind_param($stmt, "ids", $accountId, $amount, $description);
            mysqli_stmt_execute($stmt);
            $message = 'Withdrawal complete.';
        }
    }

    if ($amount > 0 && $action === 'transfer_to_checking') {
        $checkingAccountId = (int) ($_POST['checking_account_id'] ?? 0);
        $sql = "SELECT account_number FROM AccountsTable WHERE account_id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $checkingAccountId, $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $checkingAccount = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_begin_transaction($conn);

        $sql = "UPDATE AccountsTable
                SET balance = balance - ?
                WHERE account_id = ? AND user_id = ? AND account_type = 'savings' AND balance >= ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "diid", $amount, $accountId, $_SESSION['user_id'], $amount);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $sql = "UPDATE AccountsTable
                    SET balance = balance + ?
                    WHERE account_id = ? AND user_id = ? AND account_type = 'checking'";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "dii", $amount, $checkingAccountId, $_SESSION['user_id']);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $sql = "INSERT INTO TransactionsTable (account_id, transaction_type, amount, description)
                        VALUES (?, 'transfer_out', ?, ?), (?, 'transfer_in', ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                $fromDescription = 'Transfer from savings ' . $sourceAccount['account_number'] . ' to checking ' . $checkingAccount['account_number'];
                $toDescription = 'Transfer to checking ' . $checkingAccount['account_number'] . ' from savings ' . $sourceAccount['account_number'];
                mysqli_stmt_bind_param($stmt, "idsids", $accountId, $amount, $fromDescription, $checkingAccountId, $amount, $toDescription);
                mysqli_stmt_execute($stmt);
                mysqli_commit($conn);
                $message = 'Transfer to checking complete.';
            } else {
                mysqli_rollback($conn);
            }
        } else {
            mysqli_rollback($conn);
        }
    }
}

$sql = "SELECT account_id, account_type, account_number, balance, created_at
        FROM AccountsTable WHERE user_id = ? ORDER BY account_type, created_at";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($account = mysqli_fetch_assoc($result)) {
    $accounts[] = $account;
}

$checkingAccounts = array_filter($accounts, function ($account) {
    return $account['account_type'] === 'checking';
});

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
<html lang="en">

<head>
    <title>My MAC172 Bank — Accounts</title>
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
        <h2>Your Accounts</h2>
        <p>Deposit or manage funds in your checking and savings accounts below.</p>
        <div id="quick-actions">
            <a href="createbankaccount.php">Open New Account</a>
        </div>
    </section>

    <section id="overview">
        <h2>Account Overview</h2>

        <?php if ($message !== ''): ?>
            <p style="color: green; text-align: center;"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <?php if (empty($accounts)): ?>
            <div class="account-box">
                <p>You do not have any bank accounts yet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($accounts as $account): ?>
                <div class="account-box">
                    <h3><?php echo htmlspecialchars(ucfirst($account['account_type'])); ?> Account</h3>
                    <p>Account number: <?php echo htmlspecialchars($account['account_number']); ?></p>
                    <p><strong>Balance:</strong> $<?php echo number_format((float) $account['balance'], 2); ?></p>
                    <p><strong>Opened:</strong> <?php echo htmlspecialchars($account['created_at']); ?></p>

                    <form method="post" action="bankaccounts.php">
                        <input type="hidden" name="account_id" value="<?php echo htmlspecialchars($account['account_id']); ?>">
                        <label for="deposit-<?php echo htmlspecialchars($account['account_id']); ?>">Deposit Amount:</label>
                        <input type="number" id="deposit-<?php echo htmlspecialchars($account['account_id']); ?>" name="amount" min="0.01" step="0.01" required placeholder="Amount">
                        <button type="submit" name="action" value="deposit">Deposit</button>
                    </form>

                    <?php if ($account['account_type'] === 'checking'): ?>
                        <br>
                        <form method="post" action="bankaccounts.php">
                            <input type="hidden" name="account_id" value="<?php echo htmlspecialchars($account['account_id']); ?>">
                            <label>Withdraw Amount:</label>
                            <input type="number" name="amount" min="0.01" step="0.01" required placeholder="Amount">
                            <button type="submit" name="action" value="withdraw">Withdraw</button>
                        </form>
                    <?php elseif (!empty($checkingAccounts)): ?>
                        <br>
                        <form method="post" action="bankaccounts.php">
                            <input type="hidden" name="account_id" value="<?php echo htmlspecialchars($account['account_id']); ?>">
                            <label>Move Money to Checking:</label>
                            <input type="number" name="amount" min="0.01" step="0.01" required placeholder="Amount">
                            <select name="checking_account_id">
                                <?php foreach ($checkingAccounts as $checkingAccount): ?>
                                    <option value="<?php echo htmlspecialchars($checkingAccount['account_id']); ?>">
                                        <?php echo htmlspecialchars($checkingAccount['account_number']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="action" value="transfer_to_checking">Transfer</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
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

    <section id="services">
        <h2>Helpful Links</h2>
        <ul>
            <li><a href="profile.html">Update Profile</a></li>
            <li><a href="sendmoney.html">Schedule a Transfer</a></li>
            <li><a href="contactpage.html">Contact Support</a></li>
        </ul>
    </section>

    <footer>
        <p>2025 MAC172 Online Banking. All Rights Reserved.</p>
        <p><a href="aboutpage.html">About</a> | <a href="contactpage.html">Contact</a> | <a
                href="termsofservicepage.html">Terms of Service</a></p>
    </footer>

</body>

</html>
