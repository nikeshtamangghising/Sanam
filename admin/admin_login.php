<?php
include '../components/connect.php';

session_start();

if (isset($_POST['submit'])) {
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ? AND password = ?");
   $select_admin->execute([$name, $pass]);

   if ($select_admin->rowCount() > 0) {
      $fetch_admin_id = $select_admin->fetch(PDO::FETCH_ASSOC);
      $_SESSION['admin_id'] = $fetch_admin_id['id'];
      header('location:dashboard.php');
   } else {
      $message[] = 'Incorrect username or password!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Login</title>
   <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<!-- Error message display -->
<?php if (isset($message)): ?>
   <?php foreach ($message as $msg): ?>
      <div class="fixed top-5 left-1/2 transform -translate-x-1/2 bg-red-500 text-white px-4 py-2 rounded-md flex items-center space-x-2">
         <span><?php echo $msg; ?></span>
         <i class="fas fa-times cursor-pointer" onclick="this.parentElement.remove();"></i>
      </div>
   <?php endforeach; ?>
<?php endif; ?>

<!-- Login form section -->
<section class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">
   <form action="" method="POST" class="space-y-6">
      <h3 class="text-2xl font-semibold text-center text-gray-800">Login Now</h3>
      
      <input type="text" name="name" maxlength="20" required placeholder="Enter your username" 
             class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary transition duration-200" 
             oninput="this.value = this.value.replace(/\s/g, '')">
      
      <input type="password" name="pass" maxlength="20" required placeholder="Enter your password" 
             class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary transition duration-200" 
             oninput="this.value = this.value.replace(/\s/g, '')">
      
      <!-- Updated Login Button -->
      <button type="submit" name="submit" 
              class="w-full py-3 rounded-md text-lg font-semibold text-white bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg transform transition-all duration-300 ease-in-out hover:scale-105 hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
         Login Now
      </button>
   </form>
</section>

</body>
</html>
