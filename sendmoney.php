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
        <p>Transfer funds securely to another user's email account.</p>
    </section>

    <section id="overview">
        <div class="account-box">
            <h3>Send Money to External Account</h3>

            <form id="sendMoneyForm" onsubmit="submitTransfer(); return false;">

                <div class="form-group">
                    <label for="senderEmail">Your Email</label>
                    <input type="email" id="senderEmail" name="senderEmail" required placeholder="you@example.com">
                </div>
                <br>

                <div class="form-group">
                    <label for="recipientEmail">Recipient Email</label>
                    <input type="email" id="recipientEmail" name="recipientEmail" required
                        placeholder="recipient@example.com">
                </div>
                <br>

                <div class="form-group">
                    <label for="amount">Amount ($)</label>
                    <input type="number" id="amount" name="amount" required min="1" placeholder="Enter amount">
                </div>
                <br>

                <div class="form-group">
                    <label for="transferDate">Transfer Date</label>
                    <input type="date" id="transferDate" name="transferDate" required>
                </div>
                <br>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Send Money</button>
                    <button type="reset" class="btn-reset">Reset</button>
                </div>

                <p id="message" style="text-align:center; margin-top:15px;"></p>
            </form>
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

    <!--<script>
    function submitTransfer() {
        let sender = document.getElementById("senderEmail").value;
        let recipient = document.getElementById("recipientEmail").value;
        let amount = document.getElementById("amount").value;
        let date = document.getElementById("transferDate").value;

        const transfer = {
            senderEmail: sender,
            recipientEmail: recipient,
            amount: amount,
            date: date
        };

        // Save to localStorage (append to existing transfers)
        let transfers = JSON.parse(localStorage.getItem("transfers")) || [];
        transfers.push(transfer);
        localStorage.setItem("transfers", JSON.stringify(transfers));

        document.getElementById("message").textContent =
            "Transfer scheduled successfully!";
    }
    -->
    </script>

</body>

</html>