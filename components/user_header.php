<?php


// Include the database connection
include 'connect.php';

// Initialize variables
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$total_cart_items = 0;

// Check if user is logged in, and fetch cart item count
if ($user_id) {
    try {
        // Prepare and execute the query to count the number of items in the user's cart
        $count_cart_items = $conn->prepare("SELECT COUNT(*) AS cart_count FROM `cart` WHERE user_id = ?");
        $count_cart_items->execute([$user_id]);
        $result = $count_cart_items->fetch(PDO::FETCH_ASSOC);
        $total_cart_items = $result['cart_count']; // Get the total number of items
    } catch (Exception $e) {
        // Handle any errors during the query execution
        error_log("Error fetching cart count: " . $e->getMessage());
    }
}
?>

<header class="bg-white text-black shadow-lg py-2">
   <div class="container mx-auto flex justify-between items-center px-4">
      <!-- Logo Section -->
      <a href="home.php" class="logo text-2xl font-bold text-black hover:text-blue-800 flex items-center space-x-2">
         <img src="project images/logo.png" alt="Sanam Logo" class="w-16 h-16">
         <span>SANAM</span>
      </a>

      <!-- Navigation Links -->
      <nav class="navbar hidden md:flex space-x-6 text-lg">
         <a href="home.php" class="text-black hover:text-muted-foreground transition duration-200">Home</a>
         <a href="about.php" class="text-black hover:text-muted-foreground transition duration-200">About</a>
         <a href="orders.php" class="text-black hover:text-muted-foreground transition duration-200">Orders</a>
      </nav>

      <!-- Icons and Profile Section -->
      <div class="flex items-center space-x-4 relative">
         

         <!-- Cart Icon -->
         <a href="cart.php" class="relative text-black">
            <i class="fas fa-shopping-cart text-2xl cursor-pointer"></i>
            <span class="absolute top-0 right-0 text-xs bg-red-500 text-white rounded-full px-2"><?= $total_cart_items; ?></span>
         </a>

         <!-- User Profile Icon -->
         <div id="user-btn" class="fas fa-user text-2xl cursor-pointer text-black"></div>

         <!-- Profile Dropdown (Initially hidden) -->
         <div id="profile-dropdown" class="hidden absolute top-16 right-4 bg-white rounded-lg shadow-xl w-64 z-20 transition-all duration-300 transform opacity-0 scale-95">
            <div class="p-4 border-b">
               <?php
               if ($user_id) {
                   try {
                       // Fetch user profile if user is logged in
                       $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
                       $select_profile->execute([$user_id]);
                       
                       if ($select_profile->rowCount() > 0) {
                           $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                           echo "<p class='font-semibold text-center text-gray-800'>{$fetch_profile['name']}</p>";
                       } else {
                           echo "<p class='text-center text-gray-700'>Profile not found</p>";
                       }
                   } catch (Exception $e) {
                       // Handle error
                       echo "<p class='text-center text-gray-700'>Error fetching profile: {$e->getMessage()}</p>";
                   }
               } else {
                   echo "<p class='text-center text-gray-700'>Please login first!</p>";
               }
               ?>
            </div>

            <div class="flex flex-col p-4 space-y-2">
               <a href="profile.php" class="flex items-center space-x-2 text-gray-700 hover:bg-gray-100 p-2 rounded-lg transition duration-200">
                  <i class="fas fa-user-edit text-blue-500"></i>
                  <span>Update Profile</span>
               </a>
               <a href="components/user_logout.php" onclick="return confirm('Logout from this website?');" class="flex items-center space-x-2 text-red-600 hover:bg-red-100 p-2 rounded-lg transition duration-200">
                  <i class="fas fa-sign-out-alt"></i>
                  <span>Logout</span>
               </a>
            </div>
         </div>
      </div>
   </div>
</header>

<!-- Add to Cart Popup (Toast Notification) -->
<div id="cart-popup" class="fixed bottom-5 right-5 bg-green-500 text-white p-4 rounded-lg shadow-lg hidden">
   <p>Item added to cart!</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const userBtn = document.getElementById('user-btn');
    const dropdown = document.getElementById('profile-dropdown');
    const cartPopup = document.getElementById('cart-popup');

    // Function to show and hide the dropdown with animation
    function toggleDropdown() {
        if (dropdown.classList.contains('hidden')) {
            dropdown.classList.remove('hidden');
            dropdown.style.opacity = '1';
            dropdown.style.transform = 'scale(1)';
        } else {
            dropdown.style.opacity = '0';
            dropdown.style.transform = 'scale(0.95)';
            setTimeout(() => dropdown.classList.add('hidden'), 300); // Hides completely after transition
        }
    }

    // Open and close dropdown on user icon click
    userBtn.addEventListener('click', function(event) {
        event.stopPropagation(); // Prevents window listener from closing it
        if (dropdown.classList.contains('hidden')) {
            if (!userLoggedIn()) {
                window.location.href = 'login.php'; // Redirect to login page if not logged in
            } else {
                toggleDropdown();
            }
        }
    });

    // Close dropdown if clicked outside
    window.addEventListener('click', function(event) {
        if (!dropdown.contains(event.target) && event.target !== userBtn) {
            dropdown.style.opacity = '0';
            dropdown.style.transform = 'scale(0.95)';
            setTimeout(() => dropdown.classList.add('hidden'), 300);
        }
    });

    // Function to check if the user is logged in
    function userLoggedIn() {
        // Implement your own logic here to check if user is logged in, e.g., checking session
        return <?= $user_id ? 'true' : 'false'; ?>;
    }

    // Function to show cart popup
    function showCartPopup() {
        cartPopup.classList.remove('hidden');
        setTimeout(() => cartPopup.classList.add('hidden'), 3000); // Hide after 3 seconds
    }

    // Triggering Add to Cart action
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            showCartPopup(); // Show the popup when an item is added to the cart
        });
    });
});
</script>
