# Laravel REST API - Backend Demo Project

## 📌 Introduction
This project is a backend developer test implementation using **Laravel 11**. It includes:
- Authentication with **Laravel Sanctum** (Token-based Authentication)
- Role and Permission Management using **Spatie Laravel Permissions**
- CRUD operations for Users and Posts with full access control
- Advanced filtering for posts based on the author
- **Unit and Feature Testing** using PHPUnit
- Organized **JSON API Resources** for structured API responses

---

## 🚀 **Technologies Used**
- Laravel 11
- MySQL
- Sanctum (for authentication tokens)
- Spatie Laravel Permission (for roles and permissions)
- PHPUnit (for testing)
- Postman (for API testing)

---

## ⚙ **Installation and Setup**
### 1️⃣ **Clone the repository**
```sh
git clone https://github.com/YOUR_USERNAME/laravel-rest-api-demo.git
cd laravel-rest-api-demo
```

### 2️⃣ **Install dependencies**
```sh
composer install
```

### 3️⃣ **Create `.env` file**
```sh
cp .env.example .env
```

### 4️⃣ **Configure Database in `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_demo
DB_USERNAME=root
DB_PASSWORD=
```

### 5️⃣ **Run database migrations**
```sh
php artisan migrate
```

### 6️⃣ **Generate application key**
```sh
php artisan key:generate
```

### 7️⃣ **Create an Admin User**
To manually create an admin user, run the following commands in Tinker:
```sh
php artisan tinker
```
Then execute:
```php
use App\Models\User;
use Spatie\Permission\Models\Role;

Role::firstOrCreate(['name' => 'admin']);

$admin = User::create([
    'name' => 'Super Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password123'),
]);

$admin->assignRole('admin');

$token = $admin->createToken('api-token')->plainTextToken;
$token;
```
🚀 **Now you have an admin user with a token!**

### 8️⃣ **Start the development server**
```sh
php artisan serve
```

---

## 📌 **API Endpoints (Postman Collection)**
You can download the **Postman Collection** from this link:
🔗 [Postman Collection](LINK_TO_YOUR_COLLECTION)

### **🔹 Example API Endpoints**
| Function             | Endpoint                  | Method   | Authentication? |
|---------------------|------------------------|----------|----------------|
| Register User      | `/api/register`        | `POST`   | ❌ No         |
| Login             | `/api/login`           | `POST`   | ❌ No         |
| Logout            | `/api/logout`          | `POST`   | ✅ Yes        |
| Get Users List    | `/api/v1/users`        | `GET`    | ✅ Admin Only |
| Create Post       | `/api/v1/posts`        | `POST`   | ✅ User Only  |
| Update Post       | `/api/v1/posts/{id}`   | `PUT`    | ✅ Only Owner |
| Delete Post       | `/api/v1/posts/{id}`   | `DELETE` | ✅ Only Owner |

---

## 📌 **Running Tests (Unit & Feature Tests)**
Run the tests using:
```sh
php artisan test
```

---



