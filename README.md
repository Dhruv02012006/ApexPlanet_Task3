# ApexPlanet Task 4 - Security Enhancements

## Project Overview

This project is an enhanced PHP and MySQL blog application developed as part of the ApexPlanet Web Development Internship.

Task 4 focuses on improving the security of the application through prepared statements, form validation, and user roles and permissions.

## Security Features Implemented

### 1. Prepared Statements

Prepared statements using MySQLi are used for database queries involving user input.

They are implemented for:

- Creating posts
- Editing posts
- Deleting posts
- Searching posts
- Retrieving posts
- User authentication

Prepared statements help prevent SQL injection.

### 2. Server-Side Validation

Server-side validation has been implemented for post forms.

The application checks:

- Title cannot be empty.
- Content cannot be empty.
- Title cannot exceed 255 characters.

### 3. Client-Side Validation

JavaScript validation has been added to the Create Post and Edit Post forms.

The validation checks:

- Title is required.
- Content is required.
- Title must not exceed 255 characters.

### 4. User Roles

A `role` column was added to the `users` table.

The application supports:

- `admin`
- `editor`

### 5. Role-Based Access Control

Admin-only access has been implemented for:

- Creating posts
- Editing posts
- Deleting posts

Users without the admin role are redirected away from these pages.

### 6. Authentication

The application uses PHP sessions to maintain authenticated users.

Passwords are handled using password hashing and password verification.

## Files Updated

- `login.php`
- `index.php`
- `create.php`
- `edit.php`
- `delete.php`

## Database Changes

The `users` table was extended with:

```sql
role VARCHAR(20) NOT NULL DEFAULT 'editor'