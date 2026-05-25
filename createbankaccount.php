<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = '';
$createdAccount = null;
$accountTypes = [
    'checking' => 'Checking',
    'savings' => 'Savings',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedAccountType = $_POST['account_type'] ?? '';
    $initialDepositInput = trim($_POST['initial_deposit'] ?? '');
    $initialDeposit = $initialDepositInput === '' ? 0.00 : filter_var($initialDepositInput, FILTER_VALIDATE_FLOAT);

    if (!array_key_exists($selectedAccountType, $accountTypes)) {
        $message = 'Select a valid account type.';
    } elseif ($initialDeposit === false || $initialDeposit < 0) {
        $message = 'Enter a valid deposit amount of zero or more.';
    } else {
        $conn = mysqli_connect($servername, $username, $password, $dbname);

        if (!$conn) {
            $message = 'DB connection error: ' . mysqli_connect_error();
        } else {
            do {
                $accountNumber = (string) random_int(1000000000, 9999999999);
                $numberSql = "SELECT account_id FROM AccountsTable WHERE account_number = ?";
                $numberStmt = mysqli_prepare($conn, $numberSql);
                mysqli_stmt_bind_param($numberStmt, "s", $accountNumber);
                mysqli_stmt_execute($numberStmt);
                $numberResult = mysqli_stmt_get_result($numberStmt);
                $numberExists = mysqli_fetch_assoc($numberResult);
                mysqli_stmt_close($numberStmt);
            } while ($numberExists);

            mysqli_begin_transaction($conn);

            $sql = "INSERT INTO AccountsTable (user_id, account_type, account_number, balance)
                    VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);

            if (!$stmt) {
                mysqli_rollback($conn);
                $message = 'Unable to prepare the account creation request.';
            } else {
                mysqli_stmt_bind_param($stmt, "issd", $_SESSION['user_id'], $selectedAccountType, $accountNumber, $initialDeposit);

                if (!mysqli_stmt_execute($stmt)) {
                    mysqli_rollback($conn);
                    $message = 'Unable to create the account: ' . mysqli_stmt_error($stmt);
                } else {
                    $accountId = mysqli_insert_id($conn);

                    if ($initialDeposit > 0) {
                        $transactionSql = "INSERT INTO TransactionsTable (account_id, transaction_type, amount, description)
                                           VALUES (?, 'deposit', ?, 'Initial deposit')";
                        $transactionStmt = mysqli_prepare($conn, $transactionSql);

                        if (!$transactionStmt) {
                            mysqli_rollback($conn);
                            $message = 'Unable to record the initial deposit.';
                        } else {
                            mysqli_stmt_bind_param($transactionStmt, "id", $accountId, $initialDeposit);

                            if (!mysqli_stmt_execute($transactionStmt)) {
                                mysqli_rollback($conn);
                                $message = 'Unable to record the initial deposit: ' . mysqli_stmt_error($transactionStmt);
                            }

                            mysqli_stmt_close($transactionStmt);
                        }
                    }

                    if ($message === '') {
                        mysqli_commit($conn);

                        $createdSql = "SELECT account_number, account_type, balance, created_at
                                       FROM AccountsTable WHERE account_id = ?";
                        $createdStmt = mysqli_prepare($conn, $createdSql);
                        mysqli_stmt_bind_param($createdStmt, "i", $accountId);
                        mysqli_stmt_execute($createdStmt);
                        $createdResult = mysqli_stmt_get_result($createdStmt);
                        $createdAccount = mysqli_fetch_assoc($createdResult);
                        mysqli_stmt_close($createdStmt);
                        $message = 'Bank account created successfully.';
                    }
                }

                mysqli_stmt_close($stmt);
            }

            mysqli_close($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Create Bank Account - My MAC172 Bank</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Create Bank Account</h1>
    </header>

    <section class="account-box">
        <h3>Open a New Account</h3>

        <?php if ($message !== ''): ?>
            <p><strong><?php echo htmlspecialchars($message); ?></strong></p>
        <?php endif; ?>

        <?php if ($createdAccount !== null): ?>
            <p><strong>Account Type:</strong> <?php echo htmlspecialchars(ucfirst($createdAccount['account_type'])); ?></p>
            <p><strong>Account Number:</strong> <?php echo htmlspecialchars($createdAccount['account_number']); ?></p>
            <p><strong>Balance:</strong> $<?php echo number_format((float) $createdAccount['balance'], 2); ?></p>
            <p><strong>Created At:</strong> <?php echo htmlspecialchars($createdAccount['created_at']); ?></p>
        <?php endif; ?>

        <form method="post" action="createbankaccount.php">
            <div class="form-group">
                <label for="account_type">Account Type</label>
                <select id="account_type" name="account_type" required>
                    <option value="">Select an account type</option>
                    <option value="checking">Checking</option>
                    <option value="savings">Savings</option>
                </select>
            </div>
            <br>
            <div class="form-group">
                <label for="initial_deposit">Initial Deposit</label>
                <input type="number" id="initial_deposit" name="initial_deposit" min="0" step="0.01" placeholder="0.00">
                <button type="submit" class="btn-submit">Deposit and Create Account</button>
            </div>
        </form>

        <p>The account number and created date are generated automatically.</p>
        <p><a href="bankaccounts.php">Back to Accounts</a></p>
    </section>
</body>
</html>
