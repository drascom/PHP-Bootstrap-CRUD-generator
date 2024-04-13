<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "root";
if (isset($config_database) && !empty($config_database)) {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $config_database);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    // Create table_options table if not exists
    $create_table_query = "CREATE TABLE IF NOT EXISTS table_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_name VARCHAR(255) NOT NULL UNIQUE,
    options TEXT NOT NULL
    )";
    $conn->query($create_table_query);

} else {
    // Create connection
    $conn = new mysqli($servername, $username, $password);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
}

// Close connection
// $conn->close();
?>