<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:home.php');
};

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Profile</title>

   <!-- Font Awesome for Icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Tailwind CSS (via CDN) -->
   <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="bg-gray-50 text-gray-800">

<!-- Header section starts -->
<?php include 'components/user_header.php'; ?>
<!-- Header section ends -->

<section class="py-16 px-4">

   <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8">
      <div class="flex items-center space-x-6">
         <img src="images/user-icon.png" alt="User Profile Picture" class="w-24 h-24 object-cover rounded-full shadow-lg">
         <div>
            <h2 class="text-3xl font-semibold text-gray-800"><?= $fetch_profile['name']; ?></h2>
            <p class="text-sm text-gray-600"><?= $fetch_profile['email']; ?></p>
            <p class="text-sm text-gray-600"><?= $fetch_profile['number']; ?></p>
         </div>
      </div>

      <div class="mt-8">
         <h3 class="text-2xl font-semibold text-gray-800 mb-4">User Information</h3>

         <div class="space-y-4">
            <div class="flex items-center space-x-2">
               <i class="fas fa-phone-alt text-gray-600"></i>
               <span class="text-lg"><?= $fetch_profile['number']; ?></span>
            </div>
            <div class="flex items-center space-x-2">
               <i class="fas fa-envelope text-gray-600"></i>
               <span class="text-lg"><?= $fetch_profile['email']; ?></span>
            </div>
            <div class="flex items-center space-x-2">
               <i class="fas fa-map-marker-alt text-gray-600"></i>
               <span class="text-lg"><?= ($fetch_profile['address'] == '') ? 'Please enter your address' : $fetch_profile['address']; ?></span>
            </div>
         </div>

         <div class="mt-6 flex space-x-4">
            <a href="update_profile.php" class="w-full md:w-auto px-6 py-3 bg-blue-500 text-white rounded-lg text-center hover:bg-blue-600 transition-all">Update Profile</a>
            <a href="update_address.php" class="w-full md:w-auto px-6 py-3 bg-gray-200 text-gray-700 rounded-lg text-center hover:bg-gray-300 transition-all">Update Address</a>
         </div>
      </div>
   </div>

</section>

<!-- Footer section -->
<?php include 'components/footer.php'; ?>

<!-- Custom JS file link -->
<script src="js/script.js"></script>

</body>
</html>
