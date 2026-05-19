<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../style.css">
 
    <title>My Bank MAC172 Admin Home</title>

</head>

<body>

    <header>
        <h1>MAC172 Bank Admin Portal</h1>

        <nav>
            <ul>
                <li><a href="admin_homepage.php">Home</a></li>
                <li><a href="admin_manage_acc.php">Account Management</a></li>
                <li><a href="admin_manage_emp.php">Employee Management</a></li>
                <li><a href="admin_help_tickets.php">Help Tickets</a></li>
                <li><a href="admin_profile.php">Profile</a></li>
            </ul>
        </nav>
    </header>

    <section id="welcome">
        <h2>Welcome Back to the Admin Dashboard</h2>
        <p>Monitor accounts, manage transactions, and oversee banking activity from one place.</p>

        <div id="quick-actions">
            <a href="admin_manage_acc.php">View Accounts</a>
            <a href="admin_shifts.php">Manage Shifts</a>
            <a href="admin_create_emp.php">Create Employee Accounts</a>
        </div>
    </section>


    <section id="overview">
        <h2>Company Weekly Overview</h2>

        <div class="account-box">
            <h3>Weekly Goals</h3>
            <ul>
                <li>Review 25 pending loan applications</li>
                <li>Complete staff training for customer service updates</li>
                <li>Reduce overdue account follow-ups by 10%</li>
            </ul>
            <a href="admin_manage_emp.php">View Goals</a>
        </div>

            <h2 class="overview">Company News</h3>
            <div class="news-box">
                <div class="news-card">
                    <img src="../images/branch.jpg" alt="New branch opening">
                    <p>New branch opening in downtown on Monday.</p>
                </div>
                <div class="news-card">
                    <img src="../images/app-update.jpg" alt="Mobile app update">
                    <p>Mobile app update scheduled for Friday rollout.</p>
                </div>
                <div class="news-card">
                    <img src="../images/wellness.jpg" alt="Employee wellness program">
                    <p>Employee wellness program launches next week.</p>
                </div>
            </div>
    </section>

    <footer>
        <p>2025 MAC172 Online Banking. All Rights Reserved.</p>
        <p><a href="../aboutpage.php">About</a> | <a href="../contactpage.php">Contact</a> | <a href="../termsofservicepage.php">Terms of Service</a></p>
    </footer>
    <script src="../script.js"></script>
    <script src="../homepage.js"></script>
</body>
</html>