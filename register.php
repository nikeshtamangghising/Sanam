<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
}

if(isset($_POST['submit'])){
   $message = [];

   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
   $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
   $pass = $_POST['pass'];
   $cpass = $_POST['cpass'];

   // Using password_hash for security
   if($pass !== $cpass){
      $message[] = 'Confirm password not matched!';
   } else {
      $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

      $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? OR number = ?");
      $select_user->execute([$email, $number]);

      if($select_user->rowCount() > 0){
         $message[] = 'Email or number already exists!';
      } else {
         $insert_user = $conn->prepare("INSERT INTO `users`(name, email, number, password) VALUES(?,?,?,?)");
         $insert_user->execute([$name, $email, $number, $hashed_pass]);

         $_SESSION['user_id'] = $conn->lastInsertId();
         header('location:home.php');
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
   <title>Register</title>

   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Custom CSS -->
   <link rel="stylesheet" href="css/style.css">

   <!-- Tailwind CSS -->
   <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
   <style type="text/tailwindcss">
      /* Add any additional Tailwind customizations here */
   </style>
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="form-container flex flex-col items-center justify-center min-h-screen  bg-background">
   <h1 class="text-6xl font-bold mb-10">Register Now</h1>
   <form class="w-full max-w-lg space-y-8 mt-6" action="" method="post">
      <input type="text" name="name" placeholder="Enter your name" class="w-full py-5 px-6 border border-border rounded-lg text-xl focus:outline-none focus:ring focus:ring-ring" maxlength="50" required>
      <input type="email" name="email" placeholder="Enter your email" class="w-full py-5 px-6 border border-border rounded-lg text-xl focus:outline-none focus:ring focus:ring-ring" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')" required>
      <input type="text" name="number" placeholder="Enter your number" class="w-full py-5 px-6 border border-border rounded-lg text-xl focus:outline-none focus:ring focus:ring-ring" maxlength="10" required>
      <input type="password" name="pass" placeholder="Enter your password" class="w-full py-5 px-6 border border-border rounded-lg text-xl focus:outline-none focus:ring focus:ring-ring" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')" required>
      <input type="password" name="cpass" placeholder="Confirm your password" class="w-full py-5 px-6 border border-border rounded-lg text-xl focus:outline-none focus:ring focus:ring-ring" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')" required>
      <button type="submit" name="submit" class="w-full py-5 px-6 rounded-xl text-xl font-bold text-white bg-gradient-to-r from-blue-500 to-purple-600 shadow-lg hover:shadow-xl hover:from-blue-600 hover:to-purple-700 transform hover:scale-105 transition duration-300">Register Now</button>
      <p class="mt-8 text-lg text-muted-foreground">Already have an account? <a href="login.php" class="text-primary">Login now</a></p>
   </form>
</section>



<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>