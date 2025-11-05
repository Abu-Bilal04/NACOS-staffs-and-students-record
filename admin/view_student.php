<?php
session_start();
include('../config/db_connect.php');

// Allow only admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// Validate student ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: review_student.php");
    exit();
}

$student_id = intval($_GET['id']);
$message = "";

// Handle Approve / Reject actions
if (isset($_POST['approve'])) {
    mysqli_query($conn, "UPDATE student SET approval_status='Approved' WHERE student_id=$student_id");
    $message = "<div class='alert alert-success text-center'>Student approved successfully.</div>";
}

elseif (isset($_POST['reject'])) {
    $reason = mysqli_real_escape_string($conn, $_POST['rejection_reason'] ?? '');
    
    if (empty($reason)) {
        $message = "<div class='alert alert-warning text-center'>Please provide a reason for rejection.</div>";
    } else {
        // Update status in student table
        mysqli_query($conn, "UPDATE student SET approval_status='Rejected' WHERE student_id=$student_id");

        // Insert reason into separate table
        $admin = mysqli_real_escape_string($conn, $_SESSION['username'] ?? 'admin'); // adjust if username is stored differently
        mysqli_query($conn, "
            INSERT INTO student_rejection_reasons (student_id, reason, rejected_by)
            VALUES ($student_id, '$reason', '$admin')
        ");

        $message = "<div class='alert alert-danger text-center'>Student rejected with reason recorded.</div>";
    }
}

// Fetch student details
$studentQuery = mysqli_query($conn, "SELECT * FROM student WHERE student_id = $student_id");
$student = mysqli_fetch_assoc($studentQuery);

// Fetch student credentials
$credQuery = mysqli_query($conn, "SELECT * FROM student_credentials WHERE student_id = $student_id");
$credentials = mysqli_fetch_assoc($credQuery);

// Fetch latest rejection reason (if any)
$reasonQuery = mysqli_query($conn, "SELECT reason, rejected_at, rejected_by FROM student_rejection_reasons WHERE student_id=$student_id ORDER BY rejected_at DESC LIMIT 1");
$lastReason = mysqli_fetch_assoc($reasonQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Student Details</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #f4f7fb; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    .container { max-width: 900px; margin-top: 50px; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .section-title { background-color: #003366; color: white; padding: 8px 12px; border-radius: 6px; font-size: 1.1rem; }
    .doc-link { text-decoration: none; color: #0d6efd; }
    .doc-link:hover { text-decoration: underline; }
  </style>
</head>
<body>

<div class="container">
  <h3 class="text-center text-primary mb-4"><i class="bi bi-person-badge"></i> Student Details</h3>

  <?php echo $message; ?>

  <?php if ($student): ?>
    <div class="mb-4">
      <h5 class="section-title"><i class="bi bi-info-circle"></i> Personal Information</h5>
      <div class="mt-3">
        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($student['name']); ?></p>
        <p><strong>Reg Number:</strong> <?php echo htmlspecialchars($student['reg_number']); ?></p>
        <p><strong>Level:</strong> <?php echo htmlspecialchars($student['level']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($student['address']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($student['phone']); ?></p>
        <p><strong>Status:</strong> 
          <?php 
            $status = $student['approval_status'];
            if ($status == 'Approved') echo "<span class='badge bg-success'>Approved</span>";
            elseif ($status == 'Rejected') echo "<span class='badge bg-danger'>Rejected</span>";
            else echo "<span class='badge bg-warning text-dark'>Pending</span>";
          ?>
        </p>
      </div>
    </div>

    <div class="mb-4">
      <h5 class="section-title"><i class="bi bi-file-earmark-text"></i> Uploaded Credentials</h5>
      <?php if ($credentials): ?>
        <table class="table table-bordered mt-3">
          <thead>
            <tr>
              <th>Document Type</th>
              <th>File</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>Primary Certificate</td><td><?php echo !empty($credentials['primary_certificate']) ? "<a href='../uploads/students/{$credentials['primary_certificate']}' target='_blank' class='doc-link'>View</a>" : "Not uploaded"; ?></td></tr>
            <tr><td>Batch Certificate</td><td><?php echo !empty($credentials['batch_certificate']) ? "<a href='../uploads/students/{$credentials['batch_certificate']}' target='_blank' class='doc-link'>View</a>" : "Not uploaded"; ?></td></tr>
            <tr><td>Secondary (O-Level)</td><td><?php echo !empty($credentials['olevel_certificate']) ? "<a href='../uploads/students/{$credentials['olevel_certificate']}' target='_blank' class='doc-link'>View</a>" : "Not uploaded"; ?></td></tr>
            <tr><td>Admission Letter</td><td><?php echo !empty($credentials['admission_letter']) ? "<a href='../uploads/students/{$credentials['admission_letter']}' target='_blank' class='doc-link'>View</a>" : "Not uploaded"; ?></td></tr>
            <tr><td>Recommendation Letter</td><td><?php echo !empty($credentials['recommendation_letter']) ? "<a href='../uploads/students/{$credentials['recommendation_letter']}' target='_blank' class='doc-link'>View</a>" : "Not uploaded"; ?></td></tr>
            <tr><td>School Fees Payment</td><td><?php echo !empty($credentials['school_fees_payment']) ? "<a href='../uploads/students/{$credentials['school_fees_payment']}' target='_blank' class='doc-link'>View</a>" : "Not uploaded"; ?></td></tr>
            <tr><td>Consultancy Fee Payment</td><td><?php echo !empty($credentials['consultancy_fee_payment']) ? "<a href='../uploads/students/{$credentials['consultancy_fee_payment']}' target='_blank' class='doc-link'>View</a>" : "Not uploaded"; ?></td></tr>
            <tr><td>T-Ship Payment</td><td><?php echo !empty($credentials['tship_payment']) ? "<a href='../uploads/students/{$credentials['tship_payment']}' target='_blank' class='doc-link'>View</a>" : "Not uploaded"; ?></td></tr>
            <tr><td>Departmental Payment</td><td><?php echo !empty($credentials['departmental_payment']) ? "<a href='../uploads/students/{$credentials['departmental_payment']}' target='_blank' class='doc-link'>View</a>" : "Not uploaded"; ?></td></tr>
          </tbody>
        </table>
      <?php else: ?>
        <p class="text-muted mt-3">No credentials uploaded yet.</p>
      <?php endif; ?>
    </div>

    <!-- NEW: Show previous rejection reason -->
    <?php if ($lastReason): ?>
      <div class="alert alert-secondary">
        <strong>Last Rejection Reason:</strong> <?php echo htmlspecialchars($lastReason['reason']); ?><br>
        <small><em>By <?php echo htmlspecialchars($lastReason['rejected_by']); ?> on <?php echo htmlspecialchars($lastReason['rejected_at']); ?></em></small>
      </div>
    <?php endif; ?>

    <form method="POST" class="text-center mt-4">
      <div class="mb-3">
        <label for="rejection_reason" class="form-label fw-bold">Reason for Rejection (if rejecting):</label>
        <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" placeholder="Enter reason for rejection..."></textarea>
      </div>

      <button type="submit" name="approve" class="btn btn-success me-2">
        <i class="bi bi-check-circle"></i> Approve
      </button>
      <button type="submit" name="reject" class="btn btn-danger me-2">
        <i class="bi bi-x-circle"></i> Reject
      </button>
      <a href="review_student.php" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </form>

  <?php else: ?>
    <div class="alert alert-danger text-center">Student record not found!</div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
