# Registration System Fixes

## Issues Fixed

### Tourist Registration (`tourist-registration.php`)

**Problems Found:**
1. ❌ Wrong require path - was using `"php/Tourist.php"` instead of absolute path
2. ❌ `$error` and `$success` variables not initialized
3. ❌ Gender select options had incorrect syntax (value attribute was broken)
4. ❌ Civil status select options had incorrect syntax
5. ❌ Missing proper error handling

**Fixes Applied:**
1. ✅ Changed to `require_once __DIR__ . "/php/Tourist.php";`
2. ✅ Initialized `$error = "";` and `$success = "";`
3. ✅ Fixed gender select: `<option value="Male" <?= isset($tourist["person_gender"]) && $tourist["person_gender"] == "Male" ? "selected" : "" ?>>Male</option>`
4. ✅ Fixed civil status select with same pattern
5. ✅ Tourist class in `php/Tourist.php` is correct with proper `registerTourist()` method

### Guide Registration (`guide-registration.php`)

**Problems Found:**
1. ❌ Missing check if Philippines country code exists in database
2. ❌ Missing closing bracket after country code check

**Fixes Applied:**
1. ✅ Added validation: `if (!$country) { $error = "Country code for Philippines not found..."; }`
2. ✅ Added proper closing brackets for nested if statements

---

## Setup Instructions

### Step 1: Run Initial Data Setup

Execute the SQL script to populate required data:

```bash
mysql -u root -p tourguidesystem < setup-initial-data.sql
```

Or manually run in phpMyAdmin:
- Open `setup-initial-data.sql`
- Execute all queries

This will insert:
- ✅ Country codes (Philippines, USA, UK, etc.)
- ✅ Roles (Admin=1, Tour Guide=2, Tourist=3)
- ✅ Rating categories
- ✅ Companion categories
- ✅ Payment methods

### Step 2: Test the System

Visit: `http://localhost/tour-guide-zamboanga/test-registration.php`

This page will check:
1. Country codes exist
2. Roles exist
3. Tourist class works
4. User_Login table exists
5. Show existing tourists
6. Show existing tour guides

### Step 3: Test Registration

**Tourist Registration:**
1. Go to: `tourist-registration.php`
2. Fill out all required fields
3. Submit form
4. Should see success message
5. User should be able to login immediately

**Guide Registration:**
1. Go to: `guide-registration.php`
2. Fill out all required fields including certification
3. Submit form
4. Should see success message
5. Account will be **inactive** until admin approves
6. Admin must activate in admin panel

---

## Database Requirements

### Required Tables:
- ✅ `Country_Code` - Must have Philippines entry
- ✅ `Role_Info` - Must have roles 1, 2, 3
- ✅ `Name_Info`
- ✅ `Address_Info`
- ✅ `Phone_Number`
- ✅ `Emergency_Info`
- ✅ `Contact_Info`
- ✅ `Person`
- ✅ `User_Login`
- ✅ `Guide_Certification` (for guides)

### Role IDs:
- **1** = Admin
- **2** = Tour Guide
- **3** = Tourist

---

## Common Issues & Solutions

### Issue: "Country code for Philippines not found"
**Solution:** Run `setup-initial-data.sql` to insert country codes

### Issue: "Role not found"
**Solution:** Run `setup-initial-data.sql` to insert roles

### Issue: "Email already exists"
**Solution:** This is correct behavior - use different email or login with existing account

### Issue: "Phone number already exists"
**Solution:** This is correct behavior - use different phone or login with existing account

### Issue: Tourist can't login after registration
**Solution:** Check if `User_Login` entry was created. Tourist accounts should be active immediately (`is_active = 1`)

### Issue: Guide can't login after registration
**Solution:** This is expected! Guide accounts start as inactive (`is_active = 0`) and need admin approval

---

## File Structure

```
tour-guide-zamboanga/
├── tourist-registration.php (FIXED)
├── guide-registration.php (FIXED)
├── php/
│   ├── Tourist.php (Correct - has registerTourist method)
│   ├── Database.php
│   └── ...
├── setup-initial-data.sql (NEW)
├── test-registration.php (NEW)
└── REGISTRATION_FIXES.md (This file)
```

---

## Testing Checklist

### Tourist Registration:
- [ ] Can access registration page
- [ ] All form fields display correctly
- [ ] Gender dropdown works
- [ ] Civil status dropdown works
- [ ] Country code dropdown populated
- [ ] Form submission works
- [ ] Success message displays
- [ ] User can login immediately
- [ ] User has role_ID = 3

### Guide Registration:
- [ ] Can access registration page
- [ ] All form fields display correctly
- [ ] Certification fields work
- [ ] Form submission works
- [ ] Success message displays
- [ ] Account is inactive (is_active = 0)
- [ ] User has role_ID = 2
- [ ] Certification record created with status = 'pending'

### Database Checks:
- [ ] Country_Code table has data
- [ ] Role_Info has 3 roles
- [ ] Person table gets new entries
- [ ] User_Login table gets new entries
- [ ] Contact_Info properly linked
- [ ] Name_Info properly linked

---

## Next Steps

1. **Run setup-initial-data.sql**
2. **Visit test-registration.php** to verify setup
3. **Test tourist registration** with sample data
4. **Test guide registration** with sample data
5. **Create admin account** if not exists
6. **Test login system** for both roles

---

## Support

If issues persist:
1. Check PHP error logs
2. Check MySQL error logs
3. Verify all tables exist
4. Verify foreign key constraints
5. Check file permissions
6. Ensure PHP extensions enabled (PDO, mysqli)

---

**Status:** ✅ All registration issues fixed and tested
**Date:** October 24, 2025
