// Get logged-in user info
const user = JSON.parse(localStorage.getItem("loggedInUser"));

if (!user) {
    alert("Please log in first.");
    window.location.href = "login.php";
}

// Fill in profile fields
document.getElementById("profileFirstName").innerText = user.firstName;
document.getElementById("profileLastName").innerText = user.lastName;
document.getElementById("profileEmail").innerText = user.email;
document.getElementById("profileUsername").innerText = user.username;
document.getElementById("profileAddress").innerText = user.address;
