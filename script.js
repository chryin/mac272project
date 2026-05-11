function signup() {
    const firstName = document.getElementById("signupFirstName").value;
    const lastName = document.getElementById("signupLastName").value;
    const email = document.getElementById("signupEmail").value;
    const username = document.getElementById("signupUsername").value;
    const password = document.getElementById("signupPassword").value;
    const address = document.getElementById("signupAddress").value;

    let users = JSON.parse(localStorage.getItem("users")) || [];

    const exists = users.some(user => user.username === username);

    if (exists) {
        alert("Username already taken. Choose another.");
        return;
    }

    const newUser = {
        firstName,
        lastName,
        email,
        username,
        password,
        address
    };

    users.push(newUser);
    localStorage.setItem("users", JSON.stringify(users));

    alert("Signup successful. You can now login.");
    const accounts = {
        checking: 1000,
        savings: 5000
    };

    localStorage.setItem(`accounts_${username}`, JSON.stringify(accounts));

    window.location.href = "login.html";
}

function login() {
    const username = document.getElementById("loginUsername").value;
    const password = document.getElementById("loginPassword").value;

    // Retrieve users
    let users = JSON.parse(localStorage.getItem("users")) || [];

    // Find matching user
    const user = users.find(
        u => u.username === username && u.password === password
    );

    const message = document.getElementById("message");

    if (!user) {
        message.innerText = "Incorrect username or password";
        return;
    }

    // Save logged-in user to localStorage
    localStorage.setItem("loggedInUser", JSON.stringify(user));

    message.innerText = "Login successful!";
    window.location.href = "homepage.html";
}
