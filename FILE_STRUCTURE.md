# Tourismo Zamboanga - Complete File Structure

## 📁 Directory Organization

```
tour-guide-zamboanga/
│
├── 📂 admin/                          # Admin Panel
│   ├── admin-login.html              # Admin login page
│   ├── dashboard.html                # Admin dashboard
│   ├── users.html                    # User management
│   ├── guides.html                   # Guide management
│   ├── bookings.html                 # Booking management
│   ├── payments.html                 # Payment management
│   ├── destinations.html             # Destination management
│   ├── packages.html                 # Package management
│   ├── reviews.html                  # Review moderation
│   ├── reports.html                  # Reports & analytics
│   └── settings.html                 # System settings
│
├── 📂 tourist/                        # Tourist Section
│   ├── tourist-dashboard.html        # Tourist dashboard
│   ├── tourist-register.html         # Tourist registration
│   ├── payment.html                  # Payment page
│   ├── payment-success.html          # Payment confirmation
│   ├── booking-history.html          # Booking history
│   └── assets/                       # Tourist-specific assets
│
├── 📂 guide/                          # Tour Guide Section
│   ├── guide-dashboard.html          # Guide dashboard
│   ├── guide-register.html           # Guide application
│   ├── earnings.html                 # Earnings tracker
│   ├── schedule.html                 # Schedule management
│   └── assets/                       # Guide-specific assets
│
├── 📂 assets/                         # Shared Assets
│   ├── 📂 css/                       # Stylesheets
│   │   ├── main.css                  # Main site styles
│   │   ├── login.css                 # Login page styles
│   │   ├── dashboard.css             # Dashboard styles (shared)
│   │   ├── register.css              # Registration styles (shared)
│   │   ├── payment.css               # Payment system styles
│   │   └── admin.css                 # Admin panel styles
│   │
│   ├── 📂 js/                        # JavaScript Files
│   │   ├── main.js                   # Main scripts
│   │   ├── dashboard.js              # Dashboard scripts
│   │   └── payment.js                # Payment scripts
│   │
│   ├── 📂 img/                       # Images
│   │   ├── index/                    # Homepage images
│   │   ├── slider/                   # Slider images
│   │   ├── person/                   # Profile images
│   │   └── destinations/             # Destination images
│   │
│   ├── 📂 vendor/                    # Third-party Libraries
│   │   ├── bootstrap/                # Bootstrap framework
│   │   ├── bootstrap-icons/          # Bootstrap icons
│   │   ├── aos/                      # Animate on scroll
│   │   ├── swiper/                   # Swiper slider
│   │   └── glightbox/                # Lightbox gallery
│   │
│   └── 📂 classes/                   # PHP Classes (future use)
│
├── 📂 includes/                       # PHP Backend Files
│   ├── config.php                    # Database configuration
│   ├── functions.php                 # Common functions
│   ├── auth.php                      # Authentication functions
│   ├── booking.php                   # Booking operations
│   ├── payment.php                   # Payment processing
│   └── email.php                     # Email functions
│
├── 📂 api/                            # API Endpoints (future)
│   ├── auth/                         # Authentication API
│   ├── bookings/                     # Booking API
│   ├── payments/                     # Payment API
│   └── users/                        # User API
│
├── 📂 uploads/                        # Uploaded Files
│   ├── profiles/                     # Profile pictures
│   ├── documents/                    # Guide certifications
│   └── receipts/                     # Payment receipts
│
├── 📄 index.html                      # Homepage
├── 📄 tourist-login.html              # Tourist login
├── 📄 tour-guides.html                # Browse guides
├── 📄 tourguidesystem.sql             # Database schema
├── 📄 tourguidesystem-data.sql        # Sample data
├── 📄 README.md                       # Main documentation
├── 📄 FILE_STRUCTURE.md               # This file
└── 📄 WEBSITE_GUIDE.txt               # Quick reference guide
```

## 🎨 CSS File Organization

### Shared CSS (assets/css/)
- **main.css** - Main website styles (homepage, navigation, footer)
- **login.css** - Login page styles (tourist, guide, admin)
- **dashboard.css** - Dashboard styles (shared by tourist & guide)
- **register.css** - Registration form styles (shared)
- **payment.css** - Payment system styles
- **admin.css** - Admin panel specific styles

### Usage in HTML Files

#### Tourist Pages
```html
<link href="../assets/css/main.css" rel="stylesheet">
<link href="../assets/css/dashboard.css" rel="stylesheet">
```

#### Guide Pages
```html
<link href="../assets/css/main.css" rel="stylesheet">
<link href="../assets/css/dashboard.css" rel="stylesheet">
```

#### Admin Pages
```html
<link href="../assets/css/admin.css" rel="stylesheet">
```

## 🔗 File Linking Reference

### From Root Directory (index.html, tourist-login.html)
```html
<!-- CSS -->
<link href="assets/css/main.css" rel="stylesheet">
<link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<!-- JS -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Images -->
<img src="assets/img/index/main_bg.jpg" alt="">

<!-- Links -->
<a href="tourist-login.html">Login</a>
<a href="tourist/tourist-dashboard.html">Dashboard</a>
<a href="admin/admin-login.html">Admin</a>
```

