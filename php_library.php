<?php
// php_library.php - Library of useful functions for the book management system

// Include config for database connection
include_once 'config.php';

/**
 * Book Management Library Functions
 * This file contains reusable functions for the Sewabuku system
 */

class BookLibrary {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Get all books with optional filters
     * @param array $filters - associative array of filters
     * @param int $limit - number of records to return
     * @param int $offset - offset for pagination
     * @return array
     */
    public function getAllBooks($filters = [], $limit = null, $offset = 0) {
        $sql = "SELECT * FROM buku WHERE 1=1";
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $this->conn->real_escape_string($filters['search']);
            $sql .= " AND (judul LIKE '%$search%' OR pengarang LIKE '%$search%' OR kode LIKE '%$search%' OR penerbit LIKE '%$search%')";
        }
        
        if (!empty($filters['stok_min'])) {
            $stok_min = (int)$filters['stok_min'];
            $sql .= " AND stok >= $stok_min";
        }
        
        if (!empty($filters['stok_max'])) {
            $stok_max = (int)$filters['stok_max'];
            $sql .= " AND stok <= $stok_max";
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $result = $this->conn->query($sql);
        $books = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }
        }
        
        return $books;
    }
    
    /**
     * Get book by ID
     * @param int $id
     * @return array|null
     */
    public function getBookById($id) {
        $id = (int)$id;
        $sql = "SELECT * FROM buku WHERE idbuku = $id";
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Get book by code
     * @param string $code
     * @return array|null
     */
    public function getBookByCode($code) {
        $code = $this->conn->real_escape_string($code);
        $sql = "SELECT * FROM buku WHERE kode = '$code'";
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Insert new book
     * @param array $data
     * @return bool|int - returns book ID on success, false on failure
     */
    public function insertBook($data) {
        $kode = $this->conn->real_escape_string($data['kode']);
        $judul = $this->conn->real_escape_string($data['judul']);
        $pengarang = $this->conn->real_escape_string($data['pengarang']);
        $penerbit = $this->conn->real_escape_string($data['penerbit']);
        $stok = (int)$data['stok'];
        $foto = isset($data['foto']) ? $this->conn->real_escape_string($data['foto']) : '';
        
        $sql = "INSERT INTO buku (kode, judul, pengarang, penerbit, stok, foto) 
                VALUES ('$kode', '$judul', '$pengarang', '$penerbit', $stok, '$foto')";
        
        if ($this->conn->query($sql) === TRUE) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    /**
     * Update book
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateBook($id, $data) {
        $id = (int)$id;
        $kode = $this->conn->real_escape_string($data['kode']);
        $judul = $this->conn->real_escape_string($data['judul']);
        $pengarang = $this->conn->real_escape_string($data['pengarang']);
        $penerbit = $this->conn->real_escape_string($data['penerbit']);
        $stok = (int)$data['stok'];
        $foto = isset($data['foto']) ? $this->conn->real_escape_string($data['foto']) : '';
        
        $sql = "UPDATE buku SET 
                kode = '$kode', 
                judul = '$judul', 
                pengarang = '$pengarang', 
                penerbit = '$penerbit', 
                stok = $stok, 
                foto = '$foto' 
                WHERE idbuku = $id";
        
        return $this->conn->query($sql) === TRUE;
    }
    
    /**
     * Delete book
     * @param int $id
     * @return bool
     */
    public function deleteBook($id) {
        $id = (int)$id;
        $sql = "DELETE FROM buku WHERE idbuku = $id";
        return $this->conn->query($sql) === TRUE;
    }
    
    /**
     * Check if book code exists
     * @param string $code
     * @param int $exclude_id - ID to exclude from check (for updates)
     * @return bool
     */
    public function codeExists($code, $exclude_id = null) {
        $code = $this->conn->real_escape_string($code);
        $sql = "SELECT idbuku FROM buku WHERE kode = '$code'";
        
        if ($exclude_id) {
            $exclude_id = (int)$exclude_id;
            $sql .= " AND idbuku != $exclude_id";
        }
        
        $result = $this->conn->query($sql);
        return $result && $result->num_rows > 0;
    }
    
    /**
     * Get statistics
     * @return array
     */
    public function getStatistics() {
        $stats = [];
        
        // Total books
        $result = $this->conn->query("SELECT COUNT(*) as total FROM buku");
        $stats['total_books'] = $result->fetch_assoc()['total'];
        
        // Available books
        $result = $this->conn->query("SELECT COUNT(*) as available FROM buku WHERE stok > 0");
        $stats['available_books'] = $result->fetch_assoc()['available'];
        
        // Out of stock
        $result = $this->conn->query("SELECT COUNT(*) as out_of_stock FROM buku WHERE stok = 0");
        $stats['out_of_stock'] = $result->fetch_assoc()['out_of_stock'];
        
        // Total stock
        $result = $this->conn->query("SELECT SUM(stok) as total_stock FROM buku");
        $stats['total_stock'] = $result->fetch_assoc()['total_stock'] ?? 0;
        
        // Low stock books (stok <= 2)
        $result = $this->conn->query("SELECT COUNT(*) as low_stock FROM buku WHERE stok <= 2 AND stok > 0");
        $stats['low_stock'] = $result->fetch_assoc()['low_stock'];
        
        return $stats;
    }
    
    /**
     * Get recent books
     * @param int $limit
     * @return array
     */
    public function getRecentBooks($limit = 5) {
        $limit = (int)$limit;
        $sql = "SELECT * FROM buku ORDER BY created_at DESC LIMIT $limit";
        $result = $this->conn->query($sql);
        
        $books = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }
        }
        
        return $books;
    }
    
    /**
     * Get low stock books
     * @param int $threshold
     * @return array
     */
    public function getLowStockBooks($threshold = 2) {
        $threshold = (int)$threshold;
        $sql = "SELECT * FROM buku WHERE stok <= $threshold AND stok > 0 ORDER BY stok ASC";
        $result = $this->conn->query($sql);
        
        $books = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }
        }
        
        return $books;
    }
    
    /**
     * Update stock
     * @param int $id
     * @param int $new_stock
     * @return bool
     */
    public function updateStock($id, $new_stock) {
        $id = (int)$id;
        $new_stock = (int)$new_stock;
        $sql = "UPDATE buku SET stok = $new_stock WHERE idbuku = $id";
        return $this->conn->query($sql) === TRUE;
    }
    
    /**
     * Search books with advanced options
     * @param string $query
     * @param array $options
     * @return array
     */
    public function searchBooks($query, $options = []) {
        $query = $this->conn->real_escape_string($query);
        $sql = "SELECT * FROM buku WHERE 1=1";
        
        if (!empty($query)) {
            $sql .= " AND (judul LIKE '%$query%' OR pengarang LIKE '%$query%' OR kode LIKE '%$query%' OR penerbit LIKE '%$query%')";
        }
        
        // Additional filters
        if (isset($options['min_stock'])) {
            $min_stock = (int)$options['min_stock'];
            $sql .= " AND stok >= $min_stock";
        }
        
        if (isset($options['max_stock'])) {
            $max_stock = (int)$options['max_stock'];
            $sql .= " AND stok <= $max_stock";
        }
        
        // Sorting
        $sort_by = isset($options['sort_by']) ? $options['sort_by'] : 'created_at';
        $sort_order = isset($options['sort_order']) ? $options['sort_order'] : 'DESC';
        
        $allowed_sorts = ['judul', 'pengarang', 'kode', 'stok', 'created_at'];
        if (in_array($sort_by, $allowed_sorts)) {
            $sql .= " ORDER BY $sort_by $sort_order";
        }
        
        // Pagination
        if (isset($options['limit'])) {
            $limit = (int)$options['limit'];
            $offset = isset($options['offset']) ? (int)$options['offset'] : 0;
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $result = $this->conn->query($sql);
        $books = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }
        }
        
        return $books;
    }
}

