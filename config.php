<?php
// --- KONFIGURASI AWS S3 ---
// Ganti dengan kredensial IAM User Anda
// Sebaiknya gunakan IAM Role jika dijalankan di EC2, namun untuk development lokal/VM, IAM User bisa digunakan.
define('AWS_ACCESS_KEY_ID', 'GANTI_DENGAN_ACCESS_KEY_ANDA');
define('AWS_SECRET_ACCESS_KEY', 'GANTI_DENGAN_SECRET_KEY_ANDA');
define('AWS_SESSION_TOKEN', 'YOUR_SESSION_TOKEN');
define('AWS_REGION', 'YOUR_REGION'); // Ganti dengan region S3 Bucket Anda, contoh: ap-southeast-1 (Singapura)
define('S3_BUCKET', 'GANTI_DENGAN_NAMA_BUCKET_ANDA');

// --- KONFIGURASI DATABASE AWS RDS ---
$db_host = 'GANTI_DENGAN_ENDPOINT_RDS_ANDA'; // Endpoint dari RDS console
$db_user = 'admin'; // Username master yang Anda buat di RDS
$db_pass = 'PASSWORD_RDS_ANDA'; // Password master yang Anda buat di RDS
$db_name = 'db_sekolah'; // Nama database yang Anda buat di RDS

// Membuat koneksi ke database
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Cek koneksi
if ($mysqli->connect_error) {
    die("Koneksi database gagal: " . $mysqli->connect_error);
}

// Setup AWS SDK for PHP
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

$s3Client = new S3Client([
    'version' => 'latest',
    'region'  => AWS_REGION,
    'credentials' => [
        'key'    => AWS_ACCESS_KEY_ID,
        'secret' => AWS_SECRET_ACCESS_KEY,
        'token'  => AWS_SESSION_TOKEN,
    ]
]);

?>