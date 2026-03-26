document.getElementById("loginForm").addEventListener("submit", function (event) {
    event.preventDefault();

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const message = document.getElementById("loginMessage");

    if (email === "" || password === "") {
        message.textContent = "Please fill in both fields.";
        message.style.color = "red";
    } else {
        message.textContent = "Checking credentials...";
        message.style.color = "black";
    }

    const formData = new FormData();
    const csrfToken = document.getElementById("csrfToken").value;
    formData.append("csrfToken", csrfToken);
    formData.append("email", email);
    formData.append("password", password);

    fetch("login.php", {
        method: "POST",
        body: formData
    })
    
    .then(response => response.json())
    .then(data => {

        if (data.success) {
            message.textContent = data.message;
            message.style.color = "green";

            setTimeout(() => {
                window.location.href = "../main/dashboardHTML.php";
            }, 2000);
        } else {
            message.textContent = data.message;
            message.style.color = "red";
        }
    })
    .catch(() => {
        message.textContent = "something has gone wrong!";
        message.style.color = "red";
    })
})