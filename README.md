


# **📦 E-Commerce App — Full Stack (Flutter + CodeIgniter 4 + MySQL)**

A complete e-commerce application built with **Flutter** for the customer app, **CodeIgniter 4** for backend REST APIs, and **MySQL** as the database.
The project also includes an **Admin Panel** (browser-based) for managing products, categories, and orders.

---

# 🚀 **Project Features**

## **Frontend (Flutter)**

* User Authentication (Login / Register)
* Browse Categories
* Browse Products
* Product Details Page
* Cart Management
* Place Orders
* Order History & Details
* Fully responsive + works on Web, Android, Desktop

## **Backend (CodeIgniter 4)**

* JWT-based Authentication
* REST APIs for Categories, Products, Users, Orders
* Secure Admin Panel
* File upload (images) under `public/uploads/`
* CORS Enabled
* Validation + Middleware Filters

## **Admin Panel**

* Login for Admin
* Add/Edit/Delete Categories
* Add/Edit/Delete Products
* View Orders
* Manage Users
* Upload Images

---

# 🛠 **Tech Stack**

| Layer    | Technology                             |
| -------- | -------------------------------------- |
| Frontend | Flutter 3.x, Provider State Management |
| Backend  | CodeIgniter 4 (REST APIs)              |
| Database | MySQL / MariaDB                        |
| Server   | Apache 2.4 (AWS EC2)                   |
| Security | JWT Authentication                     |
| OS       | Amazon Linux 2023                      |

---

# 📁 **Folder Structure**

```
project/
│
├── backend/                     # CodeIgniter 4 API + Admin Panel
│   ├── app/
│   │   ├── Controllers/
│   │   │   ├── Api/            # API controllers
│   │   │   └── Admin/          # Admin Panel controllers
│   │   ├── Filters/            # JWT & CORS filters
│   │   ├── Models/             # DB Models
│   │   ├── Views/
│   │   │   └── admin/          # Admin Panel UI
│   │   └── Config/             # Routes, CORS, Filters
│   ├── public/                 # Web-accessible folder
│   │   ├── index.php
│   │   └── uploads/            # Uploaded images
│   ├── writable/               # Cache, logs
│   └── .env                    # Environment settings
│
└── customer_app/               # Flutter App
    ├── lib/
    │   ├── screens/            # UI Screens
    │   ├── providers/          # State Management
    │   ├── models/
    │   ├── services/           # API Service
    │   ├── widgets/
    │   └── constants.dart      # API base URL
```

---

# 🔧 **Backend Setup (Local or Server)**

### **1. Copy project files**

```
/var/www/html/backend(if using apache) since root was different for nginx
```

### **2. Install dependencies**

```
composer install(make sure composer.json exists there if you are using the above project)
```

### **3. Configure .env**

```
CI_ENVIRONMENT = development
app.baseURL = 'http://YOUR-IP/'
database.default.hostname = localhost
database.default.database = ecommerce_db
database.default.username = ciuser(you can also do it with root user)
database.default.password = StrongPass123
JWT_SECRET = "your_secret"
```

### **4. Set permissions**

```
sudo chown -R apache:apache writable/( This might be the error most of the times when deployment)
sudo chmod -R 775 writable/
```

### **5. Apache VirtualHost**

`/etc/httpd/conf.d/backend.conf`

```
<VirtualHost *:80>
    DocumentRoot /var/www/html/backend/public
    <Directory /var/www/html/backend/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### **6. Restart Apache**

```
sudo systemctl restart httpd
```

---

# 📱 **Flutter Frontend Setup**

### **Install dependencies**

```
flutter pub get
```

### **Update API base URL — lib/constants.dart**

```
const String baseUrl = "http://YOUR-SERVER-IP";
```

### **Fix image handling — replace localhost**

```
if (!image.startsWith("http")) {
   image = "$baseUrl/$image";
}
```

### **Run on web**

```
flutter run -d chrome
```

---

# ☁️ **Deployment (AWS EC2)**

### ✔ Install Apache, PHP, MySQL

### ✔ Create database & user

### ✔ Import ecommerce_db.sql(you can  find this file in backend_with_admin_panel folder and while deployment make sure to set SET FOREIGN_KEY_CHECKS = 0; at the top and  SET FOREIGN_KEY_CHECKS = 1; at the bottom while setting up database in mariadb



### ✔ Upload backend → `/var/www/html/backend`(zipping correct folders and files are mandatory (dont zip vendor if its size is too high, you can install vendor using composer install later after unzipping the backend)

### ✔ Configure VirtualHost and `.env`

### ✔ Update Flutter Base URL (only changing the url is constants.dart is enough for connecting with backend)

### ✔ Enable CORS

### ✔ Open ports in AWS Security Group

```
- 80 → HTTP  
- 443 → HTTPS  
- 22 → SSH  
```

### ✔ Handle IP change when instance stops

Update `.env` and Flutter `constants.dart` with new IP.

---

# 🐞 **Major Issues Faced & Fixes**

### **1. 404 Not Found after deployment**

Cause: Wrong DocumentRoot
Fix: Set Apache VirtualHost to backend/public

---

### **2. Image paths showing localhost in Flutter**

Cause: DB stored old localhost URLs
Fix:

```
UPDATE products SET image_url = REPLACE(image_url, 'http://localhost/...', 'http://NEW-IP');
UPDATE categories SET image = REPLACE(image, 'http://localhost/...', 'http://NEW-IP');
```

---

### **3. JWTAuthFilter not found (500 error)**

Cause: CI4 cache + wrong namespace
Fix:

```
rm -rf writable/cache/*
systemctl restart httpd
```

---

### **4. CORS blocked Flutter Web**

Fix: Update CORS.php

```
public $allowedOrigins = ['*'];
public $allowedHeaders = ['Content-Type', 'Authorization'];
public $allowedMethods = ['GET','POST','PUT','DELETE','OPTIONS'];
```

---

### **5. IP changed after instance restart**

Fix: Update

* `.env`
* Flutter `constants.dart`
* DB image paths
  **Recommendation: Use Elastic IP**

---

# 🎉 **Conclusion**

This project is a complete full-stack E-Commerce system successfully deployed on **AWS EC2** with real-world configurations including routing, security, database modeling, image optimization, state management, and deployment automation.

The system is fully functional and ready for real usage or further enhancements.

if any queries regarding above project, please feel free to contact krishnatejakarnakanti@gmail.com(our team will always be ready to help you at any cost)

