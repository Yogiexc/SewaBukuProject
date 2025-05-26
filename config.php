<?php
// config.php
$host = "localhost";
$username = "root";
$password = "";
$database = "sewabuku";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8");

// Database setup (uncomment and run once to create database and table)
/*
CREATE DATABASE IF NOT EXISTS sewabuku;
USE sewabuku;

CREATE TABLE IF NOT EXISTS buku (
    idbuku INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(10) NOT NULL UNIQUE,
    judul VARCHAR(50) NOT NULL,
    pengarang VARCHAR(50) NOT NULL,
    penerbit VARCHAR(50) NOT NULL,
    stok INT(10) NOT NULL DEFAULT 0,
    foto VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO buku (kode, judul, pengarang, penerbit, stok, foto) VALUES
('001', 'LiveCoding 9 Aplikasi Android', 'Arif Akbarul Huda', 'Elex Media', 5, 'uploads/android.jpg'),
('002', 'Menguasai Rahasia Pembuatan Virus', 'Agha Abdurrahman Nuruddin', 'Andi Offset', 3, 'uploads/virus.jpg');
*/

// Function to sanitize input
function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = $conn->real_escape_string($data);
    return $data;
}

// Function to upload image
function upload_image($file) {
    $upload_dir = "uploads/";
    
    // Create uploads directory if not exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (in_array($file_extension, $allowed_types)) {
        $new_filename = uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            return $upload_path;
        }
    }
    return false;
}

// Function to delete image
function delete_image($image_path) {
    if ($image_path && file_exists($image_path)) {
        unlink($image_path);
        return true;
    }
    return false;
}
?>