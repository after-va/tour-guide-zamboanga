<?php
session_start();
require_once __DIR__ . '/classes/customer.php';

$cust = new Customer();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name_first = trim($_POST['name_first'] ?? '');
    $name_second = trim($_POST['name_second'] ?? '');
    $name_middle = trim($_POST['name_middle'] ?? '');
    $name_last = trim($_POST['name_last'] ?? '');
    $name_suffix = trim($_POST['name_suffix'] ?? '');

    $address_houseno = trim($_POST['address_houseno'] ?? '');
    $address_street = trim($_POST['address_street'] ?? '');
    $barangay_ID = (int)($_POST['barangay_ID'] ?? 0);

    $phone_country_ID = (int)($_POST['phone_country_ID'] ?? 0);
    $phone_number = trim($_POST['phone_number'] ?? '');

    $em_name = trim($_POST['em_name'] ?? '');
    $em_country_ID = (int)($_POST['em_country_ID'] ?? 0);
    $em_phone = trim($_POST['em_phone'] ?? '');
    $em_relationship = trim($_POST['em_relationship'] ?? '');

    $contact_email = trim($_POST['contact_email'] ?? '');

    $person_nationality = trim($_POST['person_nationality'] ?? '');
    $person_gender = trim($_POST['person_gender'] ?? '');
    $person_dateofbirth = trim($_POST['person_dateofbirth'] ?? '');

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($name_first==='') $errors['name_first'] = 'Required';
    if ($name_last==='') $errors['name_last'] = 'Required';
    if ($address_houseno==='') $errors['address_houseno'] = 'Required';
    if ($address_street==='') $errors['address_street'] = 'Required';
    if ($barangay_ID<=0) $errors['barangay_ID'] = 'Required';
    if ($phone_country_ID<=0) $errors['phone_country_ID'] = 'Required';
    if ($phone_number==='') $errors['phone_number'] = 'Required';
    if ($contact_email==='') $errors['contact_email'] = 'Required';
    if ($person_dateofbirth==='') $errors['person_dateofbirth'] = 'Required';
    if ($username==='') $errors['username'] = 'Required';
    if ($password==='') $errors['password'] = 'Required';

    if (!$errors){
        $res = $cust->registerTourist(
            $name_first, $name_second ?: null, $name_middle ?: null, $name_last, $name_suffix ?: null,
            $address_houseno, $address_street, $barangay_ID,
            $phone_country_ID, $phone_number,
            $em_name ?: null, $em_country_ID ?: null, $em_phone ?: null, $em_relationship ?: null,
            $contact_email,
            $person_nationality ?: null, $person_gender ?: null, $person_dateofbirth,
            $username, $password
        );
        if ($res === true){
            $success = 'Registration successful. You can now login.';
        } elseif ($res === 'username_exists'){
            $errors['username'] = 'Username already exists';
        } else {
            $errors['general'] = 'Registration failed.';
        }
    }
}

