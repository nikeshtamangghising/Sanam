<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
};

if (isset($_POST['submit'])) {

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ?");
   $select_admin->execute([$name]);

   if ($select_admin->rowCount() > 0) {
      $message[] = 'username already exists!';
   } else {
      if ($pass != $cpass) {
         $message[] = 'confirm password not matched!';
      } else {
         $insert_admin = $conn->prepare("INSERT INTO `admin`(name, password) VALUES(?,?)");
         $insert_admin->execute([$name, $cpass]);
         $message[] = 'new admin registered!';
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
   <title>Register Admin</title>

   <!-- Tailwind CSS -->
   <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-800">

<!-- Include Header -->
<?php include '../components/admin_header.php' ?>

<!-- Display messages -->
<?php if (isset($message)): ?>
   <?php foreach ($message as $msg): ?>
      <div class="fixed top-5 left-1/2 transform -translate-x-1/2 bg-red-500 text-white px-4 py-2 rounded-md flex items-center space-x-2">
         <span><?php echo $msg; ?></span>
         <i class="fas fa-times cursor-pointer" onclick="this.parentElement.remove();"></i>
      </div>
   <?php endforeach; ?>
<?php endif; ?>

<!-- Registration Form Section -->
<section class="py-12 flex flex-col items-center space-y-6 w-full max-w-lg mx-auto">

   <h3 class="text-2xl font-semibold text-center text-gray-800">Register New Admin</h3>
   
   <form action="" method="POST" class="space-y-6 w-full p-6 bg-white shadow-md rounded-lg border border-gray-200">

      <!-- Username Input -->
      <div>
         <input type="text" name="name" maxlength="20" required placeholder="Enter your username"
            class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary transition duration-200"
            oninput="this.value = this.value.replace(/\s/g, '')">
      </div>

      <!-- Password Input -->
      <div>
         <input type="password" name="pass" maxlength="20" required placeholder="Enter your password"
            class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary transition duration-200"
            oninput="this.value = this.value.replace(/\s/g, '')">
      </div>

      <!-- Confirm Password Input -->
      <div>
         <input type="password" name="cpass" maxlength="20" required placeholder="Confirm your password"
            class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary transition duration-200"
            oninput="this.value = this.value.replace(/\s/g, '')">
      </div>

      <!-- Register Button -->
      <div>
         <button type="submit" name="submit" 
            class="w-full py-3 rounded-md text-lg font-semibold text-white bg-gradient-to-r from-green-500 to-green-600 shadow-lg transform transition-all duration-300 ease-in-out hover:scale-105 hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            Register Now
         </button>
      </div>

   </form>
</section>

<!-- Custom JS File Link -->
<script src="../js/admin_script.js"></script>

</body>
</html>
