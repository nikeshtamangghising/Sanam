<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
}

if (isset($_POST['update_payment'])) {

   $order_id = $_POST['order_id'];
   $payment_status = $_POST['payment_status'];
   $update_status = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
   $update_status->execute([$payment_status, $order_id]);
   $message[] = 'Payment status updated!';

}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $delete_order->execute([$delete_id]);
   header('location:placed_orders.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Placed Orders</title>

   <!-- Font Awesome CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Tailwind CSS CDN -->
   <script src="https://cdn.tailwindcss.com"></script>


</head>
<body class="bg-gray-100 text-gray-800">

<?php include '../components/admin_header.php'; ?>

<!-- Placed Orders Section -->
<section class="py-12">

   <div class="container mx-auto px-4">
      <h1 class="text-4xl font-semibold text-center mb-8">Placed Orders</h1>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
         <?php
         $select_orders = $conn->prepare("SELECT * FROM `orders`");
         $select_orders->execute();
         if ($select_orders->rowCount() > 0) {
            while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
         ?>
         <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition duration-300">
            <p><strong>User ID:</strong> <span class="font-medium"><?= $fetch_orders['user_id']; ?></span></p>
            <p><strong>Placed On:</strong> <span class="font-medium"><?= $fetch_orders['placed_on']; ?></span></p>
            <p><strong>Name:</strong> <span class="font-medium"><?= $fetch_orders['name']; ?></span></p>
            <p><strong>Email:</strong> <span class="font-medium"><?= $fetch_orders['email']; ?></span></p>
            <p><strong>Phone:</strong> <span class="font-medium"><?= $fetch_orders['number']; ?></span></p>
            <p><strong>Address:</strong> <span class="font-medium"><?= $fetch_orders['address']; ?></span></p>
            <p><strong>Total Products:</strong> <span class="font-medium"><?= $fetch_orders['total_products']; ?></span></p>
            <p><strong>Total Price:</strong> <span class="font-medium">$<?= $fetch_orders['total_price']; ?>/-</span></p>
            <p><strong>Payment Method:</strong> <span class="font-medium"><?= $fetch_orders['method']; ?></span></p>

            <form action="" method="POST" class="mt-4">
               <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
               <div class="mb-4">
                  <label for="payment_status" class="block text-sm font-medium text-gray-700">Payment Status</label>
                  <select name="payment_status" id="payment_status" class="w-full p-4 mt-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                     <option value="" selected disabled><?= ucfirst($fetch_orders['payment_status']); ?></option>
                     <option value="pending">Pending</option>
                     <option value="completed">Completed</option>
                  </select>
               </div>

               <div class="flex justify-between items-center">
                  <input type="submit" value="Update" name="update_payment" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-500 transition duration-300">
                  <a href="placed_orders.php?delete=<?= $fetch_orders['id']; ?>" class="text-red-600 hover:text-red-500" onclick="return confirm('Are you sure you want to delete this order?');">Delete</a>
               </div>
            </form>
         </div>
         <?php
            }
         } else {
            echo '<p class="col-span-full text-center text-gray-500">No orders placed yet!</p>';
         }
         ?>
      </div>
   </div>

</section>

<!-- Custom JS -->
<script src="../js/admin_script.js"></script>

</body>
</html>