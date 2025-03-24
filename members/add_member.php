<?php
require_once "../config/config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    
    // Validate First Name
    $firstName = trim($_POST['first_name']);
    if (empty($firstName)) {
        $errors[] = "First name is required";
    } elseif (!preg_match("/^[a-zA-Z ]{2,30}$/", $firstName)) {
        $errors[] = "First name should only contain letters and be 2-30 characters long";
    }
    
    // Validate Last Name
    $lastName = trim($_POST['last_name']);
    if (empty($lastName)) {
        $errors[] = "Last name is required";
    } elseif (!preg_match("/^[a-zA-Z ]{2,30}$/", $lastName)) {
        $errors[] = "Last name should only contain letters and be 2-30 characters long";
    }
    
    // Validate Email
    $email = trim($_POST['email']);
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT member_id FROM Member WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = "Email already exists";
        }
    }
    
    // Validate Phone Number
    $phone = trim($_POST['phone_no']);
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    } elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $errors[] = "Phone number should be 10 digits";
    }
    
    // Validate Dates
    $birthDate = $_POST['birth_date'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    
    if (empty($birthDate) || empty($startDate) || empty($endDate)) {
        $errors[] = "All dates are required";
    } else {
        // Validate birth date (must be in the past)
        if (strtotime($birthDate) > time()) {
            $errors[] = "Birth date must be in the past";
        }
        
        // Validate start date (must not be in the past)
        if (strtotime($startDate) < strtotime('today')) {
            $errors[] = "Start date must not be in the past";
        }
        
        // Validate end date (must be after start date)
        if (strtotime($endDate) <= strtotime($startDate)) {
            $errors[] = "End date must be after start date";
        }
    }

    // Validate Password
    $password = trim($_POST['password']);
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }

    if (empty($errors)) {
        $joinDate = date('Y-m-d');
        $price = $_POST['price'];
        
        $sql = "INSERT INTO Member (first_name, last_name, email, password, phone_no, birth_date, join_date, start_date, end_date, price) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssd", 
            $firstName, 
            $lastName, 
            $email, 
            $password,
            $phone, 
            $birthDate, 
            $joinDate, 
            $startDate, 
            $endDate, 
            $price
        );

        if ($stmt->execute()) {
            header("Location: view_members.php");
            exit;
        } else {
            $errors[] = "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Member - Gym Management System</title>
    <link rel="stylesheet" href="../assets/css/forms.css">
    <script>
        function validateForm() {
            const form = document.getElementById('addMemberForm');
            const firstName = form.first_name.value.trim();
            const lastName = form.last_name.value.trim();
            const email = form.email.value.trim();
            const phone = form.phone_no.value.trim();
            const birthDate = new Date(form.birth_date.value);
            const startDate = new Date(form.start_date.value);
            const endDate = new Date(form.end_date.value);
            const today = new Date();
            
            let isValid = true;
            let errors = [];

            // Name validations
            if (!/^[a-zA-Z ]{2,30}$/.test(firstName)) {
                errors.push("First name should only contain letters and be 2-30 characters long");
                isValid = false;
            }
            
            if (!/^[a-zA-Z ]{2,30}$/.test(lastName)) {
                errors.push("Last name should only contain letters and be 2-30 characters long");
                isValid = false;
            }

            // Email validation
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                errors.push("Please enter a valid email address");
                isValid = false;
            }

            // Phone validation
            if (!/^[0-9]{10}$/.test(phone)) {
                errors.push("Phone number should be 10 digits");
                isValid = false;
            }

            // Date validations
            if (birthDate >= today) {
                errors.push("Birth date must be in the past");
                isValid = false;
            }

            if (startDate < today) {
                errors.push("Start date must not be in the past");
                isValid = false;
            }

            if (endDate <= startDate) {
                errors.push("End date must be after start date");
                isValid = false;
            }

            if (!isValid) {
                alert(errors.join("\n"));
            }
            return isValid;
        }
    </script>
</head>
<body>
    <div class="form-container">
        <h1 class="form-title">Add New Member</h1>
        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="post" id="addMemberForm" onsubmit="return validateForm()">
            <div class="form-group">
                <label>First Name:</label>
                <input type="text" name="first_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Last Name:</label>
                <input type="text" name="last_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" class="form-control" required 
                       minlength="8" pattern=".{8,}"
                       title="Password must be at least 8 characters long">
            </div>

            <div class="form-group">
                <label>Phone Number:</label>
                <input type="tel" name="phone_no" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Birth Date:</label>
                <input type="date" name="birth_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Start Date:</label>
                <input type="date" name="start_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label>End Date:</label>
                <input type="date" name="end_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Price:</label>
                <input type="number" name="price" class="form-control" required 
                       step="50" min="0" 
                       title="Please enter the membership price">
            </div>

            <button type="submit" class="submit-btn">Add Member</button>
        </form>
        <a href="../admin/dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>