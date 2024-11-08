<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script src="https://unpkg.com/unlazy@0.11.3/dist/unlazy.with-hashing.iife.js" defer init></script>
    <script type="text/javascript">
      window.tailwind.config = {
        darkMode: ['class'],
        theme: {
          extend: {
            colors: {
              border: 'hsl(var(--border))',
              input: 'hsl(var(--input))',
              ring: 'hsl(var(--ring))',
              background: 'hsl(var(--background))',
              foreground: 'hsl(var(--foreground))',
              primary: { DEFAULT: 'hsl(var(--primary))', foreground: 'hsl(var(--primary-foreground))' },
              secondary: { DEFAULT: 'hsl(var(--secondary))', foreground: 'hsl(var(--secondary-foreground))' },
              muted: { DEFAULT: 'hsl(var(--muted))', foreground: 'hsl(var(--muted-foreground))' },
            },
          }
        }
      }
    </script>
    <style type="text/tailwindcss">
      @layer base {
        :root {
          --background: 0 0% 100%;
          --foreground: 240 10% 3.9%;
          --primary: 240 5.9% 10%;
          --primary-foreground: 0 0% 98%;
          --secondary: 240 4.8% 95.9%;
          --secondary-foreground: 240 5.9% 10%;
        }
        .dark {
          --background: 240 10% 3.9%;
          --foreground: 0 0% 98%;
        }
      }
    </style>
  </head>
  <body>
    
    <footer class="bg-background text-foreground py-16">
      <div class="container mx-auto flex flex-col md:flex-row justify-between items-center space-y-8 md:space-y-0">
        <!-- Company Name & Social Media Icons -->
        <div class="flex flex-col items-center space-y-4">
          <!-- Company Name -->
          <span class="text-4xl font-extrabold tracking-wide uppercase">Sanam International</span>
          <!-- Social Media Icons -->
          <div class="flex space-x-6 text-2xl">
            <a href="#" class="text-muted-foreground hover:text-muted transition duration-200"><i class="fab fa-twitter"></i></a>
            <a href="#" class="text-muted-foreground hover:text-muted transition duration-200"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="text-muted-foreground hover:text-muted transition duration-200"><i class="fab fa-instagram"></i></a>
            <a href="#" class="text-muted-foreground hover:text-muted transition duration-200"><i class="fab fa-pinterest"></i></a>
          </div>
        </div>

        <!-- Navigation Links -->
        <div class="flex space-x-8 md:space-x-12 text-lg">
          <a href="#" class="text-muted-foreground hover:text-muted transition duration-200">About Us</a>
          <a href="#" class="text-muted-foreground hover:text-muted transition duration-200">Collections</a>
          <a href="#" class="text-muted-foreground hover:text-muted transition duration-200">Support</a>
          <a href="#" class="text-muted-foreground hover:text-muted transition duration-200">Contact</a>
        </div>

        <!-- Subscription Box -->
        <div class="flex items-center space-x-3">
          <input type="email" placeholder="Subscribe for Updates" class="border border-muted rounded-lg p-3 text-lg focus:outline-none focus:ring focus:ring-primary" />
          <button class="bg-secondary text-secondary-foreground hover:bg-secondary/80 rounded-lg px-6 py-2 transition duration-200">Join</button>
        </div>
      </div>
      <div class="text-center text-muted-foreground mt-8 text-sm">
        <span>© Sanam International. All rights reserved.</span>
      </div>
    </footer>

  </body>
</html>
