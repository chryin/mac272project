//Load the user
const user = JSON.parse(localStorage.getItem("loggedInUser"));

if (!user) {
    alert("You must log in first.");
    window.location.href = "../login-signup/login.php";
}

//Load account balances
let accounts = JSON.parse(localStorage.getItem(`accounts_${user.username}`));

//Display balances on page load
document.getElementById("checkingBalance").innerText = accounts.checking.toFixed(2);
document.getElementById("savingsBalance").innerText = accounts.savings.toFixed(2);


function deposit(type) {
    const inputId = type === "checking" ? "checkingDeposit" : "savingsDeposit";
    const amount = parseFloat(document.getElementById(inputId).value);

    if (isNaN(amount) || amount <= 0) {
        alert("Enter a valid amount.");
        return;
    }

    accounts[type] += amount;

    saveAccounts();
    updateUI();
}



function withdraw(type) {
    const inputId = type === "checking" ? "checkingWithdraw" : "savingsWithdraw";
    const amount = parseFloat(document.getElementById(inputId).value);

    if (isNaN(amount) || amount <= 0) {
        alert("Enter a valid amount.");
        return;
    }

    if (accounts[type] < amount) {
        alert("Insufficient funds");
        return;
    }

    accounts[type] -= amount;

    saveAccounts();
    updateUI();
}



function updateUI() {
    document.getElementById("checkingBalance").innerText = accounts.checking.toFixed(2);
    document.getElementById("savingsBalance").innerText = accounts.savings.toFixed(2);
}

function saveAccounts() {
    localStorage.setItem(`accounts_${user.username}`, JSON.stringify(accounts));
}
