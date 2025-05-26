<?php
// index.php
include 'config.php';

// Get statistics
$stats = array();

// Total books
$sql = "SELECT COUNT(*) as total FROM buku";
$result = $conn->query($sql);
$stats['total_books'] = $result->fetch_assoc()['total'];

// Available books (stok > 0)
$sql = "SELECT COUNT(*) as available FROM buku WHERE stok > 0";
$result = $conn->query($sql);
$stats['available_books'] = $result->fetch_assoc()['available'];

// Out of stock books
$sql = "SELECT COUNT(*) as out_of_stock FROM buku WHERE stok = 0";
$result = $conn->query($sql);
$stats['out_of_stock'] = $result->fetch_assoc()['out_of_stock'];

// Total stock
$sql = "SELECT SUM(stok) as total_stock FROM buku";
$result = $conn->query($sql);
$stats['total_stock'] = $result->fetch_assoc()['total_stock'] ?? 0;

// Recent books (last 5)
$sql = "SELECT * FROM buku ORDER BY created_at DESC LIMIT 5";
$recent_books = $conn->query($sql);

// Low stock books (stok <= 2)
$sql = "SELECT * FROM buku WHERE stok <= 2 AND stok > 0 ORDER BY stok ASC";
$low_stock_books = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sewabuku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="fas fa-book"></i> SEWABUKU</h1>
                <p class="mb-0">Sistem Manajemen Perpustakaan Modern</p>
            </div>
            
            <!-- Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container">
                    <div class="navbar-nav mx-auto">
                        <a class="nav-link active" href="index.php"><i class="fas fa-home"></i> Dashboard</a>
                        <a class="nav-link" href="buku_input.php"><i class="fas fa-plus"></i> Input Buku</a>
                        <a class="nav-link" href="buku_cari.php"><i class="fas fa-search"></i> Cari Buku</a>
                        <a class="nav-link" href="buku_daftar.php"><i class="fas fa-list"></i> Daftar Buku</a>
                    </div>
                </div>
            </nav>
            
            <div class="container mt-4">
                <!-- Welcome Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card bg-gradient-primary text-white">
                            <div class="card-body text-center py-5">
                                <h2><i class="fas fa-book-reader"></i> Selamat Datang di Sewabuku</h2>
                                <p class="lead mb-4">Kelola koleksi perpustakaan Anda dengan mudah dan efisien</p>
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 mb-2">
                                        <a href="buku_input.php" class="btn btn-light btn-lg w-100">
                                            <i class="fas fa-plus"></i><br>Input Buku
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-2">
                                        <a href="buku_cari.php" class="btn btn-light btn-lg w-100">
                                            <i class="fas fa-search"></i><br>Cari Buku
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-2">
                                        <a href="buku_daftar.php" class="btn btn-light btn-lg w-100">
                                            <i class="fas fa-list"></i><br>Daftar Buku
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-2">
                                        <a href="php_library.php" class="btn btn-light btn-lg w-100">
                                            <i class="fas fa-code"></i><br>Library
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card bg-primary text-white h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-book fa-3x mb-3"></i>
                                <h3 class="card-title"><?= number_format($stats['total_books']) ?></h3>
                                <p class="card-text">Total Buku</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card bg-success text-white h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle fa-3x mb-3"></i>
                                <h3 class="card-title"><?= number_format($stats['available_books']) ?></h3>
                                <p class="card-text">Buku Tersedia</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card bg-warning text-white h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                                <h3 class="card-title"><?= number_format($stats['out_of_stock']) ?></h3>
                                <p class="card-text">Stok Habis</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card bg-info text-white h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-boxes fa-3x mb-3"></i>
                                <h3 class="card-title"><?= number_format($stats['total_stock']) ?></h3>
                                <p class="card-text">Total Stok</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Books and Low Stock -->
                <div class="row">
                    <!-- Recent Books -->
                    <div class="col-lg-8 mb-4">
                        <div class="card shadow">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-clock"></i> Buku Terbaru
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if ($recent_books && $recent_books->num_rows > 0): ?>
                                    <div class="row">
                                        <?php while($book = $recent_books->fetch_assoc()): ?>
                                            <div class="col-md-4 mb-3">
                                                <div class="card book-card h-100">
                                                    <?php if ($book['foto'] && file_exists($book['foto'])): ?>
                                                        <img src="<?= htmlspecialchars($book['foto']) ?>" 
                                                             class="card-img-top" 
                                                             style="height: 150px; object-fit: cover;"
                                                             alt="<?= htmlspecialchars($book['judul']) ?>">
                                                    <?php else: ?>
                                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                                             style="height: 150px;">
                                                            <i class="fas fa-book fa-2x text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <div class="card-body p-2">
                                                        <h6 class="card-title text-truncate" title="<?= htmlspecialchars($book['judul']) ?>">
                                                            <?= htmlspecialchars($book['judul']) ?>
                                                        </h6>
                                                        <p class="card-text small text-muted mb-1">
                                                            <?= htmlspecialchars($book['pengarang']) ?>
                                                        </p>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <small class="text-muted">
                                                                Stok: <?= $book['stok'] ?>
                                                            </small>
                                                            <button class="btn btn-sm btn-outline-primary" 
                                                                    onclick="showDetail(<?= $book['idbuku'] ?>)">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                    <div class="text-center">
                                        <a href="buku_daftar.php" class="btn btn-primary">
                                            <i class="fas fa-list"></i> Lihat Semua Buku
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Belum ada buku yang ditambahkan</p>
                                        <a href="buku_input.php" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Tambah Buku Pertama
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Low Stock Alert -->
                    <div class="col-lg-4 mb-4">
                        <div class="card shadow">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">
                                    <i class="fas fa-exclamation-triangle"></i> Stok Menipis
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if ($low_stock_books && $low_stock_books->num_rows > 0): ?>
                                    <div class="list-group list-group-flush">
                                        <?php while($book = $low_stock_books->fetch_assoc()): ?>
                                            <div class="list-group-item d-flex justify-content-between align-items-start p-2">
                                                <div class="me-auto">
                                                    <h6 class="mb-1"><?= htmlspecialchars($book['judul']) ?></h6>
                                                    <small class="text-muted">
                                                        <?= htmlspecialchars($book['kode']) ?> - <?= htmlspecialchars($book['pengarang']) ?>
                                                    </small>
                                                </div>
                                                <span class="badge bg-warning rounded-pill">
                                                    <?= $book['stok'] ?>
                                                </span>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                    <div class="text-center mt-3">
                                        <small class="text-muted">Perlu restok segera!</small>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-3">
                                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                        <p class="text-muted mb-0">Semua buku memiliki stok yang cukup</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Quick Stats -->
                        <div class="card shadow mt-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-chart-pie"></i> Statistik Cepat
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6 border-end">
                                        <h4 class="text-success"><?= number_format(($stats['available_books'] / max($stats['total_books'], 1)) * 100, 1) ?>%</h4>
                                        <small class="text-muted">Buku Tersedia</small>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-warning"><?= number_format(($stats['out_of_stock'] / max($stats['total_books'], 1)) * 100, 1) ?>%</h4>
                                        <small class="text-muted">Stok Habis</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Detail Buku -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-book"></i> Detail Buku</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showDetail(id) {
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
            
            // Load detail via AJAX
            fetch('buku_detail.php?id=' + id)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('modalContent').innerHTML = data;
                })
                .catch(error => {
                    document.getElementById('modalContent').innerHTML = 
                        '<div class="alert alert-danger">Error loading data</div>';
                });
        }
        
        // Auto refresh every 5 minutes
        setTimeout(function() {
            location.reload();
        }, 300000);
    </script>
</body>
</html>

<?php $conn->close(); ?>