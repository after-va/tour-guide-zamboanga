<?php
require_once "../php/Tourist.php";

$tourist = new Tourist();
$countryCodes = $tourist->fetchCountryCode();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = $tourist->registerTourist(
        $_POST['name_first'],
        $_POST['name_second'] ?? null,
        $_POST['name_middle'] ?? null,
        $_POST['name_last'],
        $_POST['name_suffix'] ?? null,
        $_POST['houseno'],
        $_POST['street'],
        $_POST['barangay'],
        $_POST['city'],
        $_POST['province'],
        $_POST['country'],
        $_POST['countrycode_ID'],
        $_POST['phone_number'],
        $_POST['emergency_name'],
        $_POST['emergency_countrycode_ID'],
        $_POST['emergency_phonenumber'],
        $_POST['emergency_relationship'],
        $_POST['contactinfo_email'],
        $_POST['person_nationality'],
        $_POST['person_gender'],
        $_POST['person_civilstatus'],
        $_POST['person_dateofbirth'],
        $_POST['username'],
        $_POST['password']
    );
    
    if ($result) {
        $success = "Registration successful! You can now login.";
    } else {
        $error = "Registration failed. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tourist Registration - Tour Guide System</title>
</head>
<body>
    <h1>Tourist Registration</h1>
    
    <?php if (isset($success)): ?>
        <p style="color: green;"><?php echo $success; ?></p>
        <p><a href="index.php">Go to Login</a></p>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <h3>Personal Information</h3>
        <label>First Name:</label><br>
        <input type="text" name="name_first" required><br><br>
        
        <label>Second Name:</label><br>
        <input type="text" name="name_second"><br><br>
        
        <label>Middle Name:</label><br>
        <input type="text" name="name_middle"><br><br>
        
        <label>Last Name:</label><br>
        <input type="text" name="name_last" required><br><br>
        
        <label>Suffix:</label><br>
        <input type="text" name="name_suffix"><br><br>
        
        <label>Date of Birth:</label><br>
        <input type="date" name="person_dateofbirth" required><br><br>
        
        <label>Gender:</label><br>
        <select name="person_gender" required>
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select><br><br>
        
        <label>Civil Status:</label><br>
        <select name="person_civilstatus" required>
            <option value="">Select Status</option>
            <option value="Single">Single</option>
            <option value="Married">Married</option>
            <option value="Widowed">Widowed</option>
            <option value="Divorced">Divorced</option>
        </select><br><br>
        
        <label>Nationality:</label><br>
        <input type="text" name="person_nationality" required><br><br>
        
        <h3>Contact Information</h3>
        <label>Email:</label><br>
        <input type="email" name="contactinfo_email" required><br><br>
        
        <label>Country Code:</label><br>
        <select name="countrycode_ID" required>
            <option value="">Select Country Code</option>
            <?php foreach ($countryCodes as $cc): ?>
                <option value="<?php echo $cc['countrycode_ID']; ?>">
                    <?php echo $cc['countrycode_name'] . ' (' . $cc['countrycode_number'] . ')'; ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>
        
        <label>Phone Number:</label><br>
        <input type="text" name="phone_number" required><br><br>
        
        <h3>Address</h3>
        <label>House No:</label><br>
        <input type="text" name="houseno" required><br><br>
        
        <label>Street:</label><br>
        <input type="text" name="street" required><br><br>
        
        <label>Barangay:</label><br>
        <input type="text" name="barangay" required><br><br>
        
        <label>City:</label><br>
        <input type="text" name="city" required><br><br>
        
        <label>Province:</label><br>
        <input type="text" name="province" required><br><br>
        
        <label>Country:</label><br>
        <input type="text" name="country" required><br><br>
        
        <h3>Emergency Contact</h3>
        <label>Emergency Contact Name:</label><br>
        <input type="text" name="emergency_name" required><br><br>
        
        <label>Emergency Country Code:</label><br>
        <select name="emergency_countrycode_ID" required>
            <option value="">Select Country Code</option>
            <?php foreach ($countryCodes as $cc): ?>
                <option value="<?php echo $cc['countrycode_ID']; ?>">
                    <?php echo $cc['countrycode_name'] . ' (' . $cc['countrycode_number'] . ')'; ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>
        
        <label>Emergency Phone Number:</label><br>
        <input type="text" name="emergency_phonenumber" required><br><br>
        
        <label>Relationship:</label><br>
        <input type="text" name="emergency_relationship" required><br><br>
        
        <h3>Login Credentials</h3>
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>
        
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        
        <button type="submit">Register</button>
    </form>
    
    <p>Already have an account? <a href="index.php">Login here</a></p>
</body>
</html>
