<!DOCTYPE html>
<html>

<head>
    <title>Your Profile - My MAC172 Bank</title>
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
        <h2>Your Profile</h2>
        <p>Review and manage your personal information.</p>
    </section>

    <section id="overview">
        <h2>Profile Details</h2>

        <div class="account-box">
            <h3>Personal Information</h3>

            <p><strong>First Name:</strong> <span id="profileFirstName"></span></p>
            <p><strong>Last Name:</strong> <span id="profileLastName"></span></p>
            <p><strong>Email:</strong> <span id="profileEmail"></span></p>
            <p><strong>Username:</strong> <span id="profileUsername"></span></p>
            <p><strong>Address:</strong> <span id="profileAddress"></span></p>
            <script src=script.js"></script>
            <script src="profile.js"></script>
        </div>
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