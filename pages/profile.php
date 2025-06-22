<?php
require_once '../includes/header.php';

// Redirect to login if not logged in
if (!isLoggedIn()) {
    redirect(SITE_URL . '/pages/login.php?redirect=profile.php');
}

$user_id = getUserId();
$success_message = '';
$error_message = '';

// Fetch user information
$user_query = "SELECT * FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Validate and sanitize input
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    
    $errors = [];
    
    // Validate email format
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }
    
    // Check if email already exists (for another user)
    if ($email !== $user['email']) {
        $email_check_query = "SELECT id FROM users WHERE email = '$email' AND id != $user_id";
        $email_check_result = mysqli_query($conn, $email_check_query);
        if (mysqli_num_rows($email_check_result) > 0) {
            $errors[] = "Email address is already in use by another account";
        }
    }
    
    // If no errors, update profile
    if (empty($errors)) {
        $update_query = "UPDATE users SET 
                        full_name = '$full_name', 
                        email = '$email', 
                        phone = '$phone', 
                        address = '$address' 
                        WHERE id = $user_id";
        
        if (mysqli_query($conn, $update_query)) {
            $success_message = "Profile updated successfully";
            
            // Refresh user data
            $user_result = mysqli_query($conn, $user_query);
            $user = mysqli_fetch_assoc($user_result);
        } else {
            $error_message = "Failed to update profile: " . mysqli_error($conn);
        }
    } else {
        $error_message = implode("<br>", $errors);
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];
    
    // Validate current password
    if (!password_verify($current_password, $user['password'])) {
        $errors[] = "Current password is incorrect";
    }
    
    // Validate new password
    if (strlen($new_password) < 6) {
        $errors[] = "New password must be at least 6 characters long";
    }
    
    // Validate password confirmation
    if ($new_password !== $confirm_password) {
        $errors[] = "New passwords do not match";
    }
    
    // If no errors, update password
    if (empty($errors)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET password = '$hashed_password' WHERE id = $user_id";
        
        if (mysqli_query($conn, $update_query)) {
            $success_message = "Password changed successfully";
        } else {
            $error_message = "Failed to change password: " . mysqli_error($conn);
        }
    } else {
        $error_message = implode("<br>", $errors);
    }
}

$page_title = "My Profile";
?>

<div class="container py-4">
    <h1 class="mb-4">My Profile</h1>
    
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-lg-4 mb-4 mb-lg-0">
            <!-- Profile Menu -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="profile-avatar me-3">
                            <i class="fas fa-user-circle fa-4x text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-0"><?php echo $user['full_name']; ?></h5>
                            <p class="text-muted mb-0"><?php echo $user['email']; ?></p>
                        </div>
                    </div>
                    
                    <div class="list-group list-group-flush">
                        <a href="#profile-info" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                            <i class="fas fa-user me-2"></i>Profile Information
                        </a>
                        <a href="#change-password" class="list-group-item list-group-item-action" data-bs-toggle="list">
                            <i class="fas fa-lock me-2"></i>Change Password
                        </a>
                        <a href="<?php echo SITE_URL; ?>/pages/orders.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-shopping-bag me-2"></i>My Orders
                        </a>
                        <a href="<?php echo SITE_URL; ?>/pages/logout.php" class="list-group-item list-group-item-action text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Account Stats -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Account Summary</h5>
                </div>
                <div class="card-body">
                    <?php
                    // Get order stats
                    $order_stats_query = "SELECT 
                                        COUNT(*) as total_orders,
                                        SUM(total_amount) as total_spent,
                                        MAX(order_date) as last_order_date
                                        FROM orders 
                                        WHERE user_id = $user_id";
                    $order_stats_result = mysqli_query($conn, $order_stats_query);
                    $order_stats = mysqli_fetch_assoc($order_stats_result);
                    ?>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Member Since</span>
                        <span class="fw-bold"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Total Orders</span>
                        <span class="fw-bold"><?php echo $order_stats['total_orders'] ?? 0; ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Total Spent</span>
                        <span class="fw-bold"><?php echo formatPrice($order_stats['total_spent'] ?? 0); ?></span>
                    </div>
                    <?php if (!empty($order_stats['last_order_date'])): ?>
                    <div class="d-flex justify-content-between">
                        <span>Last Order</span>
                        <span class="fw-bold"><?php echo date('M j, Y', strtotime($order_stats['last_order_date'])); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="tab-content">
                <!-- Profile Information -->
                <div class="tab-pane fade show active" id="profile-info">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Profile Information</h5>
                        </div>
                        <div class="card-body">
                            <form method="post" id="profile-form">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" value="<?php echo $user['username']; ?>" readonly>
                                    <small class="text-muted">Username cannot be changed</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $user['full_name']; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo $user['phone']; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3"><?php echo $user['address']; ?></textarea>
                                </div>
                                
                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Change Password -->
                <div class="tab-pane fade" id="change-password">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Change Password</h5>
                        </div>
                        <div class="card-body">
                            <form method="post" id="password-form">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    <small class="text-muted">Password must be at least 6 characters long</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                
                                <button type="submit" name="change_password" class="btn btn-primary">
                                    <i class="fas fa-key me-2"></i>Change Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Form validation for profile update
    $('#profile-form').on('submit', function(e) {
        var isValid = true;
        
        // Validate email
        var email = $('#email').val();
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            isValid = false;
            $('#email').addClass('is-invalid');
        } else {
            $('#email').removeClass('is-invalid');
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
    
    // Form validation for password change
    $('#password-form').on('submit', function(e) {
        var isValid = true;
        
        // Validate new password length
        var newPassword = $('#new_password').val();
        if (newPassword.length < 6) {
            isValid = false;
            $('#new_password').addClass('is-invalid');
        } else {
            $('#new_password').removeClass('is-invalid');
        }
        
        // Validate password confirmation
        var confirmPassword = $('#confirm_password').val();
        if (newPassword !== confirmPassword) {
            isValid = false;
            $('#confirm_password').addClass('is-invalid');
        } else {
            $('#confirm_password').removeClass('is-invalid');
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
    
    // Remove invalid class on input
    $('input').on('input', function() {
        $(this).removeClass('is-invalid');
    });
    
    // Handle tab navigation from URL hash
    var hash = window.location.hash;
    if (hash) {
        $('.list-group-item[href="' + hash + '"]').tab('show');
    }
    
    // Update URL hash on tab change
    $('.list-group-item').on('shown.bs.tab', function(e) {
        window.location.hash = e.target.getAttribute('href');
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>