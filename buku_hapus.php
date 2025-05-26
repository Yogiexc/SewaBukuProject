<?php
// buku_hapus.php
include 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: buku_daftar.php?error=invalid_id');
    exit;
}

// Get book data first
$sql = "SELECT * FROM buku WHERE idbuku = $id";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    header('Location: buku_daftar.php?error=book_not_found');
    exit;
}

$book = $result->fetch_assoc();

// Handle confirmation
if (isset($_POST['confirm_delete'])) {
    // Delete the book
    $delete_sql = "DELETE FROM buku WHERE idbuku = $id";
    
    if ($conn->query($delete_sql) === TRUE) {
        // Delete associated image file
        if ($book['foto'] && file_exists($book['foto'])) {
            delete_image($book['foto']);
        }
        
        header('Location: buku_daftar.php?success=book_deleted');
        exit;
    } else {
        $error = $conn->error;
    }
} elseif (isset($_POST['cancel_delete'])) {
    header('Location: buku_daftar.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Buku - Sewabuku</title>
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
            
            <div class="container mt-4">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card shadow border-danger">
                            <div class="card-header bg-danger text-white">
                                <h4 class="mb-0">
                                    <i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus Buku
                                </h4>
                            </div>
                            <div class="card-body">
                                <?php if (isset($error)): ?>
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-circle"></i>
                                        Error: <?= htmlspecialchars($error) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="text-center mb-4">
                                    <?php if ($book['foto'] && file_exists($book['foto'])): ?>
                                        <img src="<?= htmlspecialchars($book['foto']) ?>" 
                                             class="img-thumbnail mb-3" 
                                             style="max-width: 200px; max-height: 200px;">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" 
                                             style="width: 200px; height: 200px; margin: 0 auto;">
                                            <i class="fas fa-book fa-3x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <h5 class="text-danger"><?= htmlspecialchars($book['judul']) ?></h5>
                                    <p class="text-muted">
                                        Kode: <?= htmlspecialchars($book['kode']) ?><br>
                                        Pengarang: <?= htmlspecialchars($book['pengarang']) ?><br>
                                        Penerbit: <?= htmlspecialchars($book['penerbit']) ?><br>
                                        Stok: <?= htmlspecialchars($book['stok']) ?>
                                    </p>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Peringatan!</strong><br>
                                    Apakah Anda yakin ingin menghapus buku ini?<br>
                                    <strong>Data yang dihapus tidak dapat dikembalikan!</strong>
                                </div>
                                
                                <form method="POST">
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                        <button type="submit" name="cancel_delete" class="btn btn-secondary btn-lg">
                                            <i class="fas fa-times"></i> Batal
                                        </button>
                                        <button type="submit" name="confirm_delete" class="btn btn-danger btn-lg">
                                            <i class="fas fa-trash"></i> Ya, Hapus Buku
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="buku_daftar.php" class="btn btn-link">
                                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Buku
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>