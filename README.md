# Deploying a PHP Website on CentOS VM with Docker, AWS RDS, S3, and MikroTik DNS

This guide explains how to deploy a PHP-based web application on a CentOS virtual machine using Docker. It connects to AWS RDS for database storage, uses Amazon S3 for image storage, and configures domain resolution using a MikroTik router.

---

## 1. Install CentOS on a Virtual Machine

1. Download **CentOS 9 ISO** from the official website.
2. Create a new VM using **VirtualBox** or **VMware**.
3. Boot from the ISO and install CentOS on the VM.

---

## 2. Configure Virtual Network Adapters

To enable internet access and communication with the host machine:

1. In the VM settings under **Network**:

   * **Adapter 1 (NAT)**: provides internet access.
   * **Adapter 2 (Host-Only Adapter)**: allows communication with the host system.

---

## 3. Configure Networking in CentOS

1. Check your current IP address:

   ```bash
   ip a
   ```

2. Use the terminal UI to configure your network:

   ```bash
   nmtui
   ```

   * Edit or add a connection.
   * Set **IPv4** to **Manual**.
   * Assign a static IP address within the host-only network range.

3. Restart the Network Manager:

   ```bash
   sudo systemctl restart NetworkManager
   ```

4. Test connectivity:

   ```bash
   ping google.com
   ping [host_machine_ip]
   ```

5. Open HTTP port 80 on the firewall:

   ```bash
   sudo firewall-cmd --add-port=80/tcp --permanent
   sudo firewall-cmd --reload
   ```

---

## 4. (Optional) Enable SSH Access

1. Open the SSH configuration file:

   ```bash
   sudo nano /etc/ssh/sshd_config
   ```

2. Enable password authentication:

   ```
   PasswordAuthentication yes
   ```

3. (Optional, not recommended) Allow root login:

   ```
   PermitRootLogin yes
   ```

4. Restart SSH:

   ```bash
   sudo systemctl restart sshd
   ```

---

## 5. Install Required Packages

1. Update system packages:

   ```bash
   sudo yum update -y
   ```

2. Install Git, Apache, and other utilities:

   ```bash
   sudo yum install -y git httpd yum-utils
   ```

3. Add Docker’s repository:

   ```bash
   sudo yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
   ```

4. Install Docker:

   ```bash
   sudo yum install -y docker-ce docker-ce-cli containerd.io
   ```

5. Enable and start the Docker service:

   ```bash
   sudo systemctl enable docker
   sudo systemctl start docker
   ```

---

## 6. Deploy the PHP Application

1. Clone the repository:

   ```bash
   git clone https://github.com/nnhida/php-hybrid-infra.git
   cd php-hybrid-infra
   ```

2. Edit the `config.php` file and configure your AWS credentials and RDS connection.

3. Set up the database schema in your RDS instance:

   ```sql
   CREATE TABLE siswa (
     id INT(11) PRIMARY KEY AUTO_INCREMENT,
     nama VARCHAR(255) NOT NULL,
     nomor_presensi INT(11) NOT NULL,
     kelas VARCHAR(100) NOT NULL,
     foto_key VARCHAR(255) NULL
   );
   ```

4. Configure the required permissions in your S3 bucket policy:

   ```json
   {
     "Version": "2012-10-17",
     "Statement": [
       {
         "Effect": "Allow",
         "Principal": "*",
         "Action": ["s3:GetObject", "s3:PutObject"],
         "Resource": "arn:aws:s3:::[your_bucket_name]/foto-siswa/*"
       }
     ]
   }
   ```

---

## 7. Build and Run the Docker Container

1. Build the Docker image:

   ```bash
   docker build -t [image_name] .
   ```

2. Run the container:

   ```bash
   docker run -d -p 80:80 [image_name]
   ```

---

## 8. Configure Domain Resolution with MikroTik

1. Log in to your MikroTik router using Winbox or WebFig.

2. Go to **IP → DNS** and enable **Allow Remote Requests**.

3. Add a **Static DNS Entry**:

   * Name: `[your_custom_domain]`
   * Address: IP address of your CentOS VM

4. On your client (host) machine, set the DNS server to the MikroTik router’s IP.

5. Test the DNS configuration:

   ```bash
   ping [your_custom_domain]
   ```

---

## 9. Access the Web Application

1. Open a web browser on the client machine.
2. Navigate to:

   ```
   http://[your_custom_domain]
   ```

You should now see your deployed PHP application.

---

## Troubleshooting

### Problem: SSH error "`: unknown terminal type.`"

**Solution:**

```bash
export TERM=xterm
```

---

### Problem: Docker build fails due to sandbox error

Error message:

```bash
xz: Failed to enable the sandbox
tar: Child returned status 1
tar: Error is not recoverable: exiting now
```

**Option 1: Use `--no-sandbox`**

1. In your `Dockerfile`, uncomment the following lines:

   ```dockerfile
   ENV XZ_OPT="--no-sandbox"
   ENV XZ_DEFAULTS="--no-sandbox"
   ```

2. Build with additional security options:

   ```bash
   docker build --security-opt seccomp=unconfined -t [image_name] .
   docker run -d -p 80:80 [image_name]
   ```

**Option 2: Use the fallback Dockerfile**

If the above fails, use the fallback Dockerfile provided:

```bash
docker build -f Dockerfile.fallback -t [image_name] .
docker run -d -p 80:80 [image_name]
```

---
