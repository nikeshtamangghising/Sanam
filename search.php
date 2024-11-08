<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include 'components/add_cart.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Search Page</title>

   <!-- Font Awesome CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Tailwind CSS (via CDN) -->
   <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="bg-gray-50 text-gray-800">

<!-- Header section starts -->
<?php include 'components/user_header.php'; ?>
<!-- Header section ends -->

<!-- Search form section starts -->
<section class="py-8 px-4 bg-white shadow-md">
   <div class="max-w-4xl mx-auto flex justify-center items-center">
      <form method="post" action="" class="w-full flex items-center space-x-2">
         <input type="text" name="search_box" placeholder="Search products..." class="w-full p-3 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
         <button type="submit" name="search_btn" class="text-blue-500 hover:text-blue-700">
            <i class="fas fa-search text-2xl"></i>
         </button>
      </form>
   </div>
</section>
<!-- Search form section ends -->

<!-- Products section starts -->
<section class="py-12 px-4">
   <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">

      <?php
         if(isset($_POST['search_box']) OR isset($_POST['search_btn'])){
         $search_box = $_POST['search_box'];
         $select_products = $conn->prepare("SELECT * FROM `products` WHERE name LIKE '%{$search_box}%'");
         $select_products->execute();
         if($select_products->rowCount() > 0){
            while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){
      ?>
      <form action="" method="post" class="bg-white rounded-lg shadow-md hover:shadow-lg transition-all">
         <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
         <input type="hidden" name="name" value="<?= $fetch_products['name']; ?>">
         <input type="hidden" name="price" value="<?= $fetch_products['price']; ?>">
         <input type="hidden" name="image" value="<?= $fetch_products['image']; ?>">
         <button type="submit" class="absolute top-2 right-2 text-white bg-blue-500 hover:bg-blue-600 rounded-full p-2" name="add_to_cart">
            <i class="fas fa-shopping-cart"></i>
         </button>
         <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="<?= $fetch_products['name']; ?>" class="w-full h-56 object-cover rounded-t-lg">
         <div class="p-4">
            <a href="category.php?category=<?= $fetch_products['category']; ?>" class="text-sm text-blue-500 hover:text-blue-700"><?= $fetch_products['category']; ?></a>
            <h3 class="text-xl font-semibold text-gray-800 mt-2"><?= $fetch_products['name']; ?></h3>
            <div class="flex items-center justify-between mt-2">
               <div class="text-lg font-semibold text-gray-700">Rs. <?= $fetch_products['price']; ?></div>
               <input type="number" name="qty" class="w-16 p-2 border rounded-md text-center" min="1" max="99" value="1">
            </div>
         </div>
      </form>
      <?php
            }
         } else {
            echo '<p class="col-span-4 text-center text-gray-500">No products found!</p>';
         }
      }
      ?>

   </div>
</section>
<!-- Products section ends -->

<!-- Footer section starts -->
<?php include 'components/footer.php'; ?>
<!-- Footer section ends -->

<!-- Custom JS -->
<script src="js/script.js"></script>

</body>
</html>
