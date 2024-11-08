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
    // Combine address fields
    $address = $_POST['tole'] . ', ' . $_POST['city'] . ', ' . $_POST['state'];
    $address = filter_var($address, FILTER_SANITIZE_STRING);

    // Update address in the database
    $update_address = $conn->prepare("UPDATE `users` SET address = ? WHERE id = ?");
    $update_address->execute([$address, $user_id]);

    // Success message
    $message[] = 'Address updated successfully!';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Address</title>

    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- TailwindCSS CDN link -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="bg-gray-100">

    <!-- Header Section -->
    <?php include 'components/user_header.php'; ?>

    <!-- Address Update Form Section -->
    <section class="max-w-4xl mx-auto p-6 mt-8 bg-white rounded-lg shadow-md mb-8">
        <h3 class="text-2xl font-semibold text-center text-gray-700 mb-8">Update Your Address</h3>

        <!-- Address Form -->
        <form action="" method="post" class="space-y-6">
            
            <!-- Tole (Street Name) Input -->
            <div class="flex flex-col">
                <label for="tole" class="text-lg font-medium text-gray-600 mb-2">Tole Name</label>
                <input type="text" name="tole" class="input-box" placeholder="Enter Tole Name" required maxlength="50">
            </div>

            <!-- City Input -->
            <div class="flex flex-col">
                <label for="city" class="text-lg font-medium text-gray-600 mb-2">City</label>
                <input type="text" name="city" class="input-box" placeholder="Enter City" required maxlength="50">
            </div>

            <!-- State Input -->
            <div class="flex flex-col">
                <label for="state" class="text-lg font-medium text-gray-600 mb-2">State</label>
                <input type="text" name="state" class="input-box" placeholder="Enter State" required maxlength="50">
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center">
                <button type="submit" name="submit" class="btn-submit">Save Address</button>
            </div>
        </form>

    </section>

    <!-- Footer Section -->
    <?php include 'components/footer.php'; ?>

    <!-- Custom JS file -->
    <script src="js/script.js"></script>
</body>

</html>
