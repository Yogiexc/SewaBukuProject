<?php
// buku_daftar.php
include 'config.php';

// Handle success/error messages
$message = '';
$message_type = '';

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'book_deleted':
            $message = 'Buku berhasil dihapus!';
            $message_type = 'success';
            break;
    }
}

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'invalid_id':
            $message = 'ID buku tidak valid!';
            $message_type = 'danger';
            break;
        case 'book_not_found':
            $message = 'Buku tidak ditemukan!';
            $message_type = 'danger';
            break;
    }
}

// Pagination
$limit = 12; // Books per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total books
$count_sql = "SELECT COUNT(*) as total FROM buku";
$count_result = $conn->query($count_sql);
$total_books = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_books / $limit);

// Get books with pagination
$sql = "SELECT * FROM buku ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Buku - Sewabuku</title>
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
                <p class="mb-0">Sistem Manajemen Perpustakaan</p>
            </div>
            
            <!-- Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container">
                    <div class="navbar-nav mx-auto">
                        <a class="nav-link" href="index.php"><i class="fas fa-home"></i> Dashboard</a>
                        <a class="nav-link" href="buku_input.php"><i class="fas fa-plus"></i> Input Buku</a>
                        <a class="nav-link" href="buku_cari.php"><i class="fas fa-search"></i> Cari Buku</a>
                        <a class="nav-link active" href="buku_daftar.php"><i class="fas fa-list"></i> Daftar Buku</a>
                    </div>
                </div>
            </nav>
            
            <div class="container mt-4">
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-0"><i class="fas fa-list"></i> Daftar Buku</h4>
                                    <small>Total: <?= $total_books ?> buku</small>
                                </div>
                                <a href="buku_input.php" class="btn btn-light">
                                    <i class="fas fa-plus"></i> Tambah Buku
                                </a>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($message)): ?>
                                    <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
                                        <i class="fas fa-<?= $message_type == 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                                        <?= $message ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Quick Actions -->
                                <div class="row mb-4">
                                    <div class="col-md-8">
                                        <div class="btn-group" role="group">
                                            <a href="buku_cari.php" class="btn btn-outline-primary">
                                                <i class="fas fa-search"></i> Cari Buku
                                            </a>
                                            <a href="buku_input.php" class="btn btn-outline-success">
                                                <i class="fas fa-plus"></i> Input Buku Baru
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <span class="text-muted">
                                            Halaman <?= $page ?> dari <?= $total_pages ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Books Grid -->
                                <div class="row">
                                    <?php if ($result && $result->num_rows > 0): ?>
                                        <?php while($row = $result->fetch_assoc()): ?>
                                            <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                                                <div class="card book-card h-100">
                                                    <div class="position-relative">
                                                        <?php if ($row['foto'] && file_exists($row['foto'])): ?>
                                                            <img src="<?= htmlspecialchars($row['foto']) ?>" 
                                                                 class="card-img-top book-image" 
                                                                 alt="<?= htmlspecialchars($row['judul']) ?>"
                                                                 style="height: 200px; object-fit: cover;">
                                                        <?php else: ?>
                                                            <div class="card-img-top book-image bg-light d-flex align-items-center justify-content-center" 
                                                                 style="height: 200px;">
                                                                <i class="fas fa-book fa-3x text-muted"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                        
                                                        <div class="position-absolute top-0 end-0 m-2">
                                                            <span class="badge <?= $row['stok'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                                                Stok: <?= $row['stok'] ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="card-body">
                                                        <h6 class="card-title text-truncate" title="<?= htmlspecialchars($row['judul']) ?>">
                                                            <?= htmlspecialchars($row['judul']) ?>
                                                        </h6>
                                                        <div class="book-info small">
                                                            <p class="mb-1 text-truncate">
                                                                <i class="fas fa-barcode text-muted"></i> 
                                                                <?= htmlspecialchars($row['kode']) ?>
                                                            </p>
                                                            <p class="mb-1 text-truncate">
                                                                <i class="fas fa-user text-muted"></i> 
                                                                <?= htmlspecialchars($row['pengarang']) ?>
                                                            </p>
                                                            <p class="mb-1 text-truncate">
                                                                <i class="fas fa-building text-muted"></i> 
                                                                <?= htmlspecialchars($row['penerbit']) ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="card-footer bg-transparent p-2">
                                                        <div class="btn-group w-100" role="group">
                                                            <button class="btn btn-info btn-sm" 
                                                                    onclick="showDetail(<?= $row['idbuku'] ?>)" 
                                                                    title="Lihat Detail">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <a href="buku_edit.php?id=<?= $row['idbuku'] ?>" 
                                                               class="btn btn-warning btn-sm" 
                                                               title="Edit Buku">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <button class="btn btn-danger btn-sm" 
                                                                    onclick="confirmDelete(<?= $row['idbuku'] ?>, '<?= htmlspecialchars($row['judul']) ?>')" 
                                                                    title="Hapus Buku">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <div class="col-12">
                                            <div class="text-center py-5">
                                                <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                                <h4 class="text-muted">Belum ada buku</h4>
                                                <p class="text-muted">
                                                    Mulai dengan <a href="buku_input.php">menambahkan buku pertama</a>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Pagination -->
                                <?php if ($total_pages > 1): ?>
                                    <nav aria-label="Page navigation" class="mt-4">
                                        <ul class="pagination justify-content-center">
                                            <?php if ($page > 1): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?= $page - 1 ?>">
                                                        <i class="fas fa-chevron-left"></i> Previous
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                            
                                            <?php
                                            $start = max(1, $page - 2);
                                            $end = min($total_pages, $page + 2);
                                            
                                            for ($i = $start; $i <= $end; $i++):
                                            ?>
                                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                                </li>
                                            <?php endfor; ?>
                                            
                                            <?php if ($page < $total_pages): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?= $page + 1 ?>">
                                                        Next <i class="fas fa-chevron-right"></i>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </nav>
                                <?php endif; ?>
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
        
        function confirmDelete(id, title) {
            if (confirm('Apakah Anda yakin ingin menghapus buku "' + title + '"?\n\nData yang dihapus tidak dapat dikembalikan!')) {
                window.location.href = 'buku_hapus.php?id=' + id;
            }
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>