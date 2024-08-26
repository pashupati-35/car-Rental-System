document.addEventListener('DOMContentLoaded', (event) => {
    // Example: Validate form on submission
    document.querySelector('form').addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        if (!email || !password) {
            alert('Please fill out all required fields.');
            e.preventDefault(); // Prevent form submission
        }
    });
});
