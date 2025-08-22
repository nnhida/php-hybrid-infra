<?php
include_once("config.php");

if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $nomor_presensi = $_POST['nomor_presensi'];
    $kelas = $_POST['kelas'];

    // Proses upload foto ke S3
    $foto_key = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $file = $_FILES['foto'];
        $foto_key = 'foto-siswa/' . uniqid() . '-' . basename($file['name']);

        try {
            $s3Client->putObject([
                'Bucket' => S3_BUCKET,
                'Key'    => $foto_key,
                'SourceFile' => $file['tmp_name'],
                // 'ACL'    => 'public-read', // Agar foto bisa diakses publik
            ]);
        } catch (S3Exception $e) {
            die("Gagal mengunggah foto ke S3: " . $e->getMessage());
        }
    }

    // Insert data ke database
    $stmt = $mysqli->prepare("INSERT INTO siswa(nama, nomor_presensi, kelas, foto_key) VALUES(?, ?, ?, ?)");
    $stmt->bind_param("siss", $nama, $nomor_presensi, $kelas, $foto_key);
    
    if ($stmt->execute()) {
        echo "Data siswa berhasil ditambahkan. ";
        echo "<a href='index.php'>Lihat Data Siswa</a>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
