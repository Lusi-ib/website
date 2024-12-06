<?php
session_start();
include_once "../konek.php"; // Pastikan file koneksi benar
$title = "Hapus Jurusan";
include_once "../template/header.php";

// Mengecek apakah ada parameter 'id_jurusan' di URL
if (!isset($_GET['id_jurusan'])) {
    echo "ID Jurusan tidak ditemukan!";
    exit;
}

$id_jurusan = $_GET['id_jurusan'];

// Validasi ID jurusan
if (empty($id_jurusan) || !is_numeric($id_jurusan)) {
    echo "ID Jurusan tidak valid!";
    exit;
}

// Query untuk mengambil data jurusan berdasarkan id_jurusan
$sql_check = "SELECT id_jurusan, nama_jurusan FROM jurusan WHERE id_jurusan = ?";
$stmt_check = $conn->prepare($sql_check);

if ($stmt_check) {
    $stmt_check->bind_param("i", $id_jurusan);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        // Data jurusan ditemukan, siap untuk dihapus
        $row = $result->fetch_assoc();
        $nama_jurusan = $row['nama_jurusan'];

        // Proses hapus data jurusan
        if (isset($_POST['confirm_delete'])) {
            // Query untuk menghapus data jurusan
            $sql_delete = "DELETE FROM jurusan WHERE id_jurusan = ?";
            $stmt_delete = $conn->prepare($sql_delete);

            if ($stmt_delete) {
                $stmt_delete->bind_param("i", $id_jurusan);
                $stmt_delete->execute();

                if ($stmt_delete->affected_rows > 0) {
                    echo "<p>Jurusan '$nama_jurusan' berhasil dihapus.</p>";
                    echo "<a href='data-jurusan.php'>Kembali ke Data Jurusan</a>";
                } else {
                    echo "<p>Gagal menghapus data jurusan.</p>";
                }

                $stmt_delete->close();
            } else {
                echo "Query tidak valid: " . $conn->error;
            }

            $conn->close();
            exit;
        }
    } else {
        echo "Data jurusan tidak ditemukan!";
        exit;
    }

    $stmt_check->close();
} else {
    echo "Query tidak valid: " . $conn->error;
    exit;
}

?>

<div class="dashboard">
    <!-- Sidebar -->
    <?php include_once '../template/sidebar.php'; ?>

    <div class="main-content">
        <?php include_once '../template/navbar.php'; ?>

        <!-- Content Area -->
        <div class="content">
            <h2>Hapus Jurusan</h2>

            <form method="POST" action="hapus-jurusan.php?id_jurusan=<?php echo $id_jurusan; ?>" class="form-confirm-delete">
                <p>Apakah Anda yakin ingin menghapus jurusan '<?php echo htmlspecialchars($nama_jurusan); ?>'?</p>
                <div class="form-group">
                    <button type="submit" name="confirm_delete" class="btn-submit">Ya, Hapus</button>
                    <a href="data-jurusan.php" class="btn-cancel">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once '../template/footer.php'; ?>
