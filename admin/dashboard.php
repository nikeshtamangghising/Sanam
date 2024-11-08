<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
   exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <!-- Tailwind CSS CDN link -->
   <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
</head>
<body class="bg-gray-50 text-gray-800">

<?php include '../components/admin_header.php'; ?>

<!-- Admin Dashboard Section -->
<section class="py-12">
   <div class="container mx-auto px-4">
      <h1 class="text-3xl font-semibold text-center mb-8">Dashboard Overview</h1>

      <!-- Dashboard Boxes -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
         
         <!-- Welcome Box -->
         <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <h3 class="text-2xl font-semibold mb-2">Welcome!</h3>
            <p class="text-lg font-medium text-gray-700"><?= htmlspecialchars($fetch_profile['name']); ?></p>
            <a href="update_profile.php" class="mt-4 inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-500 transition duration-300">Update Profile</a>
         </div>

         <!-- Pending Orders -->
         <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <?php
               $total_pendings = 0;
               $select_pendings = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
               $select_pendings->execute(['pending']);
               while($fetch_pendings = $select_pendings->fetch(PDO::FETCH_ASSOC)){
                  $total_pendings += $fetch_pendings['total_price'];
               }
            ?>
            <h3 class="text-2xl font-semibold text-blue-600 mb-2">Rs. <?= number_format($total_pendings); ?>/-</h3>
            <p class="text-gray-600">Total Pending Orders</p>
            <a href="placed_orders.php" class="mt-4 inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-500 transition duration-300">See Orders</a>
         </div>

         <!-- Completed Orders -->
         <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <?php
               $total_completes = 0;
               $select_completes = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
               $select_completes->execute(['completed']);
               while($fetch_completes = $select_completes->fetch(PDO::FETCH_ASSOC)){
                  $total_completes += $fetch_completes['total_price'];
               }
            ?>
            <h3 class="text-2xl font-semibold text-blue-600 mb-2">Rs. <?= number_format($total_completes); ?>/-</h3>
            <p class="text-gray-600">Total Completed Orders</p>
            <a href="placed_orders.php" class="mt-4 inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-500 transition duration-300">See Orders</a>
         </div>

         <!-- Total Orders -->
         <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <?php
               $select_orders = $conn->prepare("SELECT * FROM `orders`");
               $select_orders->execute();
               $numbers_of_orders = $select_orders->rowCount();
            ?>
            <h3 class="text-2xl font-semibold text-blue-600 mb-2"><?= $numbers_of_orders; ?></h3>
            <p class="text-gray-600">Total Orders</p>
            <a href="placed_orders.php" class="mt-4 inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-500 transition duration-300">See Orders</a>
         </div>

         <!-- Total Products -->
         <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <?php
               $select_products = $conn->prepare("SELECT * FROM `products`");
               $select_products->execute();
               $numbers_of_products = $select_products->rowCount();
            ?>
            <h3 class="text-2xl font-semibold text-blue-600 mb-2"><?= $numbers_of_products; ?></h3>
            <p class="text-gray-600">Products Added</p>
            <a href="products.php" class="mt-4 inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-500 transition duration-300">See Products</a>
         </div>

         <!-- User Accounts -->
         <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <?php
               $select_users = $conn->prepare("SELECT * FROM `users`");
               $select_users->execute();
               $numbers_of_users = $select_users->rowCount();
            ?>
            <h3 class="text-2xl font-semibold text-blue-600 mb-2"><?= $numbers_of_users; ?></h3>
            <p class="text-gray-600">User Accounts</p>
            <a href="users_accounts.php" class="mt-4 inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-500 transition duration-300">See Users</a>
         </div>

         <!-- Admin Accounts -->
         <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <?php
               $select_admins = $conn->prepare("SELECT * FROM `admin`");
               $select_admins->execute();
               $numbers_of_admins = $select_admins->rowCount();
            ?>
            <h3 class="text-2xl font-semibold text-blue-600 mb-2"><?= $numbers_of_admins; ?></h3>
            <p class="text-gray-600">Admins</p>
            <a href="admin_accounts.php" class="mt-4 inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-500 transition duration-300">See Admins</a>
         </div>
      </div>
   </div>
</section>

</body>
</html>
