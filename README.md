# Deploying a PHP Website on CentOS VM with Docker, AWS RDS, S3, and MikroTik DNS

This guide explains how to deploy a PHP-based web application on a CentOS virtual machine using Docker.  
It connects to **AWS RDS** for database storage, uses **Amazon S3** for image storage, and configures domain resolution using a **MikroTik router**.

---

## Prerequisites

1. **MikroTik router** with internet access.  
   * Will serve as the **gateway** and the **DNS server** for the custom domain.  

2. **CentOS VM**  
   * Download **CentOS 9 ISO**.  
   * Create a VM in **VirtualBox** or **VMware**.  
   * Install CentOS on the VM.  

3. **AWS Account** with access to VPC, RDS, and S3.  

---

## MikroTik Setup

### Configure MikroTik for Internet Access

1. **Wireless → Security Profiles**  
   * Edit an existing profile or create a new one.  
   * Enable all security options.  
   * Enter the Wi-Fi password.  
   * **Apply** → **OK**.  

2. **Wireless → Interfaces**  
   * Select your `wlan` interface.  
   * Set **Mode** to `station`.  
   * Scan and connect to the SSID.  
   * Assign the Security Profile.  
   * **Apply** → **OK**.  

3. **IP → DHCP Client → +**  
   * **Interface**: `[your_wlan_interface]`  
   * **Add Default Route**: ✓  
   * **Use Peer DNS**: ✓ (or uncheck if using public DNS)  
   * **OK**  
   * Verify **Status = bound** (shows IP Address + Gateway).  

4. **IP → Firewall → NAT → +**  
   * **Chain**: `srcnat`  
   * **Out. Interface**: `[your_wlan_interface]`  
   * **Action**: `masquerade`  
   * **OK**  

---

### Configure `ether` as LAN

1. **Interfaces → Ethernet**  
   * Ensure `ether` is not a slave.  
   * If shown as *S slave* to `ether2-master`: open → set **Master Port = none** → **Apply**.  

2. **IP → Addresses → +**  
   * **Address**: `192.168.10.1/24` or any IP you desire
   * **Interface**: `ether` → **OK**  

3. **IP → DHCP Server → DHCP Setup**  
   * **Interface**: `ether`  
   * **DHCP Address Space**: `192.168.10.0/24`  
   * **Gateway**: `192.168.10.1`  
   * **Address Pool**: `192.168.10.10–192.168.10.100`  
   * **DNS Servers**: `192.168.10.1`  

---

## AWS Setup

### AWS Credentials
1. Copy credentials under **AWS Details** on Learner Lab.  
2. Click **Start Lab** to initialize.  

### VPC
1. Create a new **VPC**.  
2. Add **two public subnets** in different Availability Zones.  
3. Enable **DNS hostnames** and **DNS resolution**.  

### Security Group
1. Create a new **Security Group** for RDS.  
2. Inbound rule:  
   * **Type**: MySQL/Aurora (3306)  
   * **Source**: Anywhere (temporary for testing).  

### RDS
1. Create a new **MySQL database instance**.  
2. **Template**: Sandbox.  
3. Set **username** and **password**.  
4. Select your **VPC** and **Security Group**.  
5. **Public Access**: Yes.  
6. Under **Additional Settings**: set DB name = `db_sekolah`.  

### S3
1. Create a **new public bucket**.  
2. Add this **bucket policy** under Permissions (replace `[your_bucket_name]`):  

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
   ````

---

## Setup Database Schema

1. Connect using **MySQL Workbench**, **TablePlus**, or `mysql` CLI.
2. Create the schema:

   ```sql
   CREATE TABLE siswa (
     id INT AUTO_INCREMENT PRIMARY KEY,
     nama VARCHAR(255) NOT NULL,
     nomor_presensi INT NOT NULL,
     kelas VARCHAR(100) NOT NULL,
     foto_key VARCHAR(255)
   );
   ```

---

## Setup VM (CentOS)

### 1. Configure Network

* VM Settings → **Network → Bridged Adapter**
* Check IP:

  ```bash
  ip a
  ```
* Test connectivity:

  ```bash
  ping google.com
  ping [host_machine_ip]
  ```
* Open port 80:

  ```bash
  sudo firewall-cmd --permanent --add-port=80/tcp
  sudo firewall-cmd --reload
  ```

### 2. (Optional) SSH Access

* Edit `/etc/ssh/sshd_config`:

  * `PasswordAuthentication yes`
  * (Optional, not recommended) `PermitRootLogin yes`
* Restart SSH:

  ```bash
  sudo systemctl restart sshd
  ```

### 3. Install Packages

```bash
sudo yum update -y && sudo yum install -y yum-utils git
sudo yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
sudo yum install -y docker-ce docker-ce-cli containerd.io
sudo systemctl enable docker
sudo systemctl start docker
```

### 4. Deploy PHP App

```bash
git clone https://github.com/nnhida/php-hybrid-infra.git
cd php-hybrid-infra
```

* Edit `config.php` → add **AWS credentials**, **S3 bucket**, **RDS details**.
* Build & run:

  ```bash
  docker build -t php-app .
  docker run -d -p 80:80 php-app
  ```

### 5. Configure Domain with MikroTik

* On MikroTik → **IP → DNS → Allow Remote Requests**.
* Add Static DNS:

  * **Name**: `[your_custom_domain]`
  * **Address**: `[CentOS_VM_IP]`

---

## Access the Web Application

In browser:

```
http://[your_custom_domain]
```

---

## Troubleshooting

### Error: `unknown terminal type` after SSH

```bash
export TERM=xterm
```

### Error: `RequestTimeTooSkewed` when uploading to S3

Fix time sync on VM:

```bash
sudo timedatectl set-ntp true
timedatectl status
```
