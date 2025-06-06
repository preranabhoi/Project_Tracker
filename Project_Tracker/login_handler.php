<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$role || !$email || !$password) {
        die("<p style='color:red;'>All fields are required.</p>");
    }

    if ($role === 'faculty') {
        $sql = "SELECT * FROM users WHERE email = ? AND role = 'faculty'";
    } elseif ($role === 'student') {
        $sql = "SELECT * FROM users WHERE email = ? AND role = 'student'";
    } else {
        die("<p style='color:red;'>Invalid role selected.</p>");
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $role;
            $_SESSION['name'] = $user['name'];

            // Redirect based on role
            if ($role === 'faculty') {
                header("Location: faculty_dashboard.php");
            } else {
                header("Location: student_dashboard.php");
            }
            exit();
        } else {
            echo "<p style='color:red;'>Incorrect password.</p>";
        }
    } else {
        echo "<p style='color:red;'>Email not found.</p>";
    }
} else {
    echo "<p style='color:red;'>Invalid request method.</p>";
}
?>
