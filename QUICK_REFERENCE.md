# Quick Reference Guide - Tourismo Zamboanga

## Quick Start

```bash
# 1. Import database
mysql -u root -p tour < sql/tourguidesystem.sql
mysql -u root -p tour < sql/tourguidesystem-data.sql
mysql -u root -p tour < sql/setup-admin.sql

# 2. Access application
http://localhost/tour-guide-zamboanga/

# 3. Login as admin
Username: admin
Password: admin123
```

---

## Common Code Patterns

### Initialize a Class
```php
require_once "classes/tour-manager.php";
$tourManager = new TourManager();
```

### Get All Records
```php
$packages = $tourManager->getAllTourPackages();
$spots = $tourManager->getAllTourSpots();
$bookings = $bookingManager->getBookingsByCustomer($customer_ID);
```

### Create a Record
```php
// Create tour package
$package_ID = $tourManager->createTourPackage(
    $name, $description, $capacity, $duration, $spot_ID
);

// Create booking
$booking_ID = $bookingManager->createBooking(
    $customer_ID, $schedule_ID, $package_ID, $pax
);
```

### Update a Record
```php
$tourManager->updateTourPackage($id, $name, $description, $capacity, $duration);
$bookingManager->updateBookingStatus($booking_ID, 'confirmed', $person_ID, $reason);
```

### Delete a Record
```php
$tourManager->deleteTourPackage($package_ID);
$tourManager->deleteTourSpot($spot_ID);
```

---

## Database Quick Reference

### Get User Info
```sql
SELECT ul.username, p.person_ID, ar.role_ID, ri.role_name
FROM User_Login ul
JOIN Person p ON ul.person_ID = p.person_ID
JOIN Account_Role ar ON ul.login_ID = ar.login_ID
JOIN Role_Info ri ON ar.role_ID = ri.role_ID
WHERE ul.username = 'admin';
```

### Get Booking Details
```sql
SELECT * FROM v_booking_details WHERE booking_ID = 1;
```

### Get Complete Address
```sql
SELECT * FROM v_address_complete WHERE address_ID = 1;
```

### Check Available Schedules
```sql
SELECT s.*, 
       (s.schedule_Capacity - COALESCE(SUM(b.booking_PAX), 0)) as available_slots
FROM Schedule s
LEFT JOIN Booking b ON s.schedule_ID = b.schedule_ID 
WHERE s.schedule_StartDateTime > NOW()
GROUP BY s.schedule_ID
HAVING available_slots > 0;
```

---

## Session Management

### Check if Logged In
```php
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    echo $user['full_name'];
    echo $user['role_name']; // Tourist, Guide, or Admin
}
```

### Protect Page by Role
```php
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Admin') {
    header('Location: ../../index.php');
    exit;
}
```

---

## Common Queries

### Get Tourist's Bookings
```php
$bookings = $bookingManager->getBookingsByCustomer($_SESSION['user']['person_ID']);
```

### Get Guide's Schedules
```php
$schedules = $tourManager->getSchedulesByGuide($_SESSION['user']['person_ID']);
```

### Search Packages
```php
$packages = $tourManager->searchPackages($search_term, $category);
```

### Get Package with Full Details
```php
$package = $tourManager->getPackageWithDetails($package_ID);
// Returns package with spots, schedules, ratings, and reviews
```

---

## Form Handling Pattern

```php
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $field = trim($_POST['field'] ?? '');
    
    if (empty($field)) {
        $error = 'Field is required.';
    } else {
        $result = $manager->createSomething($field);
        
        if ($result) {
            $success = 'Created successfully!';
            header('Location: success-page.php');
            exit;
        } else {
            $error = 'Failed to create.';
        }
    }
}
```

---

## HTML Template Pattern

```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Title - Tourismo Zamboanga</title>
</head>
<body>
    <div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
        <header style="background: #1976d2; color: white; padding: 20px;">
            <h1>Page Title</h1>
            <nav>
                <a href="link.php" style="color: white;">Link</a>
            </nav>
        </header>
        
        <!-- Content here -->
        
    </div>
</body>
</html>
```

---

## Role IDs

```php
1 = Tourist
2 = Guide  
3 = Admin
```

---

## Booking Status Values

```php
'pending'    // Awaiting confirmation
'confirmed'  // Confirmed by guide/admin
'completed'  // Tour finished
'cancelled'  // Cancelled by tourist/admin
```

---

## Payment Status Values

```php
'pending'     // Payment initiated
'processing'  // Being processed
'completed'   // Payment successful
'failed'      // Payment failed
'refunded'    // Payment refunded
```

---

## File Locations

