# User Authentication System

## Overview
This project is a PHP-based user authentication system that allows users to register and log in securely. It includes features for password hashing, IP address tracking, and last login management.

## Features
- User registration with unique username checks
- Secure password storage using `password_hash()`
- User login with session management
- Automatic database table creation if it doesn't exist
- IP address tracking for registrations and logins
- User agent tracking for better security insights

## Requirements
- PHP 7.0 or higher
- PDO extension enabled
- MySQL database


## Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/user-authentication-system.git
   cd user-authentication-system
   ```
2. Navigate to the project directory:
   ```bash
   cd user-authentication-system
   ```
3. Configure your database connection in the User class constructor.
   
## Usage
* To register a new user, call the register() method with the required parameters.
* To log in, use the login() method and provide the username and password.

## Example
### Register
```php
$user = new User('db_user', 'db_pass', 'db_name', 'localhost', 'tablename');
$registration = $user->register('exampleUser', 'examplePass', '192.168.1.1', 'Mozilla/5.0');
if ($registration === true) {
    echo "User registered successfully!";
} else {
    echo "Error";
}
```
### Login
```php
$user = new User('db_user', 'db_pass', 'db_name', 'localhost', 'tablename');

$login = $user->login('exampleUser', 'examplePass', '192.168.1.1');
if ($login === true) {
    echo "User logged in successfully!";
} else {
    echo "Error";
}
```

# License
This project is licensed under the MIT License - see the LICENSE file for details.

# Contributing
Contributions are welcome! Please fork the repository and submit a pull request.
Feel free to customize any part of it as needed!
