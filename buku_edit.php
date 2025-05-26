<?php
// buku_edit.php
include 'config.php';

$message = '';
$message_type = '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: buku_daftar.php');
    exit;
}

// Get book data
$sql = "SELECT * FROM buku WHERE idbuku = $id";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    header('Location: buku_daftar.php');
    exit;
}

$book = $result->fetch_assoc();

// Handle form submission
if ($_POST) {
    $kode = clean_input($_POST['kode']);
    $judul = clean_input($_POST['judul']);
    $pengarang = clean_input($_POST['pengarang']);
    $penerbit = clean_input($_POST['penerbit']);
    $stok = (int)$_POST['stok'];
    
    // Check if code already exists (excluding current book)
    $check_sql = "SELECT idbuku FROM buku WHERE kode = '$kode' AND idbuku != $id";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $message = 'Kode buku sudah ada! Gunakan kode yang berbeda.';
        $message_type = 'danger';
    } else {
        // Handle file upload
        $foto_path = $book['foto']; // Keep existing photo by default
        
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $new_foto = upload_image($_FILES['foto']);
            if ($new_foto) {
                // Delete old photo if exists
                if ($book['foto'] && file_exists($book['foto'])) {
                    delete_image($book['foto']);
                }
                $foto_path = $new_foto;
            } else {
                $message = 'Gagal mengupload foto baru. Data lain tetap disimpan.';
                $message_type = 'warning';
            }
        }
        
        // Update book data
        $sql = "UPDATE buku SET 
                kode = '$kode', 
                judul = '$judul', 
                pengarang = '$pengarang', 
                penerbit = '$penerbit', 
                stok = $stok, 
                foto = '$foto_path' 
                WHERE idbuku = $id";
        
        if ($conn->query($sql) === TRUE) {
            $message = 'Data buku berhasil diperbarui!';
            $message_type = 'success';
            
            // Refresh book data
            $result = $conn->query("SELECT * FROM buku WHERE idbuku = $id");
            $book = $result->fetch_assoc();
        } else {
            $message = 'Error: ' . $conn->error;
            $message_type = 'danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku - Sewabuku</title>
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
                        <a class="nav-link" href="buku_daftar.php"><i class="fas fa-list"></i> Daftar Buku</a>
                    </div>
                </div>
            </nav>
            
            <div class="container mt-4">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card shadow">
                            <div class="card-header bg-warning text-dark">
                                <h4 class="mb-0">
                                    <i class="fas fa-edit"></i> Edit Buku
                                    <small class="ms-2 text-muted">(<?= htmlspecialchars($book['kode']) ?>)</small>
                                </h4>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($message)): ?>
                                    <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
                                        <i class="fas fa-<?= $message_type == 'success' ? 'check-circle' : ($message_type == 'danger' ? 'exclamation-circle' : 'exclamation-triangle') ?>"></i>
                                        <?= $message ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>
                                
                                <form method="POST" enctype="multipart/form-data" id="editBookForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="kode" class="form-label">
                                                    <i class="fas fa-barcode"></i> Kode Buku <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="kode" name="kode" 
                                                       maxlength="10" required 
                                                       value="<?= htmlspecialchars($book['kode']) ?>"
                                                       placeholder="Contoh: B001">
                                                <div class="form-text">Maksimal 10 karakter, harus unik</div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="stok" class="form-label">
                                                    <i class="fas fa-boxes"></i> Stok <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control" id="stok" name="stok" 
                                                       min="0" required 
                                                       value="<?= htmlspecialchars($book['stok']) ?>"
                                                       placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="judul" class="form-label">
                                            <i class="fas fa-book"></i> Judul Buku <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="judul" name="judul" 
                                               maxlength="50" required
                                               value="<?= htmlspecialchars($book['judul']) ?>"
                                               placeholder="Masukkan judul buku">
                                        <div class="form-text">Maksimal 50 karakter</div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="pengarang" class="form-label">
                                                    <i class="fas fa-user"></i> Pengarang <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="pengarang" name="pengarang" 
                                                       maxlength="50" required
                                                       value="<?= htmlspecialchars($book['pengarang']) ?>"
                                                       placeholder="Nama pengarang">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="penerbit" class="form-label">
                                                    <i class="fas fa-building"></i> Penerbit <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="penerbit" name="penerbit" 
                                                       maxlength="50" required
                                                       value="<?= htmlspecialchars($book['penerbit']) ?>"
                                                       placeholder="Nama penerbit">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="foto" class="form-label">
                                            <i class="fas fa-image"></i> Foto Sampul
                                        </label>
                                        
                                        <?php if ($book['foto'] && file_exists($book['foto'])): ?>
                                            <div class="current-image mb-2">
                                                <p class="text-muted mb-1">Foto saat ini:</p>
                                                <img src="<?= htmlspecialchars($book['foto']) ?>" 
                                                     class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                            </div>
                                        <?php endif; ?>
                                        
                                        <input type="file" class="form-control" id="foto" name="foto" 
                                               accept="image/*" onchange="previewImage(this)">
                                        <div class="form-text">Format: JPG, JPEG, PNG, GIF. Maksimal 5MB. Kosongkan jika tidak ingin mengubah foto.</div>
                                        <div id="imagePreview" class="mt-2"></div>
                                    </div>
                                    
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                                        <a href="buku_daftar.php" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                                        </a>
                                        <div>
                                            <button type="button" class="btn btn-danger me-2" onclick="confirmDelete()">
                                                <i class="fas fa-trash"></i> Hapus Buku
                                            </button>
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fas fa-save"></i> Update Buku
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Info Card -->
                        <div class="card mt-4 border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Buku</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Dibuat:</strong> <?= date('d/m/Y H:i', strtotime($book['created_at'])) ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Terakhir Diupdate:</strong> <?= date('d/m/Y H:i', strtotime($book['updated_at'])) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <div class="text-center">
                            <p class="text-muted mb-1">Preview foto baru:</p>
                            <img src="${e.target.result}" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                        </div>
                    `;
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        function confirmDelete() {
            if (confirm('Apakah Anda yakin ingin menghapus buku "<?= htmlspecialchars($book['judul']) ?>"?\n\nData yang dihapus tidak dapat dikembalikan!')) {
                window.location.href = 'buku_hapus.php?id=<?= $id ?>';
            }
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>