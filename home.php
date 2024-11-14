<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<div class="flex flex-col lg:flex-row">
    <aside class="w-full lg:w-1/4 p-4 bg-card border border-border rounded-lg">
        <!-- Filter Form -->
        <form id="filter-form">
            <h2 class="text-lg font-semibold">Category:</h2>
            <select name="category" class="border border-border rounded p-2 w-full">
                <option value="all">All</option>
                <option value="hat">Hat</option>
                <option value="one-piece">One Piece</option>
                <option value="saree">Saree</option>
                <option value="kurta">Kurta</option>
                <option value="shawl">Shawl</option>
                <option value="top">Top</option>
                <option value="t-shirt">T-Shirt</option>
                <option value="pant">Pant</option>
                <option value="socks">Socks</option>
                <option value="heels">Heels</option>
                <option value="boots">Boots</option>
                <option value="bra-panty">Bra & Panty</option>
                <option value="handkerchief">Handkerchief</option>
                <option value="muffler">Muffler</option>
            </select>

            <h3 class="mt-4 text-lg font-semibold">Filter by:</h3>
            <h4 class="font-medium">Gender</h4>
            <select name="gender" class="border border-border rounded p-2 w-full">
                <option value="all">All</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="child">Child</option>
            </select>

            <h4 class="mt-4 font-medium">Color</h4>
            <select name="color" class="border border-border rounded p-2 w-full">
                <option value="all">All</option>
                <option value="red">Red</option>
                <option value="blue">Blue</option>
                <option value="green">Green</option>
                <option value="yellow">Yellow</option>
                <option value="black">Black</option>
                <option value="white">White</option>
                <option value="purple">Purple</option>
                <option value="pink">Pink</option>
                <option value="orange">Orange</option>
                <option value="gray">Gray</option>
                <option value="brown">Brown</option>
                <option value="indigo">Indigo</option>
            </select>

            <h4 class="mt-4 font-medium">Size</h4>
            <select name="size" class="border border-border rounded p-2 w-full">
                <option value="all">All</option>
                <option value="xs">XS</option>
                <option value="s">S</option>
                <option value="m">M</option>
                <option value="l">L</option>
                <option value="xl">XL</option>
            </select>

            <h4 class="mt-4 font-medium">Sort by</h4>
            <select name="sort_by" class="border border-border rounded p-2 w-full">
                <option value="popular">Most Popular</option>
                <option value="price_low">Price: Low to High</option>
                <option value="price_high">Price: High to Low</option>
            </select>

            <button type="submit" class="mt-4 bg-primary text-primary-foreground py-2 px-4 rounded w-full">Apply</button>
        </form>
    </aside>

    <main class="w-full lg:w-3/4 p-4" id="product-list">
        <!-- Products will be loaded here using AJAX -->
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Load all products by default when the page loads
    loadProducts();

    document.getElementById('filter-form').addEventListener('submit', function(e) {
        e.preventDefault();  // Prevent form submission
        loadProducts();  // Call to load products based on selected filters
    });

    function loadProducts() {
        const formData = new FormData(document.getElementById('filter-form'));

        // Send the AJAX request
        fetch('load_products.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.error) {
                    console.error(data.error);  // Show any error from PHP
                } else {
                    renderProducts(data);
                }
            } catch (error) {
                console.error('Error parsing JSON:', error);
            }
        })
        .catch(error => {
            console.error('Error fetching products:', error);
        });
    }

    function renderProducts(products) {
        const container = document.getElementById('product-list');
        container.innerHTML = '';  // Clear previous products
        console.log('Received products:', products);

        if (products.length === 0) {
            container.innerHTML = '<p>No products found.</p>';
        } else {
            container.innerHTML = `
                <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4">
                    ${products.map(product => `
                        <div class="bg-white border border-gray-300 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                            <img src="uploaded_img/${product.product_image}" alt="${product.product_name}" class="w-full h-48 object-cover rounded-t-lg" />
                            <div class="p-4">
                                <h2 class="text-lg font-semibold text-gray-800">${product.product_name}</h2>
                                <p class="text-sm text-gray-600 mt-1">Size: ${product.size}</p>
                                <p class="text-sm text-gray-600 mt-1">Gender: ${product.gender}</p>
                                <p class="text-sm text-gray-600 mt-1">Color:  ${product.color}</p>
                                <p class="text-sm text-gray-600 mt-1">Weight: ${product.weight}</p>
                                <p class="text-sm text-gray-600 mt-1">Price: Rs ${product.price}</p>
                                <button class="add-to-cart mt-4 bg-blue-600 text-white hover:bg-blue-700 py-2 px-4 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-400" 
                                        data-product-id="${product.product_id}" data-variant-id="${product.variant_id}" ">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        }
        attachAddToCartEvent();
    }

    function attachAddToCartEvent() {
        document.getElementById('product-list').addEventListener('click', function (event) {
            if (event.target.classList.contains('add-to-cart')) {
                const button = event.target;
                const product_id = button.getAttribute('data-product-id');
                const variant_id = button.getAttribute('data-variant-id');

                const data = {
                    user_id: <?php echo json_encode($user_id); ?>,
                    product_id: product_id,
                    variant_id: variant_id,
                    quantity: 1
                };

                fetch('components/add_cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Product added to cart!');
                        location.reload();
                    } else {
                        alert('Something went wrong. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error adding product to cart.');
                });
            }
        });
    }

    // Attach event listener after DOM is ready
    attachAddToCartEvent();
});
</script>

<?php include 'components/footer.php'; ?>
</body>
</html>
