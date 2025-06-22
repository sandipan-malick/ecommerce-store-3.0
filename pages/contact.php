<?php
require_once '../includes/header.php';

// Define missing functions and constants if not already defined
if (!function_exists('sanitize')) {
    function sanitize($input) {
        global $conn;
        return mysqli_real_escape_string($conn, trim($input));
    }
}

if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('getUserId')) {
    function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
}

if (!defined('ADMIN_EMAIL')) {
    define('ADMIN_EMAIL', 'admin@ecommerce.com');
}

$success_message = '';
$error_message = '';
$form_data = [
    'name' => '',
    'email' => '',
    'subject' => '',
    'message' => ''
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    // Get form data
    $form_data = [
        'name' => sanitize($_POST['name']),
        'email' => sanitize($_POST['email']),
        'subject' => sanitize($_POST['subject']),
        'message' => sanitize($_POST['message'])
    ];

    // Validate form data
    $errors = [];

    if (empty($form_data['name'])) {
        $errors[] = "Name is required";
    }

    if (empty($form_data['email']) || !filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }

    if (empty($form_data['subject'])) {
        $errors[] = "Subject is required";
    }

    if (empty($form_data['message'])) {
        $errors[] = "Message is required";
    }

    // If no errors, save message to database
    if (empty($errors)) {
        $user_id = isLoggedIn() ? getUserId() : 'NULL';

        $insert_query = "INSERT INTO contact_messages (user_id, name, email, subject, message) 
                         VALUES ($user_id, '{$form_data['name']}', '{$form_data['email']}', '{$form_data['subject']}', '{$form_data['message']}')";

        if (mysqli_query($conn, $insert_query)) {
            $success_message = "Your message has been sent successfully. We'll get back to you soon!";

            // Clear form data after successful submission
            $form_data = [
                'name' => '',
                'email' => '',
                'subject' => '',
                'message' => ''
            ];
        } else {
            $error_message = "Failed to send message: " . mysqli_error($conn);
        }
    } else {
        $error_message = implode("<br>", $errors);
    }
}

// Pre-fill form if user is logged in
if (isLoggedIn() && empty($form_data['name']) && empty($form_data['email'])) {
    $user_id = getUserId();
    $user_query = "SELECT full_name, email FROM users WHERE id = $user_id";
    $user_result = mysqli_query($conn, $user_query);

    if ($user = mysqli_fetch_assoc($user_result)) {
        $form_data['name'] = $user['full_name'];
        $form_data['email'] = $user['email'];
    }
}

$page_title = "Contact Us";
?>

<!-- HTML Markup remains the same -->
<!-- Be sure to include jQuery and Bootstrap JS for script functionality -->
<script>
$(document).ready(function() {
    $('#contact-form').on('submit', function(e) {
        var isValid = true;

        var email = $('#email').val();
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            isValid = false;
            $('#email').addClass('is-invalid');
        } else {
            $('#email').removeClass('is-invalid');
        }

        $('#name, #subject, #message').each(function() {
            if ($(this).val().trim() === '') {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
        }
    });

    $('input, textarea').on('input', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