/**
 * Utility Functions
 */

/**
 * Format date to Indonesian format
 * @param string $date
 * @return string
 */
function formatDateIndonesian($date) {
    $months = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $timestamp = strtotime($date);
    $day = date('d', $timestamp);
    $month = $months[(int)date('m', $timestamp)];
    $year = date('Y', $timestamp);
    $time = date('H:i', $timestamp);
    
    return "$day $month $year $time";
}

/**
 * Generate unique book code
 * @param string $title
 * @return string
 */
function generateBookCode($title) {
    $words = explode(' ', $title);
    $code = '';
    
    // Take first letter of first 2 words
    for ($i = 0; $i < min(count($words), 2); $i++) {
        $code .= strtoupper(substr($words[$i], 0, 1));
    }
    
    // Add timestamp suffix
    $code .= date('ymd');
    
    return $code;
}

/**
 * Format number to Indonesian format
 * @param int $number
 * @return string
 */
function formatNumber($number) {
    return number_format($number, 0, ',', '.');
}

/**
 * Get stock status
 * @param int $stock
 * @return array
 */
function getStockStatus($stock) {
    if ($stock == 0) {
        return ['status' => 'Habis', 'class' => 'danger', 'icon' => 'times-circle'];
    } elseif ($stock <= 2) {
        return ['status' => 'Menipis', 'class' => 'warning', 'icon' => 'exclamation-triangle'];
    } else {
        return ['status' => 'Tersedia', 'class' => 'success', 'icon' => 'check-circle'];
    }
}

/**
 * Validate image file
 * @param array $file
 * @return bool|string
 */
function validateImage($file) {
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return 'Error uploading file';
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        return 'File type not allowed. Please use JPG, PNG, or GIF';
    }
    
    if ($file['size'] > $max_size) {
        return 'File size too large. Maximum 5MB allowed';
    }
    
    return true;
}

/**
 * Create thumbnail from image
 * @param string $source_path
 * @param string $thumb_path
 * @param int $width
 * @param int $height
 * @return bool
 */
function createThumbnail($source_path, $thumb_path, $width = 200, $height = 200) {
    if (!file_exists($source_path)) {
        return false;
    }
    
    $image_info = getimagesize($source_path);
    $image_type = $image_info[2];
    
    switch ($image_type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($source_path);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($source_path);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($source_path);
            break;
        default:
            return false;
    }
    
    $original_width = imagesx($source);
    $original_height = imagesy($source);
    
    $thumb = imagecreatetruecolor($width, $height);
    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $width, $height, $original_width, $original_height);
    
    switch ($image_type) {
        case IMAGETYPE_JPEG:
            imagejpeg($thumb, $thumb_path, 90);
            break;
        case IMAGETYPE_PNG:
            imagepng($thumb, $thumb_path, 9);
            break;
        case IMAGETYPE_GIF:
            imagegif($thumb, $thumb_path);
            break;
    }
    
    imagedestroy($source);
    imagedestroy($thumb);
    
    return true;
}

// Example usage:
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    // This code runs only when the file is accessed directly
    include 'config.php';
    
    $library = new BookLibrary($conn);
    
    // Example: Get statistics
    $stats = $library->getStatistics();
    
    // Example: Search books
    $books = $library->searchBooks('android', ['limit' => 5]);
    
    // Display results
    echo "<h1>Library Test</h1>";
    echo "<h2>Statistics:</h2>";
    echo "<pre>" . print_r($stats, true) . "</pre>";
    
    echo "<h2>Search Results for 'android':</h2>";
    echo "<pre>" . print_r($books, true) . "</pre>";
    
    $conn->close();
}