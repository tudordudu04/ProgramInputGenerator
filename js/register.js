const form = document.getElementById('registerForm');
const messageDiv = document.getElementById('message');

form.addEventListener('submit', function (e) {
    e.preventDefault();

    const password = form.password.value;
    const password2 = form.password2.value;

    if (password !== password2) {
        messageDiv.textContent = "Passwords do not match!";
        messageDiv.style.color = "red";
        return;
    }

    const formData = new FormData(form);

    fetch('../auth/register.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        messageDiv.textContent = data.message;
        messageDiv.style.color = data.success ? "green" : "red";
        if(data.success){
            messageDiv.style.color = "green";
            let countdown = 3;
            messageDiv.textContent = data.message + " Redirecting to login page in: " + countdown;
            const intervalId = setInterval(() => {
                countdown--;
                if (countdown > 0) {
                    messageDiv.textContent = data.message + " Redirecting to login page in: " + countdown;
                } else {
                    messageDiv.textContent = '';
                    form.reset();
                    clearInterval(intervalId);
                    window.location.href = "login.html";
                }
            }, 800);
        } else {
            messageDiv.style.color = "red";
        }
    })
    .catch(() => {
        messageDiv.textContent = "Registration failed, please try again.";
        messageDiv.style.color = "red";
    });
});