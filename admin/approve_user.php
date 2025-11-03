<?php
session_start();
include('../config/db_connect.php');

// ✅ Restrict to admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// ✅ Ensure valid parameters are passed
if (!isset($_GET['type']) || !isset($_GET['id']) || !isset($_GET['action'])) {
    header("Location: dashboard.php");
    exit();
}

$type = $_GET['type'];      // 'student' or 'staff'
$id = intval($_GET['id']);
$action = $_GET['action'];  // 'approve' or 'reject'

// ✅ Determine table and ID field dynamically
$table = ($type === 'staff') ? 'staff' : 'student';
$idField = ($type === 'staff') ? 'staff_id' : 'student_id';

// ✅ Verify record exists
$check = mysqli_query($conn, "SELECT * FROM $table WHERE $idField = $id");
if (mysqli_num_rows($check) == 0) {
    header("Location: dashboard.php?error=notfound");
    exit();
}

// ✅ Update approval status
if ($action === 'approve') {
    $update = mysqli_query($conn, "UPDATE $table SET approval_status='Approved' WHERE $idField = $id");
    $msg = "User approved successfully.";
} elseif ($action === 'reject') {
    $update = mysqli_query($conn, "UPDATE $table SET approval_status='Rejected' WHERE $idField = $id");
    $msg = "User rejected successfully.";
} else {
    header("Location: dashboard.php");
    exit();
}

// ✅ Redirect back to correct review page
if ($type === 'staff') {
    header("Location: review_staff.php?msg=" . urlencode($msg));
} else {
    header("Location: review_student.php?msg=" . urlencode($msg));
}
exit();
?>
