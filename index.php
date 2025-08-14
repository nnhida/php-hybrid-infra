<?php include_once("config.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Siswa</title>
    <style>
        body { font-family: sans-serif; margin: 40px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        img { width: 100px; height: auto; }
        a { text-decoration: none; padding: 5px 10px; background-color: #007bff; color: white; border-radius: 3px; }
        .btn-edit { background-color: #ffc107; }
        .btn-delete { background-color: #dc3545; }
    </style>
</head>
<body>

<h2>Daftar Siswa</h2>
<a href="tambah.php">Tambah Siswa Baru</a><br><br>

<table>
    <tr>
        <th>Nama</th>
        <th>No. Presensi</th>
        <th>Kelas</th>
        <th>Foto</th>
        <th>Aksi</th>
    </tr>
    <?php
    $result = $mysqli->query("SELECT * FROM siswa ORDER BY id DESC");
    while ($row = $result->fetch_assoc()) {
        $foto_url = 'https://' . S3_BUCKET . '.s3.' . AWS_REGION . '.amazonaws.com/' . $row['foto_key'];
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nomor_presensi']) . "</td>";
        echo "<td>" . htmlspecialchars($row['kelas']) . "</td>";
        echo "<td><img src='" . $foto_url . "' alt='Foto " . htmlspecialchars($row['nama']) . "'></td>";
        echo "<td>
                <a href='edit.php?id=" . $row['id'] . "' class='btn-edit'>Edit</a>
                <a href='hapus.php?id=" . $row['id'] . "&key=" . $row['foto_key'] . "' onclick='return confirm(\"Yakin ingin menghapus data ini?\")' class='btn-delete'>Hapus</a>
              </td>";
        echo "</tr>";
    }
    ?>
</table>

</body>
</html>