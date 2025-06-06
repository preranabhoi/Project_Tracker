<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role'], $_POST['name'], $_POST['email'], $_POST['password'])) {
    $role = $_POST['role'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    if ($role !== 'student' && $role !== 'faculty') {
        echo "<p style='color:red;'>Invalid role selected.</p>";
        exit;
    }

    $conn->begin_transaction();

    try {
        // Check if email already exists
        $checkSql = "SELECT id FROM users WHERE email = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        if ($checkResult->num_rows > 0) {
            throw new Exception("Email already registered.");
        }

        // Insert into users table
        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);
        $stmt->execute();
        $user_id = $stmt->insert_id;

        if ($role === 'student') {
            if (!isset($_POST['year'])) {
                throw new Exception("Year is required for students.");
            }
            $year = $_POST['year'];

            $sql2 = "INSERT INTO students (user_id, year) VALUES (?, ?)";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("is", $user_id, $year);
            $stmt2->execute();
        }

        $conn->commit();
        echo "<p style='color:green;'>Registration successful! <a href='auth.php'>Login Now</a></p>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<p style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p style='color:red;'>Invalid form submission.</p>";
}
?>
<link rel="stylesheet" href="register_handler.css">
