<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
   exit;
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_message = $conn->prepare("DELETE FROM `messages` WHERE id = ?");
   $delete_message->execute([$delete_id]);
   header('location:messages.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Messages</title>

   <!-- Font Awesome CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Tailwind CSS CDN -->
   <script src="https://cdn.tailwindcss.com"></script>

   <!-- Custom CSS -->
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body class="bg-gray-50 text-gray-800">

<?php include '../components/admin_header.php'; ?>

<!-- Messages Section -->
<section class="py-12">
   <div class="container mx-auto px-4">
      <h1 class="text-3xl font-semibold text-center mb-8">Messages</h1>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
         <?php
         $select_messages = $conn->prepare("SELECT * FROM `messages`");
         $select_messages->execute();
         if ($select_messages->rowCount() > 0) {
            while ($fetch_messages = $select_messages->fetch(PDO::FETCH_ASSOC)) {
         ?>
         <div class="bg-white p-6 rounded-lg shadow-md">
            <p class="text-lg font-medium text-gray-800 mb-2">
               <span class="font-semibold text-blue-600">Name:</span> <?= htmlspecialchars($fetch_messages['name']); ?>
            </p>
            <p class="text-lg font-medium text-gray-800 mb-2">
               <span class="font-semibold text-blue-600">Number:</span> <?= htmlspecialchars($fetch_messages['number']); ?>
            </p>
            <p class="text-lg font-medium text-gray-800 mb-2">
               <span class="font-semibold text-blue-600">Email:</span> <?= htmlspecialchars($fetch_messages['email']); ?>
            </p>
            <p class="text-lg text-gray-600 mb-4">
               <span class="font-semibold text-blue-600">Message:</span> <?= htmlspecialchars($fetch_messages['message']); ?>
            </p>
            <a href="messages.php?delete=<?= $fetch_messages['id']; ?>" class="inline-block bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-500 transition duration-300 text-center w-full" onclick="return confirm('Delete this message?');">Delete</a>
         </div>
         <?php
            }
         } else {
            echo '<p class="text-center text-gray-500 col-span-full">You have no messages</p>';
         }
         ?>
      </div>
   </div>
</section>

<!-- Custom JS -->
<script src="../js/admin_script.js"></script>

</body>
</html>
