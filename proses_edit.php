<?php
include_once("config.php");

if (isset($_POST['update'])) {
    // Ambil data dari form
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nama = isset($_POST['nama']) ? $_POST['nama'] : '';
    $nomor_presensi = isset($_POST['nomor_presensi']) ? (int)$_POST['nomor_presensi'] : 0;
    $kelas = isset($_POST['kelas']) ? $_POST['kelas'] : '';

    // Ambil foto lama dari DB
    $stmt = $mysqli->prepare("SELECT foto_key FROM siswa WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }
    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) {
        die("Execute failed (ambil foto lama): " . $stmt->error);
    }
    $stmt->bind_result($foto_key);
    $stmt->fetch();
    $stmt->close();

    // Cek apakah ada foto baru diupload
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $file = $_FILES['foto'];
        $new_foto_key = 'foto-siswa/' . uniqid() . '-' . basename($file['name']);

        try {
            // Upload ke S3
            $s3Client->putObject([
                'Bucket' => S3_BUCKET,
                'Key'    => $new_foto_key,
                'SourceFile' => $file['tmp_name'],
                // 'ACL' => 'public-read',
            ]);

            // Hapus foto lama kalau ada
            if ($foto_key) {
                $s3Client->deleteObject([
                    'Bucket' => S3_BUCKET,
                    'Key'    => $foto_key,
                ]);
            }

            $foto_key = $new_foto_key; // update ke foto baru
        } catch (S3Exception $e) {
            die("Gagal upload foto: " . $e->getMessage());
        }
    }

    // Update data di DB
    $stmt = $mysqli->prepare("UPDATE siswa SET nama = ?, nomor_presensi = ?, kelas = ?, foto_key = ? WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed (update): " . $mysqli->error);
    }

    $stmt->bind_param("sissi", $nama, $nomor_presensi, $kelas, $foto_key, $id);
    if ($stmt->execute()) {
        echo "Data siswa berhasil diupdate. <a href='index.php'>Lihat Data Siswa</a>";
    } else {
        echo "Error update: " . $stmt->error;
    }
    $stmt->close();
}
?>
