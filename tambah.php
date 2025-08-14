<!DOCTYPE html>
<html>
<head>
    <title>Tambah Siswa</title>
</head>
<body>
    <h2>Tambah Siswa Baru</h2>
    <a href="index.php">Kembali ke Beranda</a><br/><br/>

    <form action="proses_tambah.php" method="post" enctype="multipart/form-data">
        <table width="25%" border="0">
            <tr> 
                <td>Nama</td>
                <td><input type="text" name="nama" required></td>
            </tr>
            <tr> 
                <td>Nomor Presensi</td>
                <td><input type="number" name="nomor_presensi" required></td>
            </tr>
            <tr> 
                <td>Kelas</td>
                <td><input type="text" name="kelas" required></td>
            </tr>
            <tr> 
                <td>Foto</td>
                <td><input type="file" name="foto" required accept="image/*"></td>
            </tr>
            <tr> 
                <td></td>
                <td><input type="submit" name="submit" value="Tambah"></td>
            </tr>
        </table>
    </form>
</body>
</html>