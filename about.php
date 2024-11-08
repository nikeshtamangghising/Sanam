<?php
include 'components/connect.php';
session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>About Us - Sanam Clothing</title>

   <!-- Font Awesome CDN Link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- TailwindCSS CDN Link for modern design -->
   <script src="https://cdn.tailwindcss.com"></script>

   <!-- Custom CSS File -->
   <link rel="stylesheet" href="css/style.css">
</head>

<body class="bg-gray-50">

   <!-- Header Section Starts -->
   <?php include 'components/user_header.php'; ?>
   <!-- Header Section Ends -->

   <!-- About Section Starts -->
   <section class="max-w-6xl mx-auto px-6 py-16 space-y-12">
      <p class="text-lg text-center text-gray-600">
         <a href="home.php" class="text-indigo-600 hover:text-indigo-800">Home</a> <span>/</span> <span class="text-gray-500">About Us</span>
      </p>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
         <!-- Image Section -->
         <div class="flex justify-center items-center">
            <img src="project images/sanam.png" alt="Sanam Clothing" class="rounded-lg shadow-xl max-w-full h-auto object-cover">
         </div>

         <!-- Content Section -->
         <div class="flex flex-col justify-center space-y-6">
            <h3 class="text-3xl font-semibold text-gray-800">The Best Nepali Clothing Brand</h3>
            <p class="text-xl text-gray-700 leading-relaxed">
               Sanam is a Nepali clothing brand designed to bring a blend of tradition and modernity into fashion. Our vision is to offer a wide variety of stylish, high-quality apparel that resonates with Nepali heritage while embracing global trends. We believe in empowering individuals with clothing that speaks of style, comfort, and culture.
            </p>
            <p class="text-xl text-gray-700 leading-relaxed">
               Whether you're dressing up for a special occasion or looking for everyday wear, Sanam has something for every fashion-forward individual. Our designs are rooted in the rich cultural tapestry of Nepal, combined with cutting-edge fashion trends to provide the perfect wardrobe for any occasion.
            </p>
            <p class="text-xl text-gray-700 leading-relaxed">
               We are committed to providing you with high-quality fabrics, exceptional customer service, and timeless fashion that you'll love to wear again and again. Join the Sanam community and dress in style, comfort, and culture.
            </p>
         </div>
      </div>

      <!-- Testimonials Section -->
      <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-8 rounded-xl shadow-2xl">
        <h2 class="text-3xl font-bold text-center text-white mb-8">What Our Customers Say</h2>

        <!-- Customer Reviews -->
        <div id="testimonial-container" class="space-y-8">

          <!-- Testimonial 1 -->
          <div class="testimonial-item flex items-center space-x-8 p-6 bg-white rounded-xl shadow-lg transform hover:scale-105 transition duration-300">
            <img src="project images/person1.jpg" alt="Customer 1" class="rounded-full border-4 border-indigo-500 w-24 h-24 object-cover" />
            <div class="flex-1">
              <p class="text-2xl font-semibold text-gray-800">Sushila Rana</p>
              <div class="flex text-yellow-500 mb-4">
                <span>⭐</span>
                <span>⭐</span>
                <span>⭐</span>
                <span>⭐</span>
                <span>☆</span>
              </div>
              <p class="text-lg text-gray-700 mb-4">“I love the mix of tradition and modern style in Sanam's clothing. The fit is perfect and I get compliments every time I wear it!”</p>
              <span class="text-sm text-gray-500">05/22/2024</span>
            </div>
          </div>

          <!-- Testimonial 2 -->
          <div class="testimonial-item flex items-center space-x-8 p-6 bg-white rounded-xl shadow-lg transform hover:scale-105 transition duration-300">
            <img src="project images/person2.jpg" alt="Customer 2" class="rounded-full border-4 border-indigo-500 w-24 h-24 object-cover" />
            <div class="flex-1">
              <p class="text-2xl font-semibold text-gray-800">Rajesh Shrestha</p>
              <div class="flex text-yellow-500 mb-4">
                <span>⭐</span>
                <span>⭐</span>
                <span>⭐</span>
                <span>⭐</span>
                <span>⭐</span>
              </div>
              <p class="text-lg text-gray-700 mb-4">“The quality of fabric is unmatched, and the designs are always on point. I will definitely be buying more.”</p>
              <span class="text-sm text-gray-500">06/15/2024</span>
            </div>
          </div>

          <!-- Testimonial 3 -->
          <div class="testimonial-item flex items-center space-x-8 p-6 bg-white rounded-xl shadow-lg transform hover:scale-105 transition duration-300">
            <img src="project images/person3.jpg" alt="Customer 3" class="rounded-full border-4 border-indigo-500 w-24 h-24 object-cover" />
            <div class="flex-1">
              <p class="text-2xl font-semibold text-gray-800">Priya Koirala</p>
              <div class="flex text-yellow-500 mb-4">
                <span>⭐</span>
                <span>⭐</span>
                <span>⭐</span>
                <span>⭐</span>
                <span>⭐</span>
              </div>
              <p class="text-lg text-gray-700 mb-4">“The perfect blend of traditional and contemporary design. I wear Sanam for both casual and festive occasions!”</p>
              <span class="text-sm text-gray-500">07/10/2024</span>
            </div>
          </div>

          <!-- Testimonial 4 -->
          <div class="testimonial-item flex items-center space-x-8 p-6 bg-white rounded-xl shadow-lg transform hover:scale-105 transition duration-300">
            <img src="project images/person4.jpg" alt="Customer 4" class="rounded-full border-4 border-indigo-500 w-24 h-24 object-cover" />
            <div class="flex-1">
              <p class="text-2xl font-semibold text-gray-800">Mohan Lamsal</p>
              <div class="flex text-yellow-500 mb-4">
                <span>⭐</span>
                <span>⭐</span>
                <span>⭐</span>
                <span>☆</span>
                <span>☆</span>
              </div>
              <p class="text-lg text-gray-700 mb-4">“Great designs and good value for the price. I appreciate the customer service as well.”</p>
              <span class="text-sm text-gray-500">08/01/2024</span>
            </div>
          </div>

          <!-- Testimonial 5 -->
          <div class="testimonial-item flex items-center space-x-8 p-6 bg-white rounded-xl shadow-lg transform hover:scale-105 transition duration-300">
            <img src="project images/person5.jpg" alt="Customer 5" class="rounded-full border-4 border-indigo-500 w-24 h-24 object-cover" />
            <div class="flex-1">
              <p class="text-2xl font-semibold text-gray-800">Anjali Gurung</p>
              <div class="flex text-yellow-500 mb-4">
                <span>⭐</span>
                <span>⭐</span>
                <span>⭐</span>
                <span>⭐</span>
                <span>⭐</span>
              </div>
              <p class="text-lg text-gray-700 mb-4">“Absolutely amazing! Sanam clothing is now my go-to brand for both work and leisure.”</p>
              <span class="text-sm text-gray-500">09/12/2024</span>
            </div>
          </div>

        </div>

        <!-- Navigation Buttons -->
        <div class="flex justify-center space-x-6 mt-8">
          <button id="prev-btn" class="p-3 bg-indigo-700 text-white rounded-full hover:bg-indigo-600 transition duration-300">
            <span class="fa fa-arrow-left"></span>
          </button>
          <button id="next-btn" class="p-3 bg-indigo-700 text-white rounded-full hover:bg-indigo-600 transition duration-300">
            <span class="fa fa-arrow-right"></span>
          </button>
        </div>
      </div>
   </section>
   <!-- About Section Ends -->

   <!-- Footer Section Starts -->
   <?php include 'components/footer.php'; ?>
   <!-- Footer Section Ends -->

   <!-- JavaScript for Arrow Button Functionality -->
   <script>
     const prevBtn = document.getElementById('prev-btn');
     const nextBtn = document.getElementById('next-btn');
     const testimonialItems = document.querySelectorAll('.testimonial-item');

     let currentIndex = 0;

     const showTestimonial = (index) => {
       testimonialItems.forEach(item => item.classList.add('hidden'));
       testimonialItems[index].classList.remove('hidden');
     };

     showTestimonial(currentIndex);

     prevBtn.addEventListener('click', () => {
       currentIndex = (currentIndex === 0) ? testimonialItems.length - 1 : currentIndex - 1;
       showTestimonial(currentIndex);
     });

     nextBtn.addEventListener('click', () => {
       currentIndex = (currentIndex === testimonialItems.length - 1) ? 0 : currentIndex + 1;
       showTestimonial(currentIndex);
     });
   </script>

</body>

</html>
