<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = 'root'; // MAMP default password

try {
    // Connect to MySQL without selecting a database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Setting up Lost and Found database...</h2>";
    
    // Create the database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS lost_and_found";
    $pdo->exec($sql);
    echo "<p>Database created successfully or already exists.</p>";
    
    // Select the database
    $pdo->exec("USE lost_and_found");
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "<p>Users table created successfully or already exists.</p>";
    
    // Create items table
    $sql = "CREATE TABLE IF NOT EXISTS items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(100) NOT NULL,
        description TEXT,
        status ENUM('lost', 'found') NOT NULL,
        location VARCHAR(255),
        date_posted DATETIME DEFAULT CURRENT_TIMESTAMP,
        image_path VARCHAR(255),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "<p>Items table created successfully or already exists.</p>";
    
    echo "<h3>Database setup completed successfully!</h3>";
    echo "<p>You can now <a href='index.php'>go to the homepage</a>.</p>";
    
} catch(PDOException $e) {
    echo "<h3>Error setting up database:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    die();
}
?>