// Get logged in user
const user = JSON.parse(localStorage.getItem("loggedInUser"));

if (!user) {
    alert("Please log in first.");
    window.location.href = "login.php";
}
//Load their account
let accounts = JSON.parse(localStorage.getItem(`accounts_${user.username}`));


//If no accounts, intialize with present balances
if (!accounts) {
    accounts = {
        checking: 1000,
        savings: 1000,
        credit: 0
    };
    localStorage.setItem(`accounts_${user.username}`, JSON.stringify(accounts));
}

//Insert values into homepage
document.getElementById("homeCheckingBalance").innerText = accounts.checking.toFixed(2);
document.getElementById("homeSavingsBalance").innerText = accounts.savings.toFixed(2);
