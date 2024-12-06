<?php
session_start();
include_once "../konek.php"; // Pastikan file koneksi benar
$title = "Edit Jurusan";
include_once "../template/header.php";

// Mengecek apakah ada parameter 'id_jurusan' di URL
if (!isset($_GET['id_jurusan'])) {
    echo "ID Jurusan tidak ditemukan!";
    exit;
}

$id_jurusan = $_GET['id_jurusan'];

// Query untuk mengambil data jurusan berdasarkan id_jurusan
$sql = "SELECT id_jurusan, nama_jurusan, id_sekolah
        FROM jurusan
        WHERE id_jurusan = ?";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $id_jurusan);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nama_jurusan = $row['nama_jurusan'];
        $id_sekolah = $row['id_sekolah'];
    } else {
        echo "Data jurusan tidak ditemukan!";
        exit;
    }
    
    $stmt->close();
} else {
    echo "Query tidak valid: " . $conn->error;
    exit;
}

// Ambil data sekolah untuk dropdown
$sql_sekolah = "SELECT id_sekolah, nama_sekolah FROM sekolah";
$result_sekolah = $conn->query($sql_sekolah); // Menjaga koneksi terbuka

// Jangan tutup koneksi di sini! Tutup koneksi setelah semua proses selesai
?>

<div class="dashboard">
    <!-- Sidebar -->
    <?php include_once '../template/sidebar.php'; ?>

    <div class="main-content">
        <?php include_once '../template/navbar.php'; ?>

        <!-- Content Area -->
        <div class="content">
            <h2>Edit Jurusan</h2>

            <!-- Form Edit Jurusan -->
            <form method="POST" action="edit-jurusan.php?id_jurusan=<?php echo $id_jurusan; ?>" class="form-edit">
                <div class="form-group">
                    <label for="nama_jurusan">Nama Jurusan:</label>
                    <input type="text" id="nama_jurusan" name="nama_jurusan" value="<?php echo htmlspecialchars($nama_jurusan); ?>" required>
                </div>

                <div class="form-group">
                    <label for="id_sekolah">Nama Sekolah:</label>
                    <select name="id_sekolah" id="id_sekolah" required>
                        <?php while ($row_sekolah = $result_sekolah->fetch_assoc()) { ?>
                            <option value="<?php echo $row_sekolah['id_sekolah']; ?>"
                                <?php if ($row_sekolah['id_sekolah'] == $id_sekolah) echo 'selected'; ?>>
                                <?php echo $row_sekolah['nama_sekolah']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" name="submit" class="btn-submit">Simpan Perubahan</button>
                </div>
            </form>

            <?php
            // Proses jika tombol submit ditekan
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
                $nama_jurusan = $_POST['nama_jurusan'];
                $id_sekolah = $_POST['id_sekolah'];

                // Validasi input
                if (empty($nama_jurusan) || empty($id_sekolah)) {
                    echo "<p>Data tidak boleh kosong!</p>";
                } else {
                    // Query untuk update data jurusan
                    $sql_update = "UPDATE jurusan SET nama_jurusan = ?, id_sekolah = ? WHERE id_jurusan = ?";

                    $stmt_update = $conn->prepare($sql_update);

                    if ($stmt_update) {
                        $stmt_update->bind_param("sii", $nama_jurusan, $id_sekolah, $id_jurusan);
                        $stmt_update->execute();

                        if ($stmt_update->affected_rows > 0) {
                            echo "<script>alert('Data berhasil diperbarui!'); window.location.href='data-jurusan.php';</script>";
                            
                        } else {
                            echo "<p>Data jurusan gagal diperbarui.</p>";
                        }

                        $stmt_update->close();
                    } else {
                        echo "Query tidak valid: " . $conn->error;
                    }
                }
            }
            ?>
        </div>
    </div>
</div>

<?php
// Tutup koneksi hanya setelah semua proses selesai
$conn->close();
?>

<?php include_once '../template/footer.php'; ?>
