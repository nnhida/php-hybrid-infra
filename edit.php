<?php
include_once("config.php");

// Cek apakah parameter id ada di URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Ambil data siswa dari database berdasarkan id
$result = $mysqli->query("SELECT * FROM siswa WHERE id=$id");
$data = $result->fetch_assoc();

if (!$data) {
    echo "Data tidak ditemukan!";
    exit();
}

$nama = $data['nama'];
$nomor_presensi = $data['nomor_presensi'];
$kelas = $data['kelas'];
$foto_key = $data['foto_key'];
$foto_url = 'https://' . S3_BUCKET . '.s3.' . AWS_REGION . '.amazonaws.com/' . $foto_key;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Siswa</title>
    <style>
        img { width: 150px; height: auto; display: block; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h2>Edit Data Siswa</h2>
    <a href="index.php">Kembali ke Beranda</a><br/><br/>

    <form action="proses_edit.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="hidden" name="old_foto_key" value="<?php echo $foto_key; ?>">
        
        <table width="25%" border="0">
            <tr> 
                <td>Nama</td>
                <td><input type="text" name="nama" value="<?php echo htmlspecialchars($nama); ?>" required></td>
            </tr>
            <tr> 
                <td>Nomor Presensi</td>
                <td><input type="number" name="nomor_presensi" value="<?php echo htmlspecialchars($nomor_presensi); ?>" required></td>
            </tr>
            <tr> 
                <td>Kelas</td>
                <td><input type="text" name="kelas" value="<?php echo htmlspecialchars($kelas); ?>" required></td>
            </tr>
            <tr> 
                <td>Foto Saat Ini</td>
                <td><img src="<?php echo $foto_url; ?>" alt="Foto saat ini"></td>
            </tr>
            <tr> 
                <td>Ganti Foto (Opsional)</td>
                <td><input type="file" name="foto" accept="image/*"></td>
            </tr>
            <tr> 
                <td></td>
                <td><input type="submit" name="update" value="Update"></td>
            </tr>
        </table>
    </form>
</body>
</html>