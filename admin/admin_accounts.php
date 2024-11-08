<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_admin = $conn->prepare("DELETE FROM `admin` WHERE id = ?");
   $delete_admin->execute([$delete_id]);
   header('location:admin_accounts.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Accounts</title>

   <!-- Font Awesome CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Tailwind CSS CDN -->
   <script src="https://cdn.tailwindcss.com"></script>


</head>
<body class="bg-gray-100 text-gray-800">

<?php include '../components/admin_header.php'; ?>

<!-- Admin Accounts Section -->
<section class="py-12">

   <div class="container mx-auto px-4">
      <h1 class="text-4xl font-semibold text-center mb-8">Admin Accounts</h1>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
         
         <!-- Register New Admin Box -->
         <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300">
            <p class="text-center font-semibold mb-4">Register New Admin</p>
            <a href="register_admin.php" class="block bg-blue-600 text-white text-center py-2 px-4 rounded-lg hover:bg-blue-500 transition duration-300">Register</a>
         </div>

         <?php
         $select_account = $conn->prepare("SELECT * FROM `admin`");
         $select_account->execute();
         if ($select_account->rowCount() > 0) {
            while ($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)) {  
         ?>
         <!-- Admin Account Box -->
         <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300">
            <p><strong>Admin ID:</strong> <span class="font-medium"><?= $fetch_accounts['id']; ?></span></p>
            <p><strong>Username:</strong> <span class="font-medium"><?= $fetch_accounts['name']; ?></span></p>
            <div class="flex justify-between items-center mt-4">
               <a href="admin_accounts.php?delete=<?= $fetch_accounts['id']; ?>" class="bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-500 transition duration-300" onclick="return confirm('Are you sure you want to delete this account?');">Delete</a>
               <?php
                  if ($fetch_accounts['id'] == $admin_id) {
                     echo '<a href="update_profile.php" class="bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-500 transition duration-300">Update</a>';
                  }
               ?>
            </div>
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
