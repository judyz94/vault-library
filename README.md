# Vault Library

Vault Library is a full-stack **Library Management System (LMS)** built with **Laravel** and **React**, designed with a **fintech-inspired workspace culture** that emphasizes execution, minimalism, and innovation.

The system allows admins and users to manage books, library members, and borrowing activities with data persisted in a relational database and a clean, responsive interface.

---

## Functionalities
The app allows users to:

**Manage Books:**

Admins can add, edit, delete, and retrieve books with attributes such as title, author, ISBN, publication year, and availability status.

**Manage Library Users:**

Admins can create, update, and remove library members, assigning roles as **Admin** or **User**.

**Borrow and Return Books:**

Users can borrow up to **3 books at a time**. Borrowed books are automatically marked as unavailable and become available once returned. The system tracks borrowing history and due dates (default: 14 days).

**Search Books:**

Search books dynamically by title, author, or ISBN, with an intuitive and responsive interface.

**JWT Authentication:**

Secure authentication and role-based access control using **Laravel Sanctum**.

**Admin Dashboard:**

Admins can manage all books and users, while regular users can view and track their borrowed books from a simple dashboard.

---

## Requirements

- **PHP** ^8.2
- **Composer** ^2.7
- **Laravel** ^11.x
- **MySQL** ^8.0
- **Node.js** ^22.12+
- **Npm** ^10.x
- **React** ^19
- **Vite** ^5.x
- **Tailwind CSS** ^3.x

---

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/judyz94/vault-library.git
   cd vault-library

2. **Install Laravel Dependencies**
   ```bash
   composer install

3. **Configure the .env file**

   Copy the sample file and edit it with the specific DB credentials:
   ```bash
   cp .env.example .env

4. **Generate the application key**
   ```bash
   php artisan key:generate

5. **Create DB in MySQL**

   The default name in the .env.example is "habit_tracker"


6. **Run migrations and seeders**
   ```bash
   php artisan migrate --seed

7. **Set the correct Node.js version**

   Before compiling the assets, make sure you are using the correct Node.js version
   ```bash
   nvm install 20
   nvm use 20

8. **Install Node.js Dependencies**
   ```bash
   npm install

9. **Compile the assets**
   ```bash
   npm run dev

10. **Start the local web server**
    ```bash
    php artisan serve

11. **Access the application**

    After the server is running, open your browser and go to the URL shown in the terminal (usually http://127.0.0.1:8000)


12. **Run the tests**

    To execute the test suite, you can use either of the following commands:

    Run tests using Laravel's built-in test runner:
    ```bash
    php artisan test
    ```

    Or, to generate a code coverage report in HTML:
    ```bash
    vendor/bin/phpunit --coverage-html coverage

---
