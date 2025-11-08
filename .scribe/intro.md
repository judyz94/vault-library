# Introduction

Welcome to the **Library Management API** — a RESTful service for managing users, books, and borrowing operations within a digital library system.

<aside>
    <strong>Base URL:</strong> <code>http://127.0.0.1:8000</code>
</aside>

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
