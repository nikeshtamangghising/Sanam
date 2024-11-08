<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
    header('location:home.php');
}

if (isset($_POST['submit'])) {
    // Initialize error message array
    $message = [];

    // Sanitize and validate inputs
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);

    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);
    
    $number = $_POST['number'];
    $number = filter_var($number, FILTER_SANITIZE_STRING);

    // Password fields
    $old_pass = sha1($_POST['old_pass']);
    $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING);

    $new_pass = sha1($_POST['new_pass']);
    $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);

    $confirm_pass = sha1($_POST['confirm_pass']);
    $confirm_pass = filter_var($confirm_pass, FILTER_SANITIZE_STRING);

    // Fetch old password from the database
    $select_prev_pass = $conn->prepare("SELECT password FROM `users` WHERE id = ?");
    $select_prev_pass->execute([$user_id]);
    $fetch_prev_pass = $select_prev_pass->fetch(PDO::FETCH_ASSOC);
    $prev_pass = $fetch_prev_pass['password'];

    // Check if fields are empty
    if (empty($name)) {
        $message[] = 'Name cannot be empty!';
    }

    if (empty($email)) {
        $message[] = 'Email cannot be empty!';
    }

    if (empty($number)) {
        $message[] = 'Phone number cannot be empty!';
    }

    if (empty($old_pass)) {
        $message[] = 'Old password cannot be empty!';
    } else {
        // Validate the old password
        if ($old_pass != $prev_pass) {
            $message[] = 'Old password does not match!';
        }
    }

    // If no errors, proceed to update the profile
    if (empty($message)) {
        if (!empty($name)) {
            $update_name = $conn->prepare("UPDATE `users` SET name = ? WHERE id = ?");
            $update_name->execute([$name, $user_id]);
        }

        if (!empty($email)) {
            $select_email = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
            $select_email->execute([$email]);
            if ($select_email->rowCount() > 0) {
                $message[] = 'Email already taken!';
            } else {
                $update_email = $conn->prepare("UPDATE `users` SET email = ? WHERE id = ?");
                $update_email->execute([$email, $user_id]);
            }
        }

        if (!empty($number)) {
            $select_number = $conn->prepare("SELECT * FROM `users` WHERE number = ?");
            $select_number->execute([$number]);
            if ($select_number->rowCount() > 0) {
                $message[] = 'Phone number already taken!';
            } else {
                $update_number = $conn->prepare("UPDATE `users` SET number = ? WHERE id = ?");
                $update_number->execute([$number, $user_id]);
            }
        }
        
        // Update the password if new password is set and matches the confirm password
        if ($new_pass != '' && $new_pass == $confirm_pass) {
            $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
            $update_pass->execute([$new_pass, $user_id]);
            $message[] = 'Password updated successfully!';
        } elseif ($new_pass != $confirm_pass) {
            $message[] = 'New passwords do not match!';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>

    <!-- Font Awesome CDN Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- TailwindCSS CDN Link -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="css/style.css">

    <!-- JavaScript for form validation -->
    <script>
        function validateForm() {
            var name = document.getElementById('name').value;
            var email = document.getElementById('email').value;
            var number = document.getElementById('number').value;
            var old_pass = document.getElementById('old_pass').value;
            var new_pass = document.getElementById('new_pass').value;
            var confirm_pass = document.getElementById('confirm_pass').value;
            var errorMessage = '';

            if (name == '' || email == '' || number == '') {
                errorMessage += 'All fields are required!\n';
            }

            if (old_pass != '' && new_pass == '') {
                errorMessage += 'Please enter a new password!\n';
            }

            if (new_pass != confirm_pass) {
                errorMessage += 'Passwords do not match!\n';
            }

            if (errorMessage != '') {
                alert(errorMessage);
                return false;
            }
            return true;
        }
    </script>
</head>
<body class="bg-gray-100">

<!-- Header Section -->
<?php include 'components/user_header.php'; ?>

<!-- Main Update Profile Section -->
<section class="max-w-4xl mx-auto p-6 mt-8 bg-white rounded-lg shadow-md mb-8">
    <h3 class="text-2xl font-semibold text-center text-gray-700 mb-8">Update Your Profile</h3>

    <!-- Update Form -->
    <form action="" method="post" class="space-y-6" onsubmit="return validateForm()">
        
        <!-- Name Input -->
        <div class="flex flex-col">
            <label for="name" class="text-lg font-medium text-gray-600 mb-2">Full Name</label>
            <input type="text" name="name" id="name" class="input-box" placeholder="<?= htmlspecialchars($fetch_profile['name']); ?>" maxlength="50">
        </div>

        <!-- Email Input -->
        <div class="flex flex-col">
            <label for="email" class="text-lg font-medium text-gray-600 mb-2">Email</label>
            <input type="email" name="email" id="email" class="input-box" placeholder="<?= htmlspecialchars($fetch_profile['email']); ?>" maxlength="50">
        </div>

        <!-- Phone Number Input -->
        <div class="flex flex-col">
            <label for="number" class="text-lg font-medium text-gray-600 mb-2">Phone Number</label>
            <input type="number" name="number" id="number" class="input-box" placeholder="<?= htmlspecialchars($fetch_profile['number']); ?>" maxlength="10" min="0" max="9999999999">
        </div>

        <!-- Password Section -->
        <div class="space-y-4">
            <div class="flex flex-col">
                <label for="old_pass" class="text-lg font-medium text-gray-600 mb-2">Old Password</label>
                <input type="password" name="old_pass" id="old_pass" class="input-box" placeholder="Enter your old password" maxlength="50">
            </div>

            <div class="flex flex-col">
                <label for="new_pass" class="text-lg font-medium text-gray-600 mb-2">New Password</label>
                <input type="password" name="new_pass" id="new_pass" class="input-box" placeholder="Enter your new password" maxlength="50">
            </div>

            <div class="flex flex-col">
                <label for="confirm_pass" class="text-lg font-medium text-gray-600 mb-2">Confirm New Password</label>
                <input type="password" name="confirm_pass" id="confirm_pass" class="input-box" placeholder="Confirm your new password" maxlength="50">
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-center">
            <button type="submit" name="submit" class="btn-submit">Update Now</button>
        </div>
    </form>
</section>

<!-- Footer Section -->
<?php include 'components/footer.php'; ?>

<!-- Custom JS file -->
<script src="js/script.js"></script>

</body>
</html>
