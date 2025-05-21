# Lost and Found Web Application

![PHP](https://img.shields.io/badge/PHP-7.4+-8892BF.svg?style=flat&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1.svg?style=flat&logo=mysql&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-2.2.19-38B2AC.svg?style=flat&logo=tailwind-css&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg)

A modern web application that helps users report lost items and post found items to reconnect people with their belongings.


## ✨ Features

- **User Authentication**
    - Secure registration and login system
    - Password hashing for security
    - Session management

- **Item Management**
    - Report lost items with details and images
    - Post found items with descriptions and location
    - Browse all items with search and filter capabilities
    - Pagination for efficient item browsing

- **User Dashboard**
    - Manage your reported items
    - Edit or delete your posts
    - View statistics and activity

- **Communication System**
    - Contact item posters
    - Claim items that belong to you
    - Email notifications

- **Responsive Design**
    - Mobile-friendly interface
    - Clean, modern UI with Tailwind CSS

## 📸 Screenshots

<details>
<summary>View Screenshots</summary>

### Home Page
![Home Page](https://via.placeholder.com/800x400?text=Home+Page)

### Dashboard
![Dashboard](https://via.placeholder.com/800x400?text=Dashboard)

### Report Item
![Report Item](https://via.placeholder.com/800x400?text=Report+Item)

</details>

## 🔧 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache or Nginx)
- Composer (optional, for future dependencies)

## 🚀 Installation

### Option 1: Manual Installation

1. **Clone the repository**
     ```bash
     git clone https://github.com/mal0101/lost-and-found-hub.git
     cd lost-and-found-hub
     ```

2. **Create the directory structure**
     ```bash
     mkdir -p assets/css assets/js config includes/helpers includes/templates pages/auth pages/items pages/user public/uploads
     ```

3. **Set permissions for uploads directory**
     ```bash
     chmod 777 public/uploads
     ```

4. **Configure the database**
     - Edit `config/db.php` with your database credentials
     - For MAMP users, default credentials are usually:
         - Username: root
         - Password: root
     - For XAMPP users, default credentials are usually:
         - Username: root
         - Password: `` (empty)

5. **Initialize the database**
     - Visit `http://localhost:8888/lost-and-found-hub/setup_database.php` in your browser
     - You should see a success message

6. **Access the application**
     - Visit `http://localhost:8888/lost-and-found-hub/index.php` in your browser

### Option 2: Using a Development Environment
If you're using a development environment like MAMP, XAMPP, or Laragon:

1. **Download the project**
     - Download the ZIP file and extract it to your web server's document root
         - For MAMP: `/Applications/MAMP/htdocs/`
         - For XAMPP: `C:\xampp\htdocs\`

2. **Follow steps 3-6 from Option 1**

## 📁 Project Structure

<details>
<summary>Expand Structure</summary>

```
/lost-and-found-hub/
├── /assets/                  # Frontend assets
│   ├── /css/                 # CSS files
│   │   └── style.css         # Custom styles
│   └── /js/                  # JavaScript files
│       └── scripts.js        # Client-side functionality
│
├── /config/                  # Configuration files
│   └── db.php                # Database connection
│
├── /includes/                # Reusable components
│   ├── /helpers/             # Helper functions
│   │   └── functions.php     # Common utility functions
│   └── /templates/           # UI components
│       ├── header.php        # Page header
│       ├── footer.php        # Page footer
│       └── navbar.php        # Navigation bar
│
├── /pages/                   # Page controllers
│   ├── /auth/                # Authentication
│   │   ├── login.php         # User login
│   │   ├── logout.php        # User logout
│   │   └── register.php      # User registration
│   ├── /items/               # Item management
│   │   ├── add_item.php      # Create new item
│   │   ├── edit_item.php     # Edit existing item
│   │   ├── item_details.php  # View item details
│   │   ├── item_list.php     # List all items
│   │   ├── report_found_item.php # Report found item
│   │   └── report_lost_item.php  # Report lost item
│   └── /user/                # User functionality
│       ├── claim_item.php    # Claim an item
│       └── dashboard.php     # User dashboard
│
├── /public/                  # Publicly accessible files
│   └── /uploads/             # Uploaded images
│
├── index.php                 # Main entry point
├── contact.php               # Contact page
├── setup_database.php        # Database setup script
└── README.md                 # Project documentation
```
</details>

## 📝 Usage Guide

### 1. Registration and Login
- Register with a unique email address and username
- Log in using your credentials
- Your session will persist until you log out

### 2. Reporting Items
- **Lost Items**
    - Click "Report Lost Item" in the navigation bar
    - Fill out the form with:
        - Title of the item
        - Detailed description
        - Location where it was lost
        - Optional image
    - Submit the form
- **Found Items**
    - Click "Report Found Item" in the navigation bar
    - Fill out similar details about the found item
    - Submit the form

### 3. Browsing Items
- Use the search bar to find specific items
- Filter by "Lost" or "Found" status
- Click on an item for more details

### 4. Managing Your Items
- Go to "My Dashboard"
- View all your posted items
- Edit or delete items as needed

### 5. Claiming Items
- When you find an item that belongs to you, click "Claim This Item"
- Provide identifying information to verify ownership
- The item poster will be notified and can contact you

## ❓ Troubleshooting

| Problem | Solution |
|---------|----------|
| "Not Found" errors | Check your file paths and MAMP/XAMPP document root settings |
| Database connection errors | Verify database credentials in `config/db.php` |
| Image upload issues | Ensure `public/uploads` directory exists with write permissions |
| Login not working | Check for correct email/password and verify that sessions are enabled |
| Blank page | Enable PHP error reporting for debugging |

## 🔮 Future Enhancements

- Email verification for new accounts
- Password reset functionality
- Advanced search filters
- Admin dashboard for moderation
- Social media sharing
- Map integration for location-based searching

## 🤝 Contributing

Contributions are welcome! To contribute:

1. Fork the repository
2. Create a new branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Commit your changes (`git commit -m 'Add some amazing feature'`)
5. Push to the branch (`git push origin feature/amazing-feature`)
6. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👏 Acknowledgments

- Built with PHP
- Styled with Tailwind CSS
- Icons from Heroicons
- Inspired by various lost and found systems worldwide
<div align="center">Made with ❤️ by Malak M.</div>

 