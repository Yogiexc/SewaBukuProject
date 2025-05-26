<?php
// buku_detail.php
include 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $sql = "SELECT * FROM buku WHERE idbuku = $id";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
?>
<div class="row">
    <div class="col-md-4 text-center">
        <?php if ($row['foto'] && file_exists($row['foto'])): ?>
            <img src="<?= htmlspecialchars($row['foto']) ?>" 
                 class="img-fluid rounded shadow" 
                 alt="<?= htmlspecialchars($row['judul']) ?>"
                 style="max-height: 300px;">
        <?php else: ?>
            <div class="bg-light rounded shadow d-flex align-items-center justify-content-center" 
                 style="height: 300px; width: 100%;">
                <i class="fas fa-book fa-5x text-muted"></i>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-md-8">
        <div class="book-detail-info">
            <h3 class="text-primary mb-3"><?= htmlspecialchars($row['judul']) ?></h3>
            
            <table class="table table-borderless">
                <tr>
                    <td width="30%"><i class="fas fa-barcode text-muted"></i> <strong>Kode Buku:</strong></td>
                    <td><?= htmlspecialchars($row['kode']) ?></td>
                </tr>
                <tr>
                    <td><i class="fas fa-user text-muted"></i> <strong>Pengarang:</strong></td>
                    <td><?= htmlspecialchars($row['pengarang']) ?></td>
                </tr>
                <tr>
                    <td><i class="fas fa-building text-muted"></i> <strong>Penerbit:</strong></td>
                    <td><?= htmlspecialchars($row['penerbit']) ?></td>
                </tr>
                <tr>
                    <td><i class="fas fa-boxes text-muted"></i> <strong>Stok:</strong></td>
                    <td>
                        <span class="badge <?= $row['stok'] > 0 ? 'bg-success' : 'bg-danger' ?> fs-6">
                            <?= $row['stok'] ?> <?= $row['stok'] > 0 ? 'Tersedia' : 'Habis' ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><i class="fas fa-calendar text-muted"></i> <strong>Ditambahkan:</strong></td>
                    <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                </tr>
                <?php if ($row['updated_at'] != $row['created_at']): ?>
                <tr>
                    <td><i class="fas fa-edit text-muted"></i> <strong>Diperbarui:</strong></td>
                    <td><?= date('d/m/Y H:i', strtotime($row['updated_at'])) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="d-flex justify-content-end gap-2">
            <a href="buku_edit.php?id=<?= $row['idbuku'] ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Buku
            </a>
            <button class="btn btn-danger" onclick="confirmDelete(<?= $row['idbuku'] ?>, '<?= htmlspecialchars($row['judul']) ?>')">
                <i class="fas fa-trash"></i> Hapus Buku
            </button>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, title) {
    if (confirm('Apakah Anda yakin ingin menghapus buku "' + title + '"?\n\nData yang dihapus tidak dapat dikembalikan!')) {
        window.location.href = 'buku_hapus.php?id=' + id;
    }
}
</script>

<?php
    } else {
        echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Buku tidak ditemukan!</div>';
    }
} else {
    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ID buku tidak valid!</div>';
}

$conn->close();
?>