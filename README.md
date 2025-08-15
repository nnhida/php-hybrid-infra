How to deploy this app

Gambar alur kerja aplikasi
([UKK CC CS NSA.png](https://github.com/adinur21/crud-ukk/blob/main/UKK%20CC%20CS%20NSA.png))

1. Deploy di VM CentOS dengan Apache, Git, Docker
   ```bash
   sudo yum update -y
   sudo yum install -y git httpd yum-utils 
   sudo yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
   sudo yum install -y docker-ce docker-ce-cli containerd.io
2. Konfigurasi file config.php sesuai dengan credentials service AWS anda
3. SQL untuk membuat tabel
Jalankan query ini di database RDS Anda (misalnya melalui MySQL Workbench atau DBeaver) untuk membuat tabel siswa.
   ```bash
   CREATE DATABASE db_sekolah;
   USE db_sekolah;
   CREATE TABLE siswa (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(255) NOT NULL,
    nomor_presensi INT(11) NOT NULL,
    kelas VARCHAR(100) NOT NULL,
    foto_key VARCHAR(255) NULL -- Menyimpan key/nama file di S3
   );

3. Ubah menjadi container docker
4. Konfigurasi DNS di MikroTik
5. Test 
