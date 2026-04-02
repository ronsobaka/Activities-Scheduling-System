$(document).ready(function() {
    $('#forgotPasswordForm').submit(function(e) {
        e.preventDefault();
        
        const email = $('#email').val();
        
        $.ajax({
            url: 'forgotPassword.php',
            type: 'POST',
            data: { email: email },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Password reset link has been sent to your email.');
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
});