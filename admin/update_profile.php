<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
}

if (isset($_POST['submit'])) {

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);

   if (!empty($name)) {
      $select_name = $conn->prepare("SELECT * FROM `admin` WHERE name = ?");
      $select_name->execute([$name]);
      if ($select_name->rowCount() > 0) {
         $message[] = 'username already taken!';
      } else {
         $update_name = $conn->prepare("UPDATE `admin` SET name = ? WHERE id = ?");
         $update_name->execute([$name, $admin_id]);
      }
   }

   $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
   $select_old_pass = $conn->prepare("SELECT password FROM `admin` WHERE id = ?");
   $select_old_pass->execute([$admin_id]);
   $fetch_prev_pass = $select_old_pass->fetch(PDO::FETCH_ASSOC);
   $prev_pass = $fetch_prev_pass['password'];
   $old_pass = sha1($_POST['old_pass']);
   $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING);
   $new_pass = sha1($_POST['new_pass']);
   $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
   $confirm_pass = sha1($_POST['confirm_pass']);
   $confirm_pass = filter_var($confirm_pass, FILTER_SANITIZE_STRING);

   if ($old_pass != $empty_pass) {
      if ($old_pass != $prev_pass) {
         $message[] = 'old password not matched!';
      } elseif ($new_pass != $confirm_pass) {
         $message[] = 'confirm password not matched!';
      } else {
         if ($new_pass != $empty_pass) {
            $update_pass = $conn->prepare("UPDATE `admin` SET password = ? WHERE id = ?");
            $update_pass->execute([$confirm_pass, $admin_id]);
            $message[] = 'password updated successfully!';
         } else {
            $message[] = 'please enter a new password!';
         }
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
   <title>Profile Update</title>

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <!-- Tailwind CSS CDN link -->
   <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
</head>
<body class="bg-background text-foreground">

<?php include '../components/admin_header.php' ?>

<!-- Admin Profile Update Section -->
<section class="py-12">
   <div class="container mx-auto px-4">
      <h1 class="text-4xl font-bold text-center mb-10">Update Profile</h1>

      <!-- Form Container -->
      <div class="max-w-lg mx-auto bg-white p-8 rounded-lg shadow-md">
         
         <!-- Update Form -->
         <form action="" method="POST">
            <div class="mb-6">
               <label for="name" class="block text-sm font-semibold mb-2">Username</label>
               <input type="text" name="name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" 
                      placeholder="<?= $fetch_profile['name']; ?>" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
            </div>

            <div class="mb-6">
               <label for="old_pass" class="block text-sm font-semibold mb-2">Old Password</label>
               <input type="password" name="old_pass" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" 
                      placeholder="Enter your old password" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
            </div>

            <div class="mb-6">
               <label for="new_pass" class="block text-sm font-semibold mb-2">New Password</label>
               <input type="password" name="new_pass" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" 
                      placeholder="Enter your new password" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
            </div>

            <div class="mb-6">
               <label for="confirm_pass" class="block text-sm font-semibold mb-2">Confirm New Password</label>
               <input type="password" name="confirm_pass" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" 
                      placeholder="Confirm your new password" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
            </div>

            <button type="submit" name="submit" 
        class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-500 transition duration-300 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-300">
   Update Now
</button>
         </form>
      </div>
   </div>
</section>

<!-- Custom JS File Link -->
<script src="../js/admin_script.js"></script>

</body>
</html>
