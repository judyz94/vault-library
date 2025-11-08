# Vault Library

Vault Library is a full-stack **Library Management System (LMS)** built with **Laravel** and **React**, designed with a **fintech-inspired workspace culture** that emphasizes execution, minimalism, and innovation.

The system allows admins and users to manage books, library members, and borrowing activities with data persisted in a relational database and a clean, responsive interface.

---

## Functionalities
The app allows users to:

- **Manage Books:**

    Admins can add, edit, delete, and retrieve books with attributes such as title, author, ISBN, publication year, and availability status.

- **Manage Library Users:**

    Admins can create, update, and remove library members, assigning roles as **admin** or **user**.

- **Borrow and Return Books:**

    Users can borrow up to **3 books at a time**. Borrowed books are automatically marked as unavailable and become available once returned. The system tracks borrowing history and due dates (default: 14 days).

- **Search Books:**

    Search books dynamically by title, author, or ISBN, with an intuitive and responsive interface.

**Authentication:**

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

   The default name in the .env.example is "vault_library"


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

## Authentication & API Overview

This project provides a secure RESTful API for managing a digital library system, built with **Laravel Sanctum** for token-based authentication.

Users can log in to borrow and return books, while administrators can fully manage users, books, and borrowing records.

### Roles

- **Admin** — Has full system access.  
  Can:
    - Create, update, list, show and delete users.
    - Create, update, list, show and delete books.
    - Search books.
    - Borrow and return books on behalf of users.
    - View the list of borrowed books for any user.

- **User** — Has limited access.  
  Can:
    - Browse and search available books.
    - Borrow and return their own books.
    - View their own borrowing history.

### Authentication Flow

The authentication system uses **Laravel Sanctum** tokens.  
Users must first register or log in to obtain an **API token**, which must be included in subsequent requests via the `Authorization` header.

#### Headers Example
```http
Authorization: Bearer your_api_token_here
Accept: application/json
```

### Authentication API

**Base route:** `/api`

| Method | Endpoint         | Description                     | Auth Required |
|--------|------------------|----------------------------------|--------------|
| POST   | `/api/login`     | Log in and receive an API token  | No           |
| POST   | `/api/logout`    | Log out and revoke the token     | Yes          |

---

## Protected API Endpoints

All routes below require authentication (`auth:sanctum` middleware).

### Users API

**Base route:** `/api/users`

| Method | Endpoint             | Description              | Access (Role) |
|--------|----------------------|--------------------------|---------------|
| GET    | `/api/users`         | List all users           | Admin & User  |
| GET    | `/api/users/{id}`    | Get a specific user      | Admin & User  |
| POST   | `/api/users`         | Create a new user        | Admin only    |
| PUT    | `/api/users/{id}`    | Update an existing user  | Admin only    |
| DELETE | `/api/users/{id}`    | Delete a user            | Admin only    |

---

### Books API

**Base route:** `/api/books`

| Method | Endpoint            | Description             | Access (Role) |
|--------|---------------------|-------------------------|---------------|
| GET    | `/api/books`        | List all books          | Admin & User  |
| GET    | `/api/books/{id}`   | Get a specific book     | Admin & User  |
| SEARCH | `/api/books/search` | Search a book           | Admin & User  |
| POST   | `/api/books`        | Create a new book       | Admin only    |
| PUT    | `/api/books/{id}`   | Update an existing book | Admin only    |
| DELETE | `/api/books/{id}`   | Delete a book           | Admin only    |

---

### Borrowing API

**Base route:** `/api/users/{user}`

| Method | Endpoint                         | Description                                 | Access (Role)  |
|--------|----------------------------------|---------------------------------------------|----------------|
| POST   | `/api/users/{user}/borrow`       | Borrow a book                               | Admin & User   |
| POST   | `/api/users/{user}/return`       | Return a borrowed book                      | Admin & User   |
| GET    | `/api/users/{user}/borrowed`     | View user’s borrowed books and loan history | Admin & User   |

---
