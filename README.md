# **Deploying PHP Website on VM CentOS with Docker, AWS RDS, S3, and MikroTik**

## **1. Install CentOS**

1. Download the **CentOS 9 ISO**.
2. Create a new VM using **VirtualBox** or **VMware**.
3. Install CentOS on the VM.

---

## **2. Configure Networking with Dual Adapters**

To provide both internet access and local connectivity with the host machine:

1. Open **VM Settings → Network**.
2. Attach **two adapters**:

   * **Adapter 1 (NAT)** → provides internet access.
   * **Adapter 2 (Host-Only)** → enables local communication with the host.

---

## **3. Network Setup on CentOS**

1. Verify the current IP address:

   ```bash
   ip a
   ```

2. Modify the network configuration using `nmtui`:

   ```bash
   nmtui
   ```

   * In the Ethernet menu, select **Add**.
   * Assign a name according to your interface name.
   * Set **IPv4** to **Manual**.
   * Specify an IP address within the local network range.

3. Restart the network service:

   ```bash
   sudo systemctl restart NetworkManager
   ```

4. Test connectivity:

   ```bash
   ping google.com
   ping [local_ip]
   ```

---

## **Additional Configuration**

1. Update the SSH daemon configuration at `/etc/ssh/sshd-config` to allow local access:

   ```bash
   PasswordAuthentication yes
   PermitRootLogin yes
   ```

2. After confirming SSH access from the local machine, run:

   ```bash
   export TERM=xterm
   ```

---

## **4. Install Required Packages and Tools**

1. Update the system:

   ```bash
   sudo yum update -y
   ```
2. Install Git, Apache, and additional utilities:

   ```bash
   sudo yum install -y git httpd yum-utils
   ```
3. Add the official Docker repository:

   ```bash
   sudo yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
   ```
4. Install Docker:

   ```bash
   sudo yum install -y docker-ce docker-ce-cli containerd.io
   ```
5. Enable and start Docker:

   ```bash
   sudo systemctl enable docker
   sudo systemctl start docker
   ```

---

## **5. Deploy the Application**

1. Clone the repository:

   ```bash
   cd /home
   git clone https://github.com/nnhida/php-hybrid-infra.git
   cd php-hybrid-infra
   ```
2. Configure the `config.php` file
3. Prepare the RDS database schema:

   ```sql
   CREATE DATABASE db_sekolah;
   USE db_sekolah;
   CREATE TABLE siswa (
       id INT(11) PRIMARY KEY AUTO_INCREMENT,
       name VARCHAR(255) NOT NULL,
       student_number INT(11) NOT NULL,
       class VARCHAR(100) NOT NULL,
       photo_key VARCHAR(255) NULL
   );
   ```

---

## **6. Build and Run the Docker Image**

1. Build the Docker image:

   ```bash
   docker build -t crud-ukk-app .
   ```
2. Run the container:

   ```bash
   docker run -d -p 80:80 crud-ukk-app
   ```

---

## **7. Configure DNS on MikroTik**

1. Log in to MikroTik using **Winbox** or **WebFig**.
2. Navigate to **IP → DNS**:

   * Enable **Allow Remote Requests**.
3. Add a **Static DNS Entry**:

   * Name: `[your_domain]`
   * Address: `(CentOS VM IP)`
4. Configure your laptop’s DNS client to point to the MikroTik router’s IP.
5. Test DNS resolution:

   ```bash
   ping [your_domain]
   ```

   If successful, the domain should resolve to the VM’s IP.

---

## **8. Verify Application Deployment**

1. Open a web browser on the client machine.
2. Access the application via the configured domain:

   ```
   http://[your_domain]
   ```

---
