# Pet QR Code Application

A PHP application for storing and managing pet information with QR code generation. Each pet gets a unique QR code that links to a responsive webpage displaying all their information.

![PHP](https://img.shields.io/badge/PHP-%3E%3D8.1-blue)
![License](https://img.shields.io/badge/license-MIT-green)

## Features

- **Complete CRUD Operations**: Create, read, update, and delete pet records
- **QR Code Generation**: Generate unique QR codes for each pet using the `endroid/qr-code` library
- **Responsive Design**: Mobile-friendly interface using Bootstrap 5
- **Secure Access**: Unique tokens for each pet to prevent unauthorized access
- **Pet Information Management**: Store comprehensive pet details including:
  - Pet name, breed, and age
  - Owner name and contact information
  - Medical history and notes
- **Print QR Codes**: Easy printing functionality for QR codes

## Project Structure

```
/pet-qr-code-app
    /assets
        /css              - CSS stylesheets
        /js               - JavaScript files
        /images           - Image assets
    /includes
        db.php            - Database connection and helper functions
        header.php        - Reusable header component
        footer.php        - Reusable footer component
    /pages
        pet_info.php      - Public pet information display page
    /qr_codes             - Generated QR code images
    /vendor               - Composer dependencies (auto-generated)
    composer.json         - PHP dependencies
    database.sql          - Database schema
    index.php             - Homepage listing all pets
    add_pet.php           - Add new pet form
    edit_pet.php          - Edit pet details form
    generate_qr.php       - QR code generation page
    delete_pet.php        - Delete pet handler
```

## Requirements

- PHP 8.1 or higher
- MySQL 5.7 or higher / MariaDB 10.2 or higher
- Apache or Nginx web server
- Composer (for dependency management)
- mod_rewrite enabled (for Apache)

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/kanakis/pet-qr-code-app.git
cd pet-qr-code-app
```

### 2. Install Dependencies

```bash
composer install
```

This will install the required `endroid/qr-code` library and its dependencies.

### 3. Configure Database

#### Option A: Using MySQL Command Line

```bash
mysql -u root -p < database.sql
```

#### Option B: Using phpMyAdmin

1. Open phpMyAdmin
2. Create a new database named `pet_qr_code_app`
3. Import the `database.sql` file

#### Option C: Manual Setup

```sql
CREATE DATABASE pet_qr_code_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pet_qr_code_app;

-- Run the SQL from database.sql file
```

### 4. Configure Database Connection

Edit `includes/db.php` and update the database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'pet_qr_code_app');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');

// Update BASE_URL if not installed at web root
// For subdirectory: define('BASE_URL', '/pet-qr-code-app/');
define('BASE_URL', '/');
```

### 5. Set Directory Permissions

Ensure the `qr_codes` directory is writable:

```bash
chmod 755 qr_codes
```

### 6. Configure Web Server

#### Apache Configuration

Create a `.htaccess` file in the root directory (if not already present):

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Prevent access to sensitive files
    <Files "composer.json">
        Require all denied
    </Files>
    <Files "composer.lock">
        Require all denied
    </Files>
</IfModule>

# Prevent directory listing
Options -Indexes

# PHP settings
<IfModule mod_php7.c>
    php_flag display_errors On
    php_value error_reporting E_ALL
</IfModule>
```

#### Nginx Configuration

Add this to your server block:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/pet-qr-code-app;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 7. Access the Application

Open your web browser and navigate to:
- Local development: `http://localhost/pet-qr-code-app/`
- Production: `http://your-domain.com/`

## Usage

### Adding a Pet

1. Click on "Add Pet" from the homepage or navigation menu
2. Fill in the pet information form:
   - Pet name (required)
   - Breed, age, owner details, and medical history (optional)
3. Click "Add Pet" to save
4. You'll be redirected to generate a QR code for the pet

### Generating QR Codes

1. After adding a pet, you'll be prompted to generate a QR code
2. The QR code links to a secure page displaying all pet information
3. You can download or print the QR code
4. Scan the QR code with any smartphone to view pet details

### Editing Pet Information

1. From the homepage, click "Edit" on any pet card
2. Update the information as needed
3. Click "Update Pet" to save changes

### Deleting a Pet

1. From the homepage, click "Delete" on the pet card
2. Confirm the deletion
3. The pet record and associated QR code will be permanently removed

### Viewing Pet Information

- Click "View Details" on any pet card
- Or scan the pet's QR code with a smartphone
- The information page is responsive and works on all devices

## Security Features

- **Unique Tokens**: Each pet has a unique 64-character token for secure access
- **SQL Injection Prevention**: All database queries use prepared statements
- **XSS Protection**: All output is sanitized using `htmlspecialchars()`
- **Input Validation**: Server-side validation of all form inputs
- **Secure File Handling**: QR codes are stored with unique filenames

## Technology Stack

- **Backend**: PHP 8.1+
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript
- **CSS Framework**: Bootstrap 5
- **Icons**: Font Awesome 6
- **QR Code Library**: Endroid QR Code 4.x

## Troubleshooting

### QR Codes Not Generating

- Ensure the `qr_codes` directory exists and is writable
- Check that Composer dependencies are installed (`composer install`)
- Verify PHP GD extension is enabled

### Database Connection Errors

- Verify database credentials in `includes/db.php`
- Ensure MySQL service is running
- Check that the database `pet_qr_code_app` exists

### Permission Errors

```bash
# Fix permissions
chmod 755 qr_codes
chown www-data:www-data qr_codes  # For Linux/Apache
```

### Blank Page or Errors

- Enable error reporting in PHP
- Check Apache/Nginx error logs
- Ensure all required PHP extensions are installed

## Browser Compatibility

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is open source and available under the [MIT License](LICENSE).

## Support

For issues, questions, or contributions, please open an issue on the GitHub repository.

## Acknowledgments

- [Endroid QR Code](https://github.com/endroid/qr-code) - QR code generation library
- [Bootstrap](https://getbootstrap.com/) - CSS framework
- [Font Awesome](https://fontawesome.com/) - Icon library

---

Made with ❤️ for pet lovers
