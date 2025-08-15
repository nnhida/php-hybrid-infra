How to deploy this app

1. Deploy di VM CentOS dengan Apache, Git, Docker
   ```bash
   sudo yum update -y
   sudo yum install -y git httpd yum-utils 
   sudo yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
   sudo yum install -y docker-ce docker-ce-cli containerd.io
2. Konfigurasi file config.php sesuai dengan credentials service AWS anda
3. Ubah menjadi container docker
4. Test 