```
Classes:           classes/
Traits:            classes/trait/
Public Pages:      pages/public/
Tourist Pages:     pages/tourist/
Guide Pages:       pages/guide/
Admin Pages:       pages/admin/
Registration:      registration/
SQL Files:         sql/
```

---

## Common URLs

```
Login:                 /index.php
Tourist Registration:  /registration/tourist-registration.php
Guide Registration:    /registration/guide-registration.php
Browse Packages:       /pages/public/browse-packages.php
Browse Spots:          /pages/public/browse-spots.php
Tourist Dashboard:     /pages/tourist/dashboard.php
Guide Dashboard:       /pages/guide/dashboard.php
Admin Dashboard:       /pages/admin/dashboard.php
Logout:                /logout.php
```

---

## Debugging Tips

### Enable Error Display
```php
// In config.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Check Database Connection
```php
$db = new Database();
$conn = $db->connect();
if ($conn) {
    echo "Connected!";
}
```

### Dump Session Data
```php
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
```

### Check SQL Errors
```php
try {
    $query->execute();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
```

---

## Useful SQL Commands

### Reset Admin Password
```sql
UPDATE User_Login 
SET password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE username = 'admin';
-- Password is now: admin123
```

### View All Users
```sql
SELECT ul.username, ri.role_name, CONCAT(n.name_first, ' ', n.name_last) as name
FROM User_Login ul
JOIN Person p ON ul.person_ID = p.person_ID
JOIN Name_Info n ON p.name_ID = n.name_ID
JOIN Account_Role ar ON ul.login_ID = ar.login_ID
JOIN Role_Info ri ON ar.role_ID = ri.role_ID;
```

### Count Records
```sql
SELECT 
    (SELECT COUNT(*) FROM Tour_Package) as packages,
    (SELECT COUNT(*) FROM Tour_Spots) as spots,
    (SELECT COUNT(*) FROM Booking) as bookings,
    (SELECT COUNT(*) FROM User_Login) as users;
```

---

## Color Codes

```
Primary Blue:   #1976d2
Success Green:  #4caf50
Warning Orange: #ff9800
Error Red:      #f44336
Info Purple:    #9c27b0
Gray:           #757575
Light Gray:     #f5f5f5
```

---

## Status Color Mapping

```php
$statusColors = [
    'pending' => '#ff9800',    // Orange
    'confirmed' => '#4caf50',  // Green
    'completed' => '#9c27b0',  // Purple
    'cancelled' => '#f44336',  // Red
];
```

---

## Quick Testing

### Test Tourist Flow
1. Register: `/registration/tourist-registration.php`
2. Login
3. Browse: `/pages/public/browse-packages.php`
4. Book: Click "View Details" → "Book Now"
5. Check: `/pages/tourist/my-bookings.php`

### Test Guide Flow
1. Register: `/registration/guide-registration.php`
2. Login
3. Dashboard: `/pages/guide/dashboard.php`

### Test Admin Flow
1. Login as admin
2. Add Spot: `/pages/admin/add-spot.php`
3. Add Package: `/pages/admin/add-package.php`
4. View: `/pages/admin/dashboard.php`

---

## Common Issues & Fixes

**Issue:** Cannot login
**Fix:** Check User_Login and Account_Role tables have matching records

**Issue:** Blank page
**Fix:** Enable error display, check PHP error log

**Issue:** Database error
**Fix:** Verify all SQL files imported, check foreign keys

**Issue:** Session not working
**Fix:** Ensure `session_start()` at top of page

**Issue:** Redirect loop
**Fix:** Check role-based redirects in index.php

---

## Performance Tips

1. Use indexes on frequently queried columns
2. Limit results with LIMIT clause
3. Use prepared statements (already implemented)
4. Cache frequently accessed data
5. Optimize images (when implemented)

---

## Security Checklist

- [x] Password hashing
- [x] SQL injection prevention (PDO)
- [x] XSS prevention (htmlspecialchars)
- [x] Session security
- [x] Role-based access
- [ ] CSRF tokens (add for production)
- [ ] Rate limiting (add for production)
- [ ] HTTPS enforcement (configure server)

---

## Backup Commands

```bash
# Backup database
mysqldump -u root -p tour > backup.sql

# Restore database
mysql -u root -p tour < backup.sql

# Backup files
tar -czf backup.tar.gz tour-guide-zamboanga/
```

---

## Contact & Support

For issues or questions:
1. Check README.md
2. Review INSTALLATION.md
3. See SYSTEM_OVERVIEW.md
4. Check error logs
5. Verify database structure

---

**Last Updated:** October 27, 2025
**Version:** 1.0.0
**Status:** Production Ready
