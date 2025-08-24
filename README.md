# Deploying a PHP Website on CentOS VM with Docker, AWS RDS, S3, and MikroTik DNS

This guide explains how to deploy a PHP-based web application on a CentOS virtual machine using Docker. It connects to AWS RDS for database storage, uses Amazon S3 for image storage, and configures domain resolution using a MikroTik router.

---

## Prerequisites

1. Prepare a **MikroTik router** with internet access.
   * The MikroTik router will serve as the **gateway.**
   * MikroTik will also act as the **DNS server** for the custom domain.
2. Install CentOS on a Virtual Machine
   * Download **CentOS 9 ISO**.
   * Create a new VM using **VirtualBox** or **VMware**.
   * Boot from the ISO and install CentOS on the VM.
3. Login to your AWS account

---

## 1. Configure Virtual Network Adapters

To allow your VM to access the internet **and** communicate with the host machine:

1. Open the VM **Settings → Network**.
2. Set **Attached to** → **Bridged Adapter**.
3. In the **Name** dropdown, choose the network interface your computer uses to access the internet.

   * Example: If your laptop connects via Wi-Fi, select **wlan0**.

---

## 2. Configure Networking in CentOS

1. Check the VM’s IP address:

   ```bash
   ip a
   ```

2. Verify that the VM’s IP address is on the same network as your host machine (the one you selected in the adapter settings, e.g., wlan0).

3. Test connectivity:

   ```bash
   ping google.com        
   ping [host_machine_ip] 
   ```

4. Allow HTTP traffic on port 80 (for web server access):

   ```bash
   sudo firewall-cmd --permanent --add-port=80/tcp
   sudo firewall-cmd --reload
   ```

---

## 3. (Optional) Enable SSH Access

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

## 4. Install Required Packages

1. Update system packages:

   ```bash
   sudo yum update -y && sudo yum install yum-utils -y
   ```

2. Add Docker’s repository:

   ```bash
   sudo yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
   ```

3. Install Git and other utilities:

   ```bash
   sudo yum install -y git docker-ce docker-ce-cli containerd.io
   ```

5. Enable and start the Docker service:

   ```bash
   sudo systemctl enable docker
   sudo systemctl start docker
   ```

---

## 5. Deploy the PHP Application

### 1. Clone the Repository

On your VM:

```bash
git clone https://github.com/nnhida/php-hybrid-infra.git
cd php-hybrid-infra
```

---

### 2. Configure AWS Resources

1. **Create a VPC**

   * Open the **VPC Console**.
   * Create a new **VPC** with **2 public subnets** in different Availability Zones (AZs).

2. **Create a Security Group**

   * Allow inbound MySQL traffic (port **3306**) **only** from your laptop’s public IP.

3. **Set Up RDS (MySQL)**

   * Create an **RDS instance** (MySQL-compatible).
   * Enable **public access**.
   * Set the **database name** to `db_sekolah`.

4. **Create an S3 Bucket**

   * Make a **public bucket**.

5. **Update the S3 Bucket Policy** to allow application access:

   ```json
   {
     "Version": "2012-10-17",
     "Statement": [
       {
         "Effect": "Allow",
         "Principal": "*",
         "Action": [
           "s3:GetObject",
           "s3:PutObject"
         ],
         "Resource": "arn:aws:s3:::[your_bucket_name]/foto-siswa/*"
       }
     ]
   }
   ```

---

### 3. Configure the Application

1. Open `config.php` in the project.
2. Set your **AWS credentials**, **S3 bucket name**, and **RDS connection details** (host, username, password, and database).

---

### 4. Set Up the Database Schema

Log in to your RDS MySQL instance and create the required table:

```sql
CREATE TABLE siswa (
  id INT(11) PRIMARY KEY AUTO_INCREMENT,
  nama VARCHAR(255) NOT NULL,
  nomor_presensi INT(11) NOT NULL,
  kelas VARCHAR(100) NOT NULL,
  foto_key VARCHAR(255) NULL
);
```

---

## 6. Build and Run the Docker Container

On your VM:

   ```bash
   docker build -t [image_name] .
   docker run -d -p 80:80 [image_name]
   ```

---

## 7. Configure Domain Resolution with MikroTik

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

## 8. Access the Web Application

1. Open a web browser on the client machine.
2. Navigate to:

   ```
   http://[your_custom_domain]
   ```

You should now see your deployed PHP application.

---

## Troubleshooting

### Problem: Error on terminal after SSH "`: unknown terminal type.`"

**Solution:**

```bash
export TERM=xterm
```

---
