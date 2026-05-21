<?php
session_start();
require 'db_setup.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="style.css">

    <title>My Bank MAC172 Home</title>

</head>

<body>

    <header>
        <h1>My MAC172 Bank Online Banking</h1>

        <nav>
            <ul>
                <li><a href="homepage.php">Home</a></li>
                <li><a href="bankaccounts.html">Accounts</a></li>
                <li><a href="sendmoney.html">Zelle2.0</a></li>
                <li><a href="creditcards.html">Credit Cards</a></li>
                <li><a href="profile.html">Profile</a></li>
                <li><a href="aboutpage.html">About</a></li>
                <li><a href="contactpage.html">Contact</a></li>
                <li><a href="termsofservicepage.html">Terms of Service</a></li>
                <li><a href="?logout=1"><button>Logout</button></a></li>

            </ul>
        </nav>
    </header>

    <section id="welcome">
        <h2>Welcome Back to My Bank MAC172</h2>
        <p>Securely manage your accounts, transfer money, and view your financial information.</p>

        <div id="quick-actions">
            <a href="bankaccounts.html">View Accounts</a>
            <a href="sendmoney.html">Transfer Funds</a>
            <a href="sendmoney.html">Send Money</a>
            <A href="submit_ticket.php">Create ticket</A>


        </div>
    </section>


    <section id="overview">
        <h2>Your Account Overview</h2>

        <div class="account-box">
            <h3>Checking Account</h3>
            <p><strong>Balance:</strong> $<span id="homeCheckingBalance">0.00</span></p>
            <a href="bankaccounts.html">View Details</a>
        </div>

        <div class="account-box">
            <h3>Savings Account</h3>
            <p><strong>Balance:</strong> $<span id="homeSavingsBalance">0.00</span></p>
            <a href="bankaccounts.html">View Details</a>
        </div>
    </section>


    <section id="services">
        <h2>Online Banking Services</h2>
        <ul>
            <li> Secure login system</li>
            <li> View personal banking information</li>
            <li> Check balances and account details</li>
            <li> Transfer funds between accounts</li>
            <li> Send money to external accounts</li>
            <li> Schedule future transfers</li>
        </ul>
    </section>


    <footer>
        <p>2025 MAC172 Online Banking. All Rights Reserved.</p>
        <p><a href="aboutpage.html">About</a> | <a href="contactpage.html">Contact</a> | <a
                href="termsofservicepage.html">Terms of Service</a></p>
    </footer>
</body>

</html>