<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <h1>My MAC172 Bank Admin Login</h1>
</header>

<section id="welcome">
    <h2>Admin Login</h2>
</section>

<div class="account-box">
    <h3>Admin Login</h3>
    <form id="adminLoginForm" method="post" action="admin_dashboard.php">
        <div class="form-group">
            <label for="adminUsername">Username</label>
            <input type="text" id="adminUsername" name="username" required>
        </div>
        <br>
        <div class="form-group">
            <label for="adminPassword">Password</label>
            <input type="password" id="adminPassword" name="password" required>
        </div>
        <br>
        <div class="form-group">
            <label for="employeeId">Employee ID</label>
            <input type="text" id="employeeId" name="employee_id" required>
        </div>
        <br>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>