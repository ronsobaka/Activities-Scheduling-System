$(document).ready(function() {
    $('#resetPasswordForm').submit(function(e) {
        e.preventDefault();
        
        const password = $('#password').val();
        const confirm = $('#confirmPassword').val();
        const token = $('#token').val();
        
        if (password !== confirm) {
            alert('Passwords do not match');
            return;
        }
        
        $.ajax({
            url: 'resetPassword.php',
            type: 'POST',
            data: { 
                token: token, 
                password: password 
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Password has been reset successfully!');
                    window.location.href = '../loginHTML.php';
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });
});r