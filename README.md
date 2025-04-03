# VPC 2-Tier Architecture: Hosting a College Form Page with LEMP Stack

## **1. Overview**

In this project, we set up a **two-tier architecture** on AWS using a **custom VPC**. The architecture consists of:

- A **Web Server (public subnet)** hosting a PHP form.
- A **Database Server (private subnet)** running MariaDB.
- Secure **networking components**: VPC, subnets, route tables, Internet Gateway (IGW), and NAT Gateway.

---

## **2. VPC and Networking Setup**

### **Step 1: Create a Custom VPC**

- Go to AWS Management Console → VPC Service → Create VPC.
- Set **Name**: `my-vpc`.
- Set **CIDR Block**: `10.0.0.0/16`.
- Click **Create VPC**.

📌 **Screenshot:**
![VPC Created](2 created vpc.png)

### **Step 2: Create Subnets**

- **Web Subnet**
  - CIDR Block: `10.0.1.0/24`
  - Select `my-vpc`.
  - Enable **Auto-assign public IP**.
- **Database Subnet**
  - CIDR Block: `10.0.2.0/24`
  - Select `my-vpc`.
  - No auto-assign public IP.

📌 **Screenshots:**
![Create Subnet](3 create subnet.png)
![Subnet Created](4 subnet created.png)

### **Step 3: Create Route Table and Attach to Web Subnet**

- Go to **Route Tables** → **Create Route Table**.
- Set **Name**: `my-rt2`.
- Attach to `my-vpc`.
- Select `web-subnet` and associate it.

📌 **Screenshots:**
![Create Route Table](5 create route table.png)
![Attach RT to Subnet](6 attach RT to subnet.png)

### **Step 4: Create an Internet Gateway**

- Go to **Internet Gateways** → **Create IGW**.
- Name it `my-igw`.
- Attach it to `my-vpc`.
- Edit **Route Table** and add:
  - Destination: `0.0.0.0/0`
  - Target: `my-igw`

📌 **Screenshots:**
![IGW Created](7 created igw and attach.png)
![Attach IGW](8 attach igw.png)
![IGW Attached to Web Subnet](9 igw Attach to websubnet.png)

### **Step 5: Create a NAT Gateway**

- Go to **NAT Gateways** → **Create NAT Gateway**.
- Attach it to `web-subnet`.
- Allocate an **Elastic IP**.
- Create a new **Route Table** for `db-subnet`:
  - Destination: `0.0.0.0/0`
  - Target: `NAT Gateway`
  - Do NOT use an Internet Gateway.

---

## **3. Launching EC2 Instances**

### **Step 6: Create Web and Database Servers**

- **Web Server**
  - Ubuntu 22.04, in `web-subnet` (public)
  - Enable Auto-assign Public IP.
- **Database Server**
  - Ubuntu 22.04, in `db-subnet` (private)
  - No Public IP.

📌 **Screenshot:**
![Launch Instance](10 launch instance.png)

---

## **4. Transferring Files and Installing LEMP Stack**

### **Step 7: Send Key Pair & LEMP Script to Web Server**

```bash
scp -i my-key.pem my-key.pem ubuntu@web-server-public-ip:/home/ubuntu/
scp -i my-key.pem lemp-install.sh ubuntu@web-server-public-ip:/home/ubuntu/
```

### **Step 8: Connect to Web Server & Install LEMP Stack**

```bash
ssh -i my-key.pem ubuntu@web-server-public-ip
sudo chmod +x lemp-install.sh
./lemp-install.sh
```

- **LEMP Setup:**
  - Install **Nginx, PHP, MySQL Client** (not MariaDB)
  - Start and enable Nginx & PHP

### **Step 9: Create Web Form Files**

```bash
cd /var/www/html
sudo nano form.html
```

Paste the following code:

```html
<!DOCTYPE html>
<html>
<body>
  <form action="submit.php" method="POST">
    Name: <input type="text" name="name"><br>
    Email: <input type="text" name="email"><br>
    <input type="submit">
  </form>
</body>
</html>
```

Save and exit.

Create `submit.php`:

```bash
sudo nano submit.php
```

Paste the following code:

```php
<?php
$servername = "db-server-private-ip";
$username = "shreyash";
$password = "Pass@123";
$database = "mydb";
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "INSERT INTO student (name, email) VALUES ('" . $_POST['name'] . "', '" . $_POST['email'] . "')";
$conn->query($sql);
echo "Data inserted successfully";
$conn->close();
?>
```

---

## **5. Configuring Database Server**

### **Step 10: Connect from Web Server to Database Server**

```bash
ssh -i my-key.pem ubuntu@db-server-private-ip
```

### **Step 11: Install MariaDB on Database Server**

```bash
sudo apt update
sudo apt install mariadb-server -y
sudo systemctl start mariadb
```

### **Step 12: Secure and Configure MySQL**

```bash
sudo mysql
CREATE USER 'shreyash'@'web-server-private-ip' IDENTIFIED BY 'Pass@123';
GRANT ALL PRIVILEGES ON mydb.* TO 'shreyash'@'web-server-private-ip';
FLUSH PRIVILEGES;
CREATE DATABASE mydb;
USE mydb;
CREATE TABLE student (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(50), email VARCHAR(50));
EXIT;
```

---

## **6. Final Validation and Testing**

### **Step 15: Access the Web Form**

- Open the browser and go to: `http://web-server-public-ip/form.html`
- Submit the form and check if data is stored in `mydb`.

### **Step 16: Verify Data in Database**

```bash
mysql -u shreyash -p -h db-server-private-ip
USE mydb;
SELECT * FROM student;
```

---

## **7. Conclusion**

- Successfully hosted a **college form page** using **Nginx, PHP, MySQL**.
- Implemented **secure networking** with **VPC, Subnets, IGW, NAT Gateway, and Security Groups**.
- Verified **database connectivity** from web to database server.

📌 **Next Steps:** Add load balancing, SSL, and automate setup using Terraform!

---

### **Author: Shreyash Myakal**
🚀 AWS DevOps Enthusiast | Cloud & Automation | Docker | Kubernetes | CI/CD

