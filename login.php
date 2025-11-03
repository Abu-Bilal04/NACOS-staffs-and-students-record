<?php
session_start();
include('config/db_connect.php'); // DB connection

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // ✅ Validate role
    $valid_roles = ['admin', 'staff', 'student'];
    if (!in_array($role, $valid_roles)) {
        echo "<script>alert('Please select a valid role!'); window.location='index.php';</script>";
        exit();
    }

    // ✅ Prepare query based on role
    switch ($role) {
        case 'admin':
            $sql = "SELECT * FROM admin WHERE username = ?";
            break;
        case 'staff':
            $sql = "SELECT * FROM staff WHERE name = ? OR staff_number = ?";
            break;
        case 'student':
            $sql = "SELECT * FROM student WHERE name = ? OR reg_number = ?";
            break;
    }

    // ✅ Prepare and execute safely
    $stmt = mysqli_prepare($conn, $sql);
    if ($role === 'admin') {
        mysqli_stmt_bind_param($stmt, "s", $username);
    } else {
        mysqli_stmt_bind_param($stmt, "ss", $username, $username);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        // ✅ Verify password
        if (password_verify($password, $user['password'])) {
            // ✅ Start session
            $_SESSION['role'] = $role;
            $_SESSION['username'] = $user['name'] ?? $user['username'];
            $_SESSION['user_id'] = $user[$role . '_id'] ?? null;

            // ✅ Redirect based on role
            switch ($role) {
                case 'admin':
                    header("Location: admin/dashboard.php");
                    break;
                case 'staff':
                    header("Location: staff/dashboard.php");
                    break;
                case 'student':
                    header("Location: student/dashboard.php");
                    break;
            }
            exit();
        } else {
            echo "<script>alert('Invalid password!'); window.location='index.php';</script>";
        }
    } else {
        echo "<script>alert('User not found! Please check your credentials.'); window.location='index.php';</script>";
    }
} else {
    header("Location: index.php");
    exit();
}
?>
