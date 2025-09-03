<?php
session_start();
include 'config.php';

if (!isset($_SESSION['manager_id'])) {
    header("Location: manager_login.php");
    exit;
}

$id = (int)$_SESSION['manager_id']; 

$msg = '';
$error = '';
$password_sql_part = '';


$query = mysqli_query($conn, "SELECT * FROM managers WHERE id=$id");
$data = mysqli_fetch_assoc($query);

$profile_pic = $data['profile_pic'] ?? ''; 

if (isset($_POST['update'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    
    
    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $password_sql_part = ", password='$hashed_password'";
    }

    
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
        $fileName = $_FILES['profile_pic']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = 'profile_'.$id.'_'.time().'.'.$fileExtension;
            $uploadFileDir = './uploads/';
            if (!is_dir($uploadFileDir)) mkdir($uploadFileDir, 0755, true);
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $profile_pic = $newFileName;
            } else {
                $error = "Error uploading profile picture.";
            }
        } else {
            $error = "Only jpg, jpeg, png, gif files allowed for profile picture.";
        }
    }

    
    if (empty($error)) {
        $sql = "UPDATE managers SET fullname='$fullname', email='$email', phone='$phone', profile_pic='$profile_pic' $password_sql_part WHERE id=$id";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $msg = "Profile updated successfully!";
            
            $query = mysqli_query($conn, "SELECT * FROM managers WHERE id=$id");
            $data = mysqli_fetch_assoc($query);
        } else {
            $error = "Failed to update profile: " . mysqli_error($conn);
        }
    }
}

$profilePicUrl = !empty($data['profile_pic']) ? 'uploads/' . $data['profile_pic'] : 'default-profile.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manager Profile</title>
    <style>
        body {
            font-family: Arial;
            background: #CDA45E;
            padding: 20px;
        }
        .container {
            background: white;
            max-width: 550px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }
        h2 {
            text-align: center;
        }
        .profile-pic {
            display: block;
            margin: 0 auto 15px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #CDA45E;
            cursor: pointer;
        }
        .info-row {
            margin-bottom: 15px;
        }
        .info-label {
            font-weight: bold;
        }
        .info-value {
            margin-left: 10px;
            display: inline-block;
        }
        input[type="text"], input[type="email"], input[type="password"], input[type="file"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            display: none;
            box-sizing: border-box;
        }
        .btn {
            padding: 10px 15px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        .btn-edit {
            background: #333;
            color: white;
            width: 100%;
        }
        .btn-save {
            background: #333;
            color: white;
            width: 100%;
            display: none;
        }
        .btn-cancel {
            background: #333;
            color: white;
            width: 100%;
            margin-top: 5px;
            display: none;
        }
        .success, .error-msg {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .success {
            color: green;
        }
        .error-msg {
            color: red;
        }
        .logout-btn, .back-btn {
            margin-top: 15px;
            background:#333;
            color: white;
            width: 48%;
            display: inline-block;
            text-align: center;
            text-decoration: none;
            padding: 10px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Manager Profile</h2>

    <?php if ($msg): ?>
        <p class="success"><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p class="error-msg"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <img src="<?= htmlspecialchars($profilePicUrl) ?>" alt="Profile Picture" class="profile-pic" id="profilePicPreview" title="Click to change profile picture">

    <form method="POST" enctype="multipart/form-data" id="profileForm">
        <div class="info-row">
            <span class="info-label">Full Name:</span>
            <span class="info-value" id="fullnameText"><?= htmlspecialchars($data['fullname']) ?></span>
            <input type="text" name="fullname" id="fullnameInput" value="<?= htmlspecialchars($data['fullname']) ?>" required>
        </div>
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span class="info-value" id="emailText"><?= htmlspecialchars($data['email']) ?></span>
            <input type="email" name="email" id="emailInput" value="<?= htmlspecialchars($data['email']) ?>" required>
        </div>
        <div class="info-row">
            <span class="info-label">Phone:</span>
            <span class="info-value" id="phoneText"><?= htmlspecialchars($data['phone']) ?></span>
            <input type="text" name="phone" id="phoneInput" value="<?= htmlspecialchars($data['phone']) ?>" required>
        </div>
        <div class="info-row">
            <span class="info-label">New Password:</span>
            <input type="password" name="password" id="passwordInput" placeholder="Leave blank to keep current">
        </div>
        <div class="info-row">
            <label for="profile_pic" class="info-label">Profile Picture:</label>
            <input type="file" name="profile_pic" id="profilePicInput" accept="image/*">
        </div>
        <button type="button" class="btn btn-edit" id="editBtn">Edit Profile</button>
        <button type="submit" name="update" class="btn btn-save" id="saveBtn">Save Changes</button>
        <button type="button" class="btn btn-cancel" id="cancelBtn">Cancel</button>
    </form>

    <a href="dashboard_manager.php" class="back-btn">Back to Dashboard</a>
    <a href="manager_logout.php" class="logout-btn">Logout</a>
</div>

<script>
    const editBtn = document.getElementById('editBtn');
    const saveBtn = document.getElementById('saveBtn');
    const cancelBtn = document.getElementById('cancelBtn');

    const fullnameText = document.getElementById('fullnameText');
    const emailText = document.getElementById('emailText');
    const phoneText = document.getElementById('phoneText');

    const fullnameInput = document.getElementById('fullnameInput');
    const emailInput = document.getElementById('emailInput');
    const phoneInput = document.getElementById('phoneInput');
    const passwordInput = document.getElementById('passwordInput');
    const profilePicInput = document.getElementById('profilePicInput');
    const profilePicPreview = document.getElementById('profilePicPreview');

    function toggleEditMode(editMode) {
        if (editMode) {
            fullnameText.style.display = 'none';
            emailText.style.display = 'none';
            phoneText.style.display = 'none';

            fullnameInput.style.display = 'block';
            emailInput.style.display = 'block';
            phoneInput.style.display = 'block';
            passwordInput.style.display = 'block';
            profilePicInput.style.display = 'block';

            editBtn.style.display = 'none';
            saveBtn.style.display = 'block';
            cancelBtn.style.display = 'block';
        } else {
            fullnameText.style.display = 'inline-block';
            emailText.style.display = 'inline-block';
            phoneText.style
        fullnameInput.style.display = 'none';
        emailInput.style.display = 'none';
        phoneInput.style.display = 'none';
        passwordInput.style.display = 'none';
        profilePicInput.style.display = 'none';
        profilePicInput.value = '';

        editBtn.style.display = 'block';
        saveBtn.style.display = 'none';
        cancelBtn.style.display = 'none';

        passwordInput.value = '';
    }
}

editBtn.addEventListener('click', () => {
    toggleEditMode(true);
});

cancelBtn.addEventListener('click', () => {
    
    fullnameInput.value = fullnameText.textContent;
    emailInput.value = emailText.textContent;
    phoneInput.value = phoneText.textContent;
    passwordInput.value = '';
    profilePicInput.value = '';

    toggleEditMode(false);
});


profilePicPreview.addEventListener('click', () => {
    if (profilePicInput.style.display === 'block') {
        profilePicInput.click();
    }
});


profilePicInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(evt) {
            profilePicPreview.src = evt.target.result;
        };
        reader.readAsDataURL(file);
    }
});


toggleEditMode(false);
</script></body></html>