<?php 
$host = 'localhost';
$username = 'root';
$password = 'root';

try {
    //Connect to MySQL without selecting a database first
    $pdo = new PDO("mysql:host=$host", $username, $password);
    // Set the PDO error mode toexception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>Setting up Lost and Found Database...</h2>";
    //Create the database if it doesnt exist
    $sql = "CREATE DATABASE IF NOT EXISTS lost_and_found";
    $pdo->exec($sql);
    echo "<p>Database created successfully or already exists</p>";
    //Select the database
    $pdo->exec("USE lost_and_found");
    // Create the users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "<p>Users table created successfully or already exists</p>";
    // Create the items table
    $sql = "CREATE TABLE IF NOT EXISTS items (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        title VARCHAR(100) NOT NULL,
        description TEXT NOT NULL,
        location VARCHAR(100) NOT NULL,
        status ENUM('lost', 'found') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        date_posted DATETIME DEFAULT CURRENT_TIMESTAMP,
        image_path VARCHAR(255),
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    $pdo->exec($sql);
    echo "<p>Items table created successfully or already exists</p>";

    echo "<h2>Database setup completed successfully!</h2>";

} catch (PDOException $e) {
    echo "<h3>Error setting up database:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    die();
}

?>