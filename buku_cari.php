<?php
// buku_cari.php
include 'config.php';

// Get search query
$search = isset($_POST['search']) ? clean_input($_POST['search']) : '';

// Build SQL query
$sql = "SELECT * FROM buku WHERE 1=1";
if (!empty($search)) {
    $sql .= " AND (judul LIKE '%$search%' OR pengarang LIKE '%$search%' OR kode LIKE '%$search%' OR penerbit LIKE '%$search%')";
}
$sql .= " ORDER BY created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Buku - Sewabuku</title>
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
                        <a class="nav-link active" href="buku_cari.php"><i class="fas fa-search"></i> Cari Buku</a>
                        <a class="nav-link" href="buku_daftar.php"><i class="fas fa-list"></i> Daftar Buku</a>
                    </div>
                </div>
            </nav>
            
            <div class="container mt-4">
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-header bg-primary text-white">
                                <h4 class="mb-0"><i class="fas fa-search"></i> Cari Buku</h4>
                            </div>
                            <div class="card-body">
                                <!-- Search Form -->
                                <form method="POST" class="mb-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-lg" name="search" 
                                               placeholder="Masukkan judul, pengarang, kode, atau penerbit..." 
                                               value="<?= htmlspecialchars($search) ?>">
                                        <button class="btn btn-primary btn-lg" type="submit">
                                            <i class="fas fa-search"></i> Cari
                                        </button>
                                    </div>
                                </form>
                                
                                <?php if (!empty($search)): ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> 
                                        Hasil pencarian untuk: <strong>"<?= htmlspecialchars($search) ?>"</strong>
                                        <a href="buku_cari.php" class="btn btn-sm btn-outline-primary ms-2">
                                            <i class="fas fa-times"></i> Reset
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Search Results -->
                                <div class="row">
                                    <?php if ($result && $result->num_rows > 0): ?>
                                        <?php while($row = $result->fetch_assoc()): ?>
                                            <div class="col-md-6 col-lg-4 mb-4">
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
                                                            <span class="badge bg-primary">Stok: <?= $row['stok'] ?></span>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="card-body">
                                                        <h5 class="card-title text-truncate" title="<?= htmlspecialchars($row['judul']) ?>">
                                                            <?= htmlspecialchars($row['judul']) ?>
                                                        </h5>
                                                        <div class="book-info">
                                                            <p class="mb-1">
                                                                <i class="fas fa-barcode text-muted"></i> 
                                                                <strong>Kode:</strong> <?= htmlspecialchars($row['kode']) ?>
                                                            </p>
                                                            <p class="mb-1">
                                                                <i class="fas fa-user text-muted"></i> 
                                                                <strong>Pengarang:</strong> <?= htmlspecialchars($row['pengarang']) ?>
                                                            </p>
                                                            <p class="mb-1">
                                                                <i class="fas fa-building text-muted"></i> 
                                                                <strong>Penerbit:</strong> <?= htmlspecialchars($row['penerbit']) ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="card-footer bg-transparent">
                                                        <div class="d-grid gap-2">
                                                            <button class="btn btn-info btn-sm" 
                                                                    onclick="showDetail(<?= $row['idbuku'] ?>)">
                                                                <i class="fas fa-eye"></i> Lihat Detail
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <div class="col-12">
                                            <div class="text-center py-5">
                                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                                <h4 class="text-muted">Tidak ada buku yang ditemukan</h4>
                                                <p class="text-muted">
                                                    <?php if (!empty($search)): ?>
                                                        Coba gunakan kata kunci yang berbeda atau 
                                                        <a href="buku_cari.php">lihat semua buku</a>
                                                    <?php else: ?>
                                                        Belum ada buku dalam database. 
                                                        <a href="buku_input.php">Tambah buku baru</a>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
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
    </script>
</body>
</html>

<?php $conn->close(); ?>