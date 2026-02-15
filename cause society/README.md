# CAUSE Smart Society Management System

## Installation Steps (XAMPP ke liye)

1. **XAMPP Start karein**
   - Apache aur MySQL services start karein

2. **Files Copy karein**
   - Is project folder ko `C:\xampp\htdocs\cause` mein copy karein

3. **Database Setup**
   - Browser mein `http://localhost/cause/setup.php` open karein
   - Ye automatically database aur tables create karega

4. **Login karein**
   - Browser mein `http://localhost/cause/` open karein
   - Default Admin Credentials:
     - Registration ID: `ADMIN-001`
     - Password: `123456`

5. **First Login**
   - Pehli baar login karne par password change karna zaroori hai

## Project Structure

```
cause/
├── config/          # Database configuration
├── auth/            # Login/logout logic
├── includes/        # Header/footer files
├── assets/          # CSS/Images (future use)
├── index.php        # Login page
├── setup.php        # Database setup script
├── setup.sql        # SQL script (alternative)
└── *_dashboard.php  # Role-based dashboards
```

## Features (Step 1)

- Professional login system with purple theme
- Role-based authentication (8 roles)
- Secure password hashing
- First-time password change requirement
- Session management
- Responsive design with Tailwind CSS

## Default Users

- Admin: ADMIN-001 / 123456

## Technologies

- PHP 7.4+
- MySQL
- Tailwind CSS (CDN)
- PDO for database
