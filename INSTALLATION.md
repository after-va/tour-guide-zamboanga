# Installation Guide - Tourismo Zamboanga

## Prerequisites

- XAMPP (or similar Apache + MySQL + PHP environment)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web browser (Chrome, Firefox, Safari, or Edge)

## Step-by-Step Installation

### 1. Setup XAMPP

1. Install XAMPP from https://www.apachefriends.org/
2. Start Apache and MySQL services from XAMPP Control Panel

### 2. Create Database

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create a new database named `tour`
3. Select the `tour` database

### 3. Import SQL Files

Import the SQL files in this exact order:

1. **Main Schema**: `sql/tourguidesystem.sql`
   - Creates all tables, views, and indexes

2. **Initial Data**: `sql/tourguidesystem-data.sql`
   - Adds default roles, payment methods, and tourist spots

3. **Location Data** (Optional): `sql/ph-location.sql`
   - Philippine regions, provinces, cities, barangays

4. **Cleanup** (Optional): `sql/00-CLEANUP-before-import.sql`
   - Cleans up duplicate location data

5. **Barangay Data** (Optional): Run in order:
   - `sql/barangays-PART1-cities-11-50.sql`
   - `sql/barangays-PART2-cities-51-110.sql`
   - `sql/barangays-PART3-cities-111-130.sql`
   - `sql/barangays-PART4-cities-131-162.sql`

6. **Ensure Roles Exist**: `sql/ensure-roles.sql`
   - Ensures Admin, Guide, and Tourist roles exist

7. **Create Admin User**: `sql/setup-admin.sql`
   - Creates default admin account
   - Username: `admin`
   - Password: `admin123`
   - **IMPORTANT**: Change this password after first login!

8. **Fix Admin Login**: `sql/fix-admin-complete.sql`
   - Ensures admin has correct Account_Role entry
   - Fixes any login issues

9. **Add Role Approval**: `sql/add-role-approval.sql`
   - Adds is_approved column for guide approval workflow


### 4. Configure Database Connection

1. Open `classes/database.php`
2. Update the database credentials if needed:
   ```php
   private $host = "localhost";
   private $username = "root";
   private $password = "";
   private $dbname = "tour";
   ```

### 5. Access the Application

1. Open your web browser
2. Navigate to: `http://localhost/tour-guide-zamboanga/`
3. You should see the login page

## Default Accounts

After running `setup-admin.sql`:

**Admin Account:**
- Username: `admin`
- Password: `admin123`

**Note**: You'll need to register tourist and guide accounts through the registration pages.

## Testing the System

### Register a Tourist

1. Go to: `http://localhost/tour-guide-zamboanga/registration/tourist-registration.php`
2. Fill in the registration form
3. Login with your new credentials
4. You'll be redirected to the tourist dashboard

### Register a Guide

1. Go to: `http://localhost/tour-guide-zamboanga/registration/guide-registration.php`
2. Fill in the registration form
3. Login with your new credentials
4. You'll be redirected to the guide dashboard

### Admin Functions

1. Login as admin
2. Add tourist spots via Admin Dashboard → Manage Spots → Add New Spot
3. Create tour packages via Admin Dashboard → Manage Packages → Add New Package
4. Manage bookings and users

## Common Issues & Solutions

### Issue: "Access denied for user 'root'@'localhost'"
**Solution**: Check your MySQL credentials in `classes/database.php`

### Issue: "Table doesn't exist"
**Solution**: Make sure you imported `tourguidesystem.sql` first

### Issue: "Cannot login"
**Solution**: 
- Verify the user exists in the database
- Check that `User_Login` and `Account_Role` tables have data
- Ensure password is hashed correctly

### Issue: "Page not found"
**Solution**: 
- Verify XAMPP Apache is running
- Check that files are in `htdocs/tour-guide-zamboanga/`
- Clear browser cache

### Issue: "Blank page or PHP errors"
**Solution**:
- Check PHP error logs in XAMPP
- Enable error reporting in `config.php`
- Verify all required PHP extensions are enabled

## Directory Permissions

Ensure the following directories have write permissions (for future file uploads):
- `uploads/` (create this directory if needed)
- `logs/` (create this directory if needed)

## Verification Checklist

- [ ] Database `tour` created
- [ ] All SQL files imported successfully
- [ ] Database connection configured
- [ ] Apache and MySQL running
- [ ] Can access login page
- [ ] Can login as admin
- [ ] Can register as tourist
- [ ] Can register as guide
- [ ] Can browse packages and spots
- [ ] Can create bookings

## Next Steps

1. **Change Admin Password**: Login as admin and change the default password
2. **Add Content**: Add tourist spots and tour packages
3. **Test Booking Flow**: Create a test booking as a tourist
4. **Configure Email** (Optional): Setup email notifications
5. **Customize**: Modify branding and content as needed

## Support

If you encounter issues:
1. Check the error logs in XAMPP
2. Review the README.md for system architecture
3. Verify all installation steps were completed
4. Check database table structure matches the SQL files

## Security Recommendations

Before deploying to production:
1. Change all default passwords
2. Update database credentials
3. Disable error display (`display_errors = 0`)
4. Enable HTTPS
5. Set secure session cookies
6. Implement rate limiting
7. Add CSRF protection
8. Validate and sanitize all inputs
9. Regular database backups
10. Keep PHP and MySQL updated

## Database Backup

To backup your database:
```bash
mysqldump -u root -p tour > backup_tour_$(date +%Y%m%d).sql
```

To restore:
```bash
mysql -u root -p tour < backup_tour_YYYYMMDD.sql
```

## Troubleshooting Commands

Check if database exists:
```sql
SHOW DATABASES LIKE 'tour';
```

Check tables:
```sql
USE tour;
SHOW TABLES;
```

Check admin user:
```sql
SELECT ul.username, p.person_ID, ar.role_ID, ri.role_name
FROM User_Login ul
JOIN Person p ON ul.person_ID = p.person_ID
JOIN Account_Role ar ON ul.login_ID = ar.login_ID
JOIN Role_Info ri ON ar.role_ID = ri.role_ID
WHERE ul.username = 'admin';
```

## Development Mode

For development, you can enable detailed error reporting in `config.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

**Remember to disable this in production!**
