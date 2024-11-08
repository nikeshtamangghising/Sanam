<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_users = $conn->prepare("DELETE FROM `users` WHERE id = ?");
   $delete_users->execute([$delete_id]);
   $delete_order = $conn->prepare("DELETE FROM `orders` WHERE user_id = ?");
   $delete_order->execute([$delete_id]);
   $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
   $delete_cart->execute([$delete_id]);
   header('location:users_accounts.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>User Accounts</title>

   <!-- Font Awesome CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Tailwind CSS CDN -->
   <script src="https://cdn.tailwindcss.com"></script>

   

</head>
<body class="bg-gray-100 text-gray-800">

<?php include '../components/admin_header.php'; ?>

<!-- User Accounts Section -->
<section class="py-12">

   <div class="container mx-auto px-4">
      <h1 class="text-4xl font-semibold text-center mb-8">User Accounts</h1>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
         <?php
         $select_account = $conn->prepare("SELECT * FROM `users`");
         $select_account->execute();
         if ($select_account->rowCount() > 0) {
            while ($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)) {  
         ?>
         <!-- User Account Box -->
         <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300">
            <p><strong>User ID:</strong> <span class="font-medium"><?= $fetch_accounts['id']; ?></span></p>
            <p><strong>Username:</strong> <span class="font-medium"><?= $fetch_accounts['name']; ?></span></p>
            <a href="users_accounts.php?delete=<?= $fetch_accounts['id']; ?>" class="block mt-4 bg-red-600 text-white text-center py-2 px-4 rounded-lg hover:bg-red-500 transition duration-300" onclick="return confirm('Are you sure you want to delete this account?');">Delete</a>
         </div>
         <?php
            }
         } else {
            echo '<p class="col-span-full text-center text-gray-500">No accounts available</p>';
         }
         ?>
      </div>
   </div>

</section>

<!-- Custom JS -->
<script src="../js/admin_script.js"></script>

</body>
</html>