### From Tourist Folder (tourist/*.html)
```html
<!-- CSS -->
<link href="../assets/css/main.css" rel="stylesheet">
<link href="../assets/css/dashboard.css" rel="stylesheet">
<link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<!-- JS -->
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Images -->
<img src="../assets/img/person/person-1.jpg" alt="">

<!-- Links -->
<a href="../index.html">Home</a>
<a href="../tourist-login.html">Login</a>
<a href="tourist-dashboard.html">Dashboard</a>
<a href="payment.html">Payment</a>
```

### From Guide Folder (guide/*.html)
```html
<!-- CSS -->
<link href="../assets/css/main.css" rel="stylesheet">
<link href="../assets/css/dashboard.css" rel="stylesheet">

<!-- JS -->
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Links -->
<a href="../index.html">Home</a>
<a href="../guide-login.html">Login</a>
<a href="guide-dashboard.html">Dashboard</a>
```

### From Admin Folder (admin/*.html)
```html
<!-- CSS -->
<link href="../assets/css/admin.css" rel="stylesheet">
<link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<!-- JS -->
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Links -->
<a href="../index.html">Website</a>
<a href="dashboard.html">Dashboard</a>
<a href="users.html">Users</a>
```

## 🗄️ Database Tables (from tourguidesystem.sql)

### Core Tables
1. **Person** - Main user table (tourists, guides, admin)
2. **Role_Info** - User roles (1=Admin, 2=Guide, 3=Tourist)
3. **Name_Info** - User names
4. **Contact_Info** - Contact details
5. **Phone_Number** - Phone numbers
6. **Address_Info** - Addresses
7. **Emergency_Info** - Emergency contacts

### Tour Management
8. **Tour_Spots** - Tourist destinations
9. **Tour_Package** - Tour packages
10. **Schedule** - Tour schedules
11. **Booking** - Booking records
12. **Booking_Bundle** - Booking companions
13. **Companion_Info** - Companion details
14. **Companion_Category** - Companion types

### Payment System
15. **Payment_Info** - Payment records

### Rating System
16. **Rating** - User ratings
17. **Rating_Category** - Rating categories

### Additional Tables (to be created)
18. **User_Login** - Login credentials
19. **Activity_Log** - System activity log
20. **Password_Reset** - Password reset tokens

## 🔐 User Roles & Access

### Role ID 1: Admin
- **Access**: Full system access
- **Login**: `admin/admin-login.html`
- **Dashboard**: `admin/dashboard.html`
- **Capabilities**:
  - Manage all users
  - Verify tour guides
  - Monitor bookings
  - Process payments
  - Generate reports
  - System settings

### Role ID 2: Tour Guide
- **Access**: Guide portal
- **Login**: `guide-login.html` (root)
- **Dashboard**: `guide/guide-dashboard.html`
- **Capabilities**:
  - Manage profile
  - Set tour packages
  - Accept/decline bookings
  - View schedule
  - Track earnings
  - View reviews

### Role ID 3: Tourist
- **Access**: Tourist portal
- **Login**: `tourist-login.html` (root)
- **Dashboard**: `tourist/tourist-dashboard.html`
- **Capabilities**:
  - Browse destinations
  - Book tours
  - Make payments
  - View bookings
  - Leave reviews
  - Manage profile

## 💳 Payment System Flow

1. **Tourist selects tour** → `tour-guides.html`
2. **Booking creation** → `tourist/tourist-dashboard.html`
3. **Payment page** → `tourist/payment.html`
4. **Payment methods**:
   - Credit/Debit Card
   - GCash
   - PayMaya
   - Bank Transfer
5. **Payment processing** → `includes/payment.php`
6. **Confirmation** → `tourist/payment-success.html`
7. **Receipt email** → `includes/email.php`

## 📊 Admin Panel Features

### Dashboard (`admin/dashboard.html`)
- Overview statistics
- Recent bookings
- Pending actions
- Top guides
- Quick stats

### User Management (`admin/users.html`)
- View all users
- Edit user details
- Deactivate accounts
- Search & filter

### Guide Management (`admin/guides.html`)
- Verify applications
- Manage certifications
- View performance
- Approve/reject guides

### Booking Management (`admin/bookings.html`)
- View all bookings
- Booking status
- Resolve disputes
- Generate reports

### Payment Management (`admin/payments.html`)
- Payment records
- Refund processing
- Payment disputes
- Financial reports

## 🚀 Quick Start

### 1. Database Setup
```sql
-- Import database
mysql -u root -p < tourguidesystem.sql
mysql -u root -p tourguidesystem < tourguidesystem-data.sql
```

### 2. Configure Database
Edit `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('DB_NAME', 'tourguidesystem');
```

### 3. Start Server
- Start Apache & MySQL in XAMPP
- Access: `http://localhost/tour-guide-zamboanga/`

### 4. Test Accounts
- **Admin**: admin@tourismozamboanga.com / admin123
- **Guide**: maria@guide.com / guide123
- **Tourist**: john@tourist.com / tourist123

## 📝 Development Notes

### CSS Organization
- All shared styles moved to `assets/css/`
- No duplicate CSS files in subfolders
- Consistent naming convention
- Modular and reusable

### File Linking
- All paths updated to new structure
- Relative paths used throughout
- Consistent across all pages

### Backend Integration
- PHP files in `includes/` folder
- Database operations centralized
- Authentication system ready
- Payment processing framework

### Next Steps
1. Complete admin panel pages
2. Implement PHP backend fully
3. Add real payment gateway
4. Create API endpoints
5. Add email notifications
6. Implement file uploads
7. Add search functionality
8. Create mobile app API

---

**Last Updated**: October 2024  
**Version**: 2.0 - Reorganized Structure
