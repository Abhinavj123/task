// Handling register form submission
document.querySelector('.register-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent form submission

    // Handle registration logic here
    alert('Registration successful! You can now log in.');
    window.location.href = '../'; // Redirect to login page after registration
});
