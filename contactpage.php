<!DOCTYPE html>
<html>

<head>
    <title>Contact - My MAC172 Bank</title>
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
        <h2>Contact Us</h2>
        <p>Send us a message and we'll get back to you.</p>
    </section>

    <section id="overview">
        <div class="account-box">
            <form id="contactForm" action="#" method="post">
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" placeholder="First name" required>
                </div>

                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" placeholder="Last name" required>
                </div>

                <div class="form-group">
                    <label for="contactEmail">Email</label>
                    <input type="email" id="contactEmail" name="email" placeholder="you@example.com" required>
                </div>

                <div class="form-group">
                    <label for="contactMessage">Message</label>
                    <textarea id="contactMessage" name="message" placeholder="Write your message here..."
                        required></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Submit</button>
                    <button type="reset" class="btn-reset">Reset</button>
                </div>

                <p style="text-align:center; margin-top:10px;">
                    <a href="homepage.html">Return to Home</a>
                </p>
            </form>
        </div>
    </section>

    <footer>
        <p>2025 MAC172 Online Banking. All Rights Reserved.</p>
        <p><a href="aboutpage.html">About</a> | <a href="contactpage.html">Contact</a> | <a
                href="termsofservicepage.html">Terms of Service</a></p>
    </footer>
</body>

</html>