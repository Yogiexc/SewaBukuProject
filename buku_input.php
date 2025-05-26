<?php
// buku_input.php
include 'config.php';

$message = '';
$message_type = '';

// Handle form submission
if ($_POST) {
    $kode = clean_input($_POST['kode']);
    $judul = clean_input($_POST['judul']);
    $pengarang = clean_input($_POST['pengarang']);
    $penerbit = clean_input($_POST['penerbit']);
    $stok = (int)$_POST['stok'];
    
    // Check if code already exists
    $check_sql = "SELECT idbuku FROM buku WHERE kode = '$kode'";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $message = 'Kode buku sudah ada! Gunakan kode yang berbeda.';
        $message_type = 'danger';
    } else {
        // Handle file upload
        $foto_path = '';
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $foto_path = upload_image($_FILES['foto']);
            if (!$foto_path) {
                $message = 'Gagal mengupload foto. Format yang diizinkan: JPG, JPEG, PNG, GIF';
                $message_type = 'warning';
            }
        }
        
        if ($message_type != 'warning') {
            // Insert new book
            $sql = "INSERT INTO buku (kode, judul, pengarang, penerbit, stok, foto) 
                    VALUES ('$kode', '$judul', '$pengarang', '$penerbit', $stok, '$foto_path')";
            
            if ($conn->query($sql) === TRUE) {
                $message = 'Buku berhasil ditambahkan!';
                $message_type = 'success';
                // Clear form data after successful insert
                $_POST = array();
            } else {
                $message = 'Error: ' . $conn->error;
                $message_type = 'danger';
                // Delete uploaded file if database insert failed
                if ($foto_path) {
                    delete_image($foto_path);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Buku - Sewabuku</title>
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
                        <a class="nav-link active" href="buku_input.php"><i class="fas fa-plus"></i> Input Buku</a>
                        <a class="nav-link" href="buku_cari.php"><i class="fas fa-search"></i> Cari Buku</a>
                        <a class="nav-link" href="buku_daftar.php"><i class="fas fa-list"></i> Daftar Buku</a>
                    </div>
                </div>
            </nav>
            
            <div class="container mt-4">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card shadow">
                            <div class="card-header bg-success text-white">
                                <h4 class="mb-0"><i class="fas fa-plus"></i> Input Buku Baru</h4>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($message)): ?>
                                    <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
                                        <i class="fas fa-<?= $message_type == 'success' ? 'check-circle' : ($message_type == 'danger' ? 'exclamation-circle' : 'exclamation-triangle') ?>"></i>
                                        <?= $message ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>
                                
                                <form method="POST" enctype="multipart/form-data" id="bookForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="kode" class="form-label">
                                                    <i class="fas fa-barcode"></i> Kode Buku <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="kode" name="kode" 
                                                       maxlength="10" required 
                                                       value="<?= isset($_POST['kode']) ? htmlspecialchars($_POST['kode']) : '' ?>"
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
                                                       value="<?= isset($_POST['stok']) ? htmlspecialchars($_POST['stok']) : '' ?>"
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
                                               value="<?= isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : '' ?>"
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
                                                       value="<?= isset($_POST['pengarang']) ? htmlspecialchars($_POST['pengarang']) : '' ?>"
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
                                                       value="<?= isset($_POST['penerbit']) ? htmlspecialchars($_POST['penerbit']) : '' ?>"
                                                       placeholder="Nama penerbit">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="foto" class="form-label">
                                            <i class="fas fa-image"></i> Foto Sampul
                                        </label>
                                        <input type="file" class="form-control" id="foto" name="foto" 
                                               accept="image/*" onchange="previewImage(this)">
                                        <div class="form-text">Format: JPG, JPEG, PNG, GIF. Maksimal 5MB</div>
                                        <div id="imagePreview" class="mt-2"></div>
                                    </div>
                                    
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button type="reset" class="btn btn-secondary" onclick="clearPreview()">
                                            <i class="fas fa-undo"></i> Reset Form
                                        </button>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i> Simpan Buku
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Tips Card -->
                        <div class="card mt-4 border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-lightbulb"></i> Tips Input Buku</h6>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li>Pastikan kode buku unik dan mudah diingat</li>
                                    <li>Gunakan foto sampul dengan kualitas baik untuk tampilan yang menarik</li>
                                    <li>Isi semua field yang wajib (bertanda *)</li>
                                    <li>Periksa kembali data sebelum menyimpan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div