$countries = $cust->fetchCountries();
?>
<!DOCTYPE html>
<html>
<head><title>Register Tourist</title></head>
<body>
<h1>Register as Tourist</h1>
<?php if (!empty($errors['general'])): ?><p style="color:red;"><?= htmlspecialchars($errors['general']) ?></p><?php endif; ?>
<?php if ($success): ?><p style="color:green;"><?= htmlspecialchars($success) ?></p><?php endif; ?>
<form method="post">
    <h3>Personal Info</h3>
    <label>First Name</label><br>
    <input name="name_first" value="<?= htmlspecialchars($_POST['name_first'] ?? '') ?>">
    <span style="color:red;"><?= $errors['name_first'] ?? '' ?></span><br>
    <label>Second Name</label><br>
    <input name="name_second" value="<?= htmlspecialchars($_POST['name_second'] ?? '') ?>"><br>
    <label>Middle Name</label><br>
    <input name="name_middle" value="<?= htmlspecialchars($_POST['name_middle'] ?? '') ?>"><br>
    <label>Last Name</label><br>
    <input name="name_last" value="<?= htmlspecialchars($_POST['name_last'] ?? '') ?>">
    <span style="color:red;"><?= $errors['name_last'] ?? '' ?></span><br>
    <label>Suffix</label><br>
    <input name="name_suffix" value="<?= htmlspecialchars($_POST['name_suffix'] ?? '') ?>"><br>
    <label>Gender</label><br>
    <select name="person_gender">
        <option value="">--</option>
        <option value="Male" <?= (($_POST['person_gender'] ?? '')==='Male')?'selected':''; ?>>Male</option>
        <option value="Female" <?= (($_POST['person_gender'] ?? '')==='Female')?'selected':''; ?>>Female</option>
    </select><br>
    <label>Date of Birth</label><br>
    <input type="date" name="person_dateofbirth" value="<?= htmlspecialchars($_POST['person_dateofbirth'] ?? '') ?>">
    <span style="color:red;"><?= $errors['person_dateofbirth'] ?? '' ?></span><br>
    <label>Nationality</label><br>
    <input name="person_nationality" value="<?= htmlspecialchars($_POST['person_nationality'] ?? '') ?>"><br>

    <h3>Address</h3>
    <label>House No.</label><br>
    <input name="address_houseno" value="<?= htmlspecialchars($_POST['address_houseno'] ?? '') ?>">
    <span style="color:red;"><?= $errors['address_houseno'] ?? '' ?></span><br>
    <label>Street</label><br>
    <input name="address_street" value="<?= htmlspecialchars($_POST['address_street'] ?? '') ?>">
    <span style="color:red;"><?= $errors['address_street'] ?? '' ?></span><br>
    <label>Barangay ID</label><br>
    <input name="barangay_ID" value="<?= htmlspecialchars($_POST['barangay_ID'] ?? '') ?>">
    <span style="color:red;"><?= $errors['barangay_ID'] ?? '' ?></span><br>

    <h3>Contact</h3>
    <label>Country (for phone)</label><br>
    <select name="phone_country_ID">
        <option value="0">--Select Country--</option>
        <?php foreach ($countries as $c): ?>
            <option value="<?= (int)$c['country_ID'] ?>" <?= ((int)($c['country_ID']) === (int)($_POST['phone_country_ID'] ?? 0))?'selected':''; ?>><?= htmlspecialchars($c['country_name'].' '.$c['country_codenumber']) ?></option>
        <?php endforeach; ?>
    </select>
    <span style="color:red;"><?= $errors['phone_country_ID'] ?? '' ?></span><br>
    <label>Phone Number</label><br>
    <input name="phone_number" value="<?= htmlspecialchars($_POST['phone_number'] ?? '') ?>">
    <span style="color:red;"><?= $errors['phone_number'] ?? '' ?></span><br>
    <label>Email</label><br>
    <input type="email" name="contact_email" value="<?= htmlspecialchars($_POST['contact_email'] ?? '') ?>">
    <span style="color:red;"><?= $errors['contact_email'] ?? '' ?></span><br>

    <h3>Emergency Contact (optional)</h3>
    <label>Name</label><br>
    <input name="em_name" value="<?= htmlspecialchars($_POST['em_name'] ?? '') ?>"><br>
    <label>Relationship</label><br>
    <input name="em_relationship" value="<?= htmlspecialchars($_POST['em_relationship'] ?? '') ?>"><br>
    <label>Country</label><br>
    <select name="em_country_ID">
        <option value="0">--Select Country--</option>
        <?php foreach ($countries as $c): ?>
            <option value="<?= (int)$c['country_ID'] ?>" <?= ((int)($c['country_ID']) === (int)($_POST['em_country_ID'] ?? 0))?'selected':''; ?>><?= htmlspecialchars($c['country_name'].' '.$c['country_codenumber']) ?></option>
        <?php endforeach; ?>
    </select><br>
    <label>Phone</label><br>
    <input name="em_phone" value="<?= htmlspecialchars($_POST['em_phone'] ?? '') ?>"><br>

    <h3>Account</h3>
    <label>Username</label><br>
    <input name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
    <span style="color:red;"><?= $errors['username'] ?? '' ?></span><br>
    <label>Password</label><br>
    <input type="password" name="password" value="<?= htmlspecialchars($_POST['password'] ?? '') ?>">
    <span style="color:red;"><?= $errors['password'] ?? '' ?></span><br><br>

    <button type="submit">Register</button>
</form>
<p><a href="index.php">Back to Login</a></p>
</body>
</html>
