<?php
session_start();
include('../config/config.php');

// If already logged in, redirect to appropriate dashboard
if (isset($_SESSION['role'])) {
    header("Location: ../" . $_SESSION['role'] . "/dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($role == 'member') {
        $sql = "SELECT * FROM Member WHERE email='$email'";
    } elseif ($role == 'trainer') {
        $sql = "SELECT * FROM Trainer WHERE email='$email'";
    } elseif ($role == 'admin') {
        // Assuming there's an Admin table
        $sql = "SELECT * FROM Admin WHERE email='$email'";
    }

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Assuming password is stored in plain text for simplicity
        if ($password == $row['password']) {
            $_SESSION['role'] = $role;
            $_SESSION['user_id'] = $row[$role . '_id'];
            header("Location: ../$role/dashboard.php");
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with this email.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gym Management System - Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }
        .login-container {
            background: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Gym Management System</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Role:</label>
                <select name="role">
                    <option value="member">Member</option>
                    <option value="trainer">Trainer</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>
