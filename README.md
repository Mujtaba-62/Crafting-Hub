# CraftHub

CraftHub is an online crafting instruction hub built with PHP and MySQL. It allows users to share blogs, tutorials, and participate in crafting events. Admins can manage all content, including blogs, tutorials, events, and user messages.

## Features

- User registration and login (user/admin roles)
- Blog posting with images and links
- Tutorials by category (Knitting, Crochet, Pottery, Sewing, Woodworking, Jewelry)
- Event management and filtering (upcoming, by date range)
- Contact form for user messages
- Admin dashboard for managing all content (including deleted/restorable items)
- Responsive design using Bootstrap 5

## Setup Instructions

1. **Clone or Download the Repository**
2. **Database Setup**
   - Import `crafthub.sql` into your MySQL server to create the required tables.
   - Update `db.php` with your database credentials if needed.
3. **Configure Web Server**
   - Place the project files in your web server's root directory (e.g., `htdocs` for XAMPP).
   - Ensure PHP and MySQL are running.
4. **File Permissions**
   - Make sure the `uploads/` directory is writable for image uploads.
5. **Admin Access**
   - Use the provided `create_admin.php` script to create or update an admin user, then delete the script for security.

## Usage

- Register as a user or login as admin.
- Users can create blogs, tutorials, and send messages.
- Admins can manage all content, restore or permanently delete items, and view all user messages.

## License

This project is for educational purposes. Please review and update for production use as needed.
