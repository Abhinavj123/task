document.addEventListener('DOMContentLoaded', () => {
    // Function to update user name and other information
    function updateUserInfo() {
        // Fetch the logged-in user's email
        fetch('getUserEmail.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Error fetching email:', data.error);
                    return;
                }

                const userEmail = data.email; // Email retrieved from the PHP script

                // Fetch user data based on email
                return fetch(`getUser.php?email=${encodeURIComponent(userEmail)}`);
            })
            .then(response => response.json())
            .then(userData => {
                if (userData) {
                    document.getElementById('userName').textContent = userData.name || 'User not found';
                    // You can update other parts of the dashboard with userData
                } else {
                    document.getElementById('userName').textContent = 'User not found';
                }
            })
            .catch(error => console.error('Error fetching user data:', error));
    }

    // Call the function to update user info when the page loads
    updateUserInfo();

    // Handle logout
    const logoutButton = document.querySelector('.logoutButton');

    if (logoutButton) {
        logoutButton.addEventListener('click', () => {
            fetch('logout.php', { method: 'POST' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '../login/login1.php'; // Redirect to login page
                    } else {
                        console.error('Logout failed:', data.error || 'Unknown error');
                    }
                })
                .catch(error => console.error('Error logging out:', error));
        });
    }
});
