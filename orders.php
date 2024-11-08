<?php
include 'components/connect.php';

session_start();

// Check if user is logged in
if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
   // Fetch the user email from the session
   $select_user_email = $conn->prepare("SELECT email FROM `users` WHERE id = ?");
   $select_user_email->execute([$user_id]);
   $user_email = $select_user_email->fetchColumn();
} else {
   $user_id = '';
   $user_email = ''; // Set empty email if not logged in
   header('location:home.php'); // Redirect if the user is not logged in
   exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Your Orders</title>

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- TailwindCSS CDN link -->
   <script src="https://cdn.tailwindcss.com"></script>

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body class="bg-gray-100">

<!-- Header Section -->
<?php include 'components/user_header.php'; ?>
<!-- End Header Section -->

<!-- Main Section -->
<div class="container mx-auto px-6 py-10">
   <div class="text-center mb-6">
      <h3 class="text-3xl font-semibold text-gray-800">Your Orders</h3>
      <p class="text-gray-500 mt-2"><a href="home.php" class="text-blue-600">Home</a> <span> / Orders</span></p>
   </div>

   <section class="orders min-h-[80vh]"> <!-- Set a min height of 80% -->
      <h1 class="text-2xl font-bold mb-6">Your Orders</h1>

      <div class="grid gap-8 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">

      <?php
         if($user_email == ''){ 
            echo '<p class="text-center text-gray-600">Please login to see your orders</p>';
         }else{
            // Fetch orders based on the logged-in user's email
            $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE email = ?");
            $select_orders->execute([$user_email]);
            
            if($select_orders->rowCount() > 0){
               // Loop through orders and display them
               while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
      ?>
      <div class="bg-white shadow-lg rounded-lg p-6">
         <div class="flex flex-col space-y-4">
            <p class="font-medium">Placed on: <span class="text-gray-600"><?= $fetch_orders['placed_on']; ?></span></p>
            <p class="font-medium">Name: <span class="text-gray-600"><?= $fetch_orders['name']; ?></span></p>
            <p class="font-medium">Email: <span class="text-gray-600"><?= $fetch_orders['email']; ?></span></p>
            <p class="font-medium">Number: <span class="text-gray-600"><?= $fetch_orders['number']; ?></span></p>
            <p class="font-medium">Address: <span class="text-gray-600"><?= $fetch_orders['address']; ?></span></p>
            <p class="font-medium">Payment Method: <span class="text-gray-600"><?= $fetch_orders['method']; ?></span></p>
            <p class="font-medium">Total Products: <span class="text-gray-600"><?= $fetch_orders['total_products']; ?></span></p>
            <p class="font-medium">Total Price: <span class="text-gray-800 font-semibold">Rs. <?= $fetch_orders['total_price']; ?>/-</span></p>
            <p class="font-medium text-sm text-white px-2 py-1 rounded-full <?php echo ($fetch_orders['payment_status'] == 'pending') ? 'bg-red-500' : 'bg-green-500'; ?>">
               Payment Status: <?= ucfirst($fetch_orders['payment_status']); ?>
            </p>
         </div>
      </div>
      <?php
         }
         } else {
            echo '<p class="text-center text-gray-600 col-span-full">No orders placed yet!</p>';
         }
      }
      ?>

      </div>

   </section>

</div>

<!-- Footer Section -->
<?php include 'components/footer.php'; ?>
<!-- End Footer Section -->

<!-- Custom JS file link -->
<script src="js/script.js"></script>

</body>
</html>
