document.getElementById("registerForm").addEventListener("submit", function (event) {
    event.preventDefault();

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const password2 = document.getElementById("passwordConfirm").value.trim();
    const firstName = document.getElementById("firstName").value.trim();
    const lastName = document.getElementById("lastName").value.trim();
    const message = document.getElementById("registerMessage");

    if (email === "" || password === "" || password2 === "" || firstName === "" || lastName === "") {
        message.textContent = "Please fill in all fields.";
        message.style.color = "red";
        return;
    } else if(password !== password2){
        message.textContent = "Passwords do not match!";
        message.style.color = "red";
        return;
    } else {
        message.textContent = "Checking credentials...";
        message.style.color = "black";
    }

    const formData = new FormData();
    const csrfToken = document.getElementById("csrfToken").value;



    formData.append("csrfToken", csrfToken);
    formData.append("email", email);
    formData.append("password", password);
    formData.append("password2", password2);
    formData.append("firstName", firstName);
    formData.append("lastName", lastName);

    fetch("register.php", {
        method: "POST",
        body: formData
    })

    .then(response => response.json())
    .then(data => {

        if (data.success) {
            message.textContent = "Registration Successful! Come back to the login page once you have been approved!";
            message.style.color = "green";
            
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