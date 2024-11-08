<?php
if (isset($message)) {
   foreach ($message as $message) {
      echo '
      <div class="bg-red-500 text-white p-3 rounded-md mb-4 shadow-md flex justify-between items-center">
         <span>'.$message.'</span>
         <i class="fas fa-times cursor-pointer" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="bg-primary text-primary-foreground shadow-lg py-4">
   <div class="container mx-auto flex justify-between items-center px-4">
      <!-- Logo Section -->
      <a href="dashboard.php" class="text-2xl font-bold tracking-wide uppercase">
         Admin<span class="text-secondary">Panel</span>
      </a>

      <!-- Navigation Links -->
      <nav class="hidden md:flex space-x-6 text-lg">
         <a href="dashboard.php" class="hover:text-muted-foreground transition duration-200">Home</a>
         <a href="products.php" class="hover:text-muted-foreground transition duration-200">Products</a>
         <a href="placed_orders.php" class="hover:text-muted-foreground transition duration-200">Orders</a>
         <a href="admin_accounts.php" class="hover:text-muted-foreground transition duration-200">Admins</a>
         <a href="users_accounts.php" class="hover:text-muted-foreground transition duration-200">Users</a>
      </nav>

      <!-- Icons and Profile Section -->
      <div class="flex items-center space-x-4 relative">
         <div id="menu-btn" class="fas fa-bars text-2xl md:hidden cursor-pointer"></div>
         <div id="user-btn" class="fas fa-user text-2xl cursor-pointer" onclick="toggleProfileDropdown()"></div>

         <!-- Profile Dropdown (Initially hidden) -->
         <div id="profile-dropdown" class="hidden absolute top-16 right-4 bg-white rounded-lg shadow-xl w-64 z-20 transition-all duration-300 transform opacity-0 scale-95">
            <div class="p-4 border-b">
               <?php
                  $select_profile = $conn->prepare("SELECT * FROM `admin` WHERE id = ?");
                  $select_profile->execute([$admin_id]);
                  $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
               ?>
               <p class="font-semibold text-center text-gray-800"><?= $fetch_profile['name']; ?></p>
            </div>

            <div class="flex flex-col p-4 space-y-2">
               <a href="update_profile.php" class="flex items-center space-x-2 text-gray-700 hover:bg-gray-100 p-2 rounded-lg transition duration-200">
                  <i class="fas fa-user-edit text-blue-500"></i>
                  <span>Update Profile</span>
               </a>
               <a href="register_admin.php" class="flex items-center space-x-2 text-gray-700 hover:bg-gray-100 p-2 rounded-lg transition duration-200">
                  <i class="fas fa-user-plus text-purple-500"></i>
                  <span>Register</span>
               </a>
               <a href="../components/admin_logout.php" onclick="return confirm('Logout from this website?');" class="flex items-center space-x-2 text-red-600 hover:bg-red-100 p-2 rounded-lg transition duration-200">
                  <i class="fas fa-sign-out-alt"></i>
                  <span>Logout</span>
               </a>
            </div>
         </div>
      </div>
   </div>
</header>

<script>
   document.addEventListener('DOMContentLoaded', function () {
      const userBtn = document.getElementById('user-btn');
      const dropdown = document.getElementById('profile-dropdown');

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
         toggleDropdown();
      });

      // Close dropdown if clicked outside
      window.addEventListener('click', function(event) {
         if (!dropdown.contains(event.target) && event.target !== userBtn) {
            dropdown.style.opacity = '0';
            dropdown.style.transform = 'scale(0.95)';
            setTimeout(() => dropdown.classList.add('hidden'), 300);
         }
      });
   });
</script>
