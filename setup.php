<?php
require_once 'database/db_connect.php';

try {
    // Create users table first (if it doesn't exist)
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create products table
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        image_url VARCHAR(255),
        category VARCHAR(50),
        stock INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create cart table
    $pdo->exec("CREATE TABLE IF NOT EXISTS cart (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )");

    // Create orders table
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'paid', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
        payment_method VARCHAR(50),
        shipping_address TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Create order_items table
    $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )");

    // Insert some sample products
    $sample_products = [
        [
            'name' => 'Sport T-shirt',
            'description' => 'Comfortable sport t-shirt for your workouts',
            'price' => 30.00,
            'image_url' => '../images/Rectangle%20310.png',
            'category' => 'Clothing',
            'stock' => 100
        ],
        [
            'name' => 'Stringer Cutoffshirt',
            'description' => 'Stylish stringer for your training sessions',
            'price' => 30.00,
            'image_url' => '../images/Rectangle%20310%20(1).png',
            'category' => 'Clothing',
            'stock' => 50
        ],
        [
            'name' => 'Jacket',
            'description' => 'Premium training jacket',
            'price' => 130.00,
            'image_url' => '../images/Rectangle%20310%20(2).png',
            'category' => 'Clothing',
            'stock' => 30
        ],
        [
            'name' => 'Backpack',
            'description' => 'Durable gym backpack',
            'price' => 30.00,
            'image_url' => '../images/Rectangle%20310%20(3).png',
            'category' => 'Accessories',
            'stock' => 40
        ],
        [
            'name' => 'Protein Powder',
            'description' => 'High-quality protein supplement',
            'price' => 130.00,
            'image_url' => '../images/Rectangle%20318%20(2).png',
            'category' => 'Nutrition',
            'stock' => 60
        ],
        [
            'name' => 'Mass Gainer',
            'description' => 'Premium mass gainer supplement',
            'price' => 230.00,
            'image_url' => '../images/Rectangle%20319%20(2).png',
            'category' => 'Nutrition',
            'stock' => 45
        ],
        [
            'name' => 'Creatine',
            'description' => 'Pure creatine monohydrate',
            'price' => 120.00,
            'image_url' => '../images/Rectangle%20320%20(2).png',
            'category' => 'Nutrition',
            'stock' => 70
        ],
        [
            'name' => 'Dumbbells Set',
            'description' => 'Professional dumbbells set',
            'price' => 200.00,
            'image_url' => '../images/Rectangle%20318.png',
            'category' => 'Equipment',
            'stock' => 25
        ]
    ];

    // Check if products table is empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        // Insert sample products
        $stmt = $pdo->prepare("
            INSERT INTO products (name, description, price, image_url, category, stock)
            VALUES (:name, :description, :price, :image_url, :category, :stock)
        ");

        foreach ($sample_products as $product) {
            $stmt->execute($product);
        }
        echo "Sample products added successfully!<br>";
    }

    echo "Database tables created successfully!";
} catch(PDOException $e) {
    echo "Error creating tables: " . $e->getMessage();
}
?> 