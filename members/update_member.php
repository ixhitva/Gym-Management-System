<?php
require_once "../config/config.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM Member WHERE member_id = $id";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        $member = $result->fetch_assoc();
    } else {
        echo "Member not found!";
        exit;
    }
} else {
    echo "ID not provided!";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = $_POST['first_name'];
    $lastName  = $_POST['last_name'];
    $email     = $_POST['email'];
    $phone     = $_POST['phone_no'];
    $birthDate = $_POST['birth_date'];
    $joinDate  = $_POST['join_date'];
    $startDate = $_POST['start_date'];
    $endDate   = $_POST['end_date'];
    $price     = $_POST['price'];
    
    $sqlUpdate = "UPDATE Member
                  SET first_name='$firstName', last_name='$lastName', email='$email', phone_no='$phone',
                      birth_date='$birthDate', join_date='$joinDate', start_date='$startDate', end_date='$endDate', price='$price'
                  WHERE member_id = $id";
    
    if ($conn->query($sqlUpdate) === TRUE) {
        header("Location: view_members.php");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Member - Gym Management System</title>
    <link rel="stylesheet" href="../assets/css/forms.css">
</head>
<body>
    <div class="form-container">
        <h1 class="form-title">Update Member Details</h1>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label>First Name:</label>
                <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($member['first_name']); ?>" required>
            </div>

            <div class="form-group">
                <label>Last Name:</label>
                <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($member['last_name']); ?>" required>
            </div>

            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($member['email']); ?>" required>
            </div>

            <div class="form-group">
                <label>Phone No:</label>
                <input type="text" name="phone_no" class="form-control" value="<?php echo htmlspecialchars($member['phone_no']); ?>" required>
            </div>

            <div class="form-group">
                <label>Birth Date:</label>
                <input type="date" name="birth_date" class="form-control" value="<?php echo htmlspecialchars($member['birth_date']); ?>" required>
            </div>

            <div class="form-group">
                <label>Join Date:</label>
                <input type="date" name="join_date" class="form-control" value="<?php echo htmlspecialchars($member['join_date']); ?>" required>
            </div>

            <div class="form-group">
                <label>Start Date:</label>
                <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($member['start_date']); ?>" required>
            </div>

            <div class="form-group">
                <label>End Date:</label>
                <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($member['end_date']); ?>" required>
            </div>

            <div class="form-group">
                <label>Price:</label>
                <input type="number" step="0.01" name="price" class="form-control" value="<?php echo htmlspecialchars($member['price']); ?>" required>
            </div>

            <button type="submit" class="submit-btn">Update Member</button>
        </form>
        <a href="view_members.php" class="back-btn">Back to Members List</a>
    </div>
</body>
</html>