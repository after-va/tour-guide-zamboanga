# Database Schema Changes Summary

## Overview
Removed `role_ID` from the `Person` table and `person_RatingScore` field. Implemented a new `Account_Role` table to support accounts having multiple roles with separate rating scores for each role (e.g., a user can be both a tourist and a guide with different ratings).

## SQL Schema Changes (tourguidesystem.sql)

### 1. Person Table
**REMOVED:**
- `role_ID` column (foreign key to Role_Info)
- `person_RatingScore` column

**BEFORE:**
```sql
CREATE TABLE Person (
    person_ID INT AUTO_INCREMENT PRIMARY KEY,
    role_ID INT,
    name_ID INT,
    person_RatingScore DECIMAL(3,2),
    ...
    FOREIGN KEY (role_ID) REFERENCES Role_Info(role_ID)
);
```

**AFTER:**
```sql
CREATE TABLE Person (
    person_ID INT AUTO_INCREMENT PRIMARY KEY,
    name_ID INT,
    person_Nationality VARCHAR(225),
    person_Gender VARCHAR(225),
    person_DateOfBirth DATE,
    contactinfo_ID INT,
    FOREIGN KEY (name_ID) REFERENCES Name_Info(name_ID),
    FOREIGN KEY (contactinfo_ID) REFERENCES Contact_Info(contactinfo_ID)
);
```

### 2. New Account_Role Table
**ADDED:**
```sql
CREATE TABLE IF NOT EXISTS Account_Role (
    account_role_ID INT AUTO_INCREMENT PRIMARY KEY,
    login_ID INT NOT NULL,
    role_ID INT NOT NULL,
    role_rating_score DECIMAL(3,2) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (login_ID) REFERENCES User_Login(login_ID) ON DELETE CASCADE,
    FOREIGN KEY (role_ID) REFERENCES Role_Info(role_ID) ON DELETE CASCADE,
    UNIQUE KEY unique_login_role (login_ID, role_ID)
);
```

### 3. Rating Table
**CHANGED:**
- `rater_ID` → `rater_account_role_ID`
- `rated_ID` → `rated_account_role_ID`

**BEFORE:**
```sql
CREATE TABLE Rating (
    rating_ID INT AUTO_INCREMENT PRIMARY KEY,
    rater_ID INT NOT NULL,
    rated_ID INT NOT NULL,
    ...
    FOREIGN KEY (rater_ID) REFERENCES Person(person_ID),
    FOREIGN KEY (rated_ID) REFERENCES Person(person_ID)
);
```

**AFTER:**
```sql
CREATE TABLE Rating (
    rating_ID INT AUTO_INCREMENT PRIMARY KEY,
    rater_account_role_ID INT NOT NULL,
    rated_account_role_ID INT NOT NULL,
    rating_value DECIMAL(2,1) NOT NULL,
    rating_description VARCHAR(255),
    rating_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rater_account_role_ID) REFERENCES Account_Role(account_role_ID),
    FOREIGN KEY (rated_account_role_ID) REFERENCES Account_Role(account_role_ID)
);
```

### 4. Updated Indexes
**ADDED:**
```sql
CREATE INDEX idx_rating_rater ON Rating(rater_account_role_ID);
CREATE INDEX idx_account_role_login ON Account_Role(login_ID);
CREATE INDEX idx_account_role_role ON Account_Role(role_ID);
```

**CHANGED:**
```sql
-- Old: CREATE INDEX idx_rating_rated ON Rating(rated_ID);
-- New:
CREATE INDEX idx_rating_rated ON Rating(rated_account_role_ID);
```

### 5. Updated View: v_user_details
**CHANGED:**
Now joins with Account_Role table to get role information and rating scores.

```sql
CREATE OR REPLACE VIEW v_user_details AS
SELECT 
    p.person_ID,
    ul.login_ID,
    ar.role_ID,
    r.role_name,
    ar.role_rating_score as rating,
    CONCAT(n.name_first, ' ', n.name_last) as full_name,
    n.name_first,
    n.name_last,
    ci.contactinfo_email as email,
    ph.phone_number,
    ul.username,
    ul.last_login,
    ul.is_active,
    ar.is_active as role_is_active
FROM Person p
LEFT JOIN Name_Info n ON p.name_ID = n.name_ID
LEFT JOIN Contact_Info ci ON p.contactinfo_ID = ci.contactinfo_ID
LEFT JOIN Phone_Number ph ON ci.phone_ID = ph.phone_ID
LEFT JOIN User_Login ul ON p.person_ID = ul.person_ID
LEFT JOIN Account_Role ar ON ul.login_ID = ar.login_ID
LEFT JOIN Role_Info r ON ar.role_ID = r.role_ID;
```

## PHP Class Changes

### 1. User.php
**UPDATED:**
- `login()` - Now returns user roles array
- `getUserById()` - Now includes roles array
- `getAllUsers()` - Now groups and concatenates role names

**ADDED:**
- `getUserRoles($login_ID)` - Get all roles for a login account
- `addUserRole($login_ID, $role_ID)` - Add a role to an account
- `hasRole($login_ID, $role_ID)` - Check if account has specific role

### 2. Guide.php
**UPDATED:**
- `registerGuide()` - Now creates Account_Role entry after User_Login
- `getAllGuides()` - Joins with Account_Role to get guide rating
- `getGuideById()` - Joins with Account_Role to get guide rating

### 3. Tourist.php
**UPDATED:**
- `registerTourist()` - Now creates Account_Role entry after User_Login
- `getAllTourists()` - Joins with Account_Role
- `getTouristById()` - Joins with Account_Role to get tourist rating

### 4. Rating.php
**UPDATED:**
- `addRating()` - Now uses `account_role_ID` instead of `person_ID`
- `updateAccountRoleRatingScore()` - Updates rating in Account_Role table
- `getRatingsByAccountRole()` - Gets ratings for a specific account role
- `getAverageRating()` - Gets average for account role
- `hasRated()` - Checks using account role IDs

**ADDED:**
- `getAccountRoleID($person_ID, $role_ID)` - Helper to get account_role_ID

### 5. CustomPackage.php
**UPDATED:**
- `getRequestsByTourist()` - Joins with Account_Role for guide rating
- `getAllActiveOfferings()` - Joins with Account_Role for guide rating
- `getRequestMessages()` - Joins with Account_Role for role_ID

## Login Page Changes

### Updated Files:
- `admin/index.php`
- `guide/index.php`
- `tourist/index.php`

**CHANGES:**
- Now checks for roles in `$result['roles']` array
- Sets `$_SESSION['account_role_id']` for the active role
- Sets `$_SESSION['login_id']` for the user login
- Stores full `$_SESSION['roles']` array for multi-role support

## Display Page Changes

### Updated Files:
- `tourist/browse-guides.php` - Changed `person_RatingScore` to `role_rating_score`
- `tourist/guide-packages.php` - Changed `person_RatingScore` to `role_rating_score`
- `tourist/request-custom-package.php` - Changed `person_RatingScore` to `role_rating_score`

## Benefits of New Structure

1. **Multi-Role Support**: Users can now have multiple roles (e.g., be both a tourist and a guide)
2. **Separate Ratings**: Each role has its own rating score
3. **Flexible Role Management**: Roles can be activated/deactivated per account
4. **Better Data Integrity**: Ratings are tied to specific roles, not just persons
5. **Scalability**: Easy to add new roles or role-specific attributes

## Migration Notes

When migrating existing data:
1. Create Account_Role entries for all existing Person records based on their role_ID
2. Migrate person_RatingScore to role_rating_score in Account_Role
3. Update Rating table to use account_role_ID instead of person_ID
4. Ensure all User_Login records have corresponding Account_Role entries

## Session Variables

**NEW SESSION VARIABLES:**
- `$_SESSION['login_id']` - User_Login.login_ID
- `$_SESSION['account_role_id']` - Account_Role.account_role_ID for current active role
- `$_SESSION['roles']` - Array of all user roles

**EXISTING (Still Used):**
- `$_SESSION['user_id']` - Person.person_ID
- `$_SESSION['role_id']` - Current active role_ID (for backward compatibility)
- `$_SESSION['username']`
- `$_SESSION['full_name']`
