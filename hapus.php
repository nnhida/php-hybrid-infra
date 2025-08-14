<?php
include_once("config.php");

// Cek apakah parameter id dan key ada di URL
if (!isset($_GET['id']) || !isset($_GET['key'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$foto_key = $_GET['key'];

// 1. Hapus file dari S3 Bucket
if (!empty($foto_key)) {
    try {
        $s3Client->deleteObject([
            'Bucket' => S3_BUCKET,
            'Key'    => $foto_key,
        ]);
    } catch (S3Exception $e) {
        // Jika gagal menghapus file, proses tetap lanjut untuk menghapus data db
        // Anda bisa menambahkan logging error di sini
        error_log("Gagal menghapus file dari S3: " . $e->getMessage());
    }
}


// 2. Hapus data dari database
$stmt = $mysqli->prepare("DELETE FROM siswa WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Redirect kembali ke halaman utama setelah berhasil
    header("Location: index.php");
    exit();
} else {
    echo "Error saat menghapus data: " . $stmt->error;
}
$stmt->close();
?>