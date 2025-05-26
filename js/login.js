const form = document.getElementById('loginForm');
const messageDiv = document.getElementById('message');

form.addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(form);

    fetch('../auth/login.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        messageDiv.textContent = data.message;
        messageDiv.style.color = data.success ? "green" : "red";
        if (data.success) {
            localStorage.setItem('jwt', data.token);
            window.location.href = '../index.php';
        }
    })
    .catch(() => {
        messageDiv.textContent = "Sign in failed, please try again.";
        messageDiv.style.color = "red";
    });
});