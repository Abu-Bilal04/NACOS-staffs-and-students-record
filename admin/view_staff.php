<?php
session_start();
include('../config/db_connect.php');

// ✅ Only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// ✅ Ensure staff ID is provided
if (!isset($_GET['id'])) {
    header("Location: review_staff.php");
    exit();
}

$staff_id = intval($_GET['id']);
$message = "";

// ✅ Fetch staff details
$staff_query = mysqli_query($conn, "SELECT * FROM staff WHERE staff_id = $staff_id");
$staff = mysqli_fetch_assoc($staff_query);

if (!$staff) {
    die("<div class='text-center mt-5 text-danger'>Staff not found!</div>");
}

// ✅ Fetch staff credentials
$cred_query = mysqli_query($conn, "SELECT * FROM staff_credentials WHERE staff_id = $staff_id");
$credentials = mysqli_fetch_assoc($cred_query);

// ✅ Handle Approve / Reject actions
if (isset($_POST['action'])) {
    $status = $_POST['action'] == 'approve' ? 'Approved' : 'Rejected';
    $update = mysqli_query($conn, "UPDATE staff SET approval_status='$status' WHERE staff_id=$staff_id");

    if ($update) {
        $message = "<div class='alert alert-success text-center'>Staff status updated to <strong>$status</strong>.</div>";
        $staff['approval_status'] = $status;
    } else {
        $message = "<div class='alert alert-danger text-center'>Error updating status: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Staff Details</title>

  <!-- ✅ Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background-color: #f4f7fb;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .container {
      max-width: 900px;
      margin-top: 50px;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }

    .card {
      border-radius: 10px;
      margin-bottom: 20px;
    }

    .btn-approve {
      background-color: #198754;
      color: white;
    }

    .btn-reject {
      background-color: #dc3545;
      color: white;
    }

    .badge {
      font-size: 0.9em;
    }
  </style>
</head>
<body>

<div class="container">
  <h3 class="text-center text-primary mb-3"><i class="bi bi-person-lines-fill"></i> Staff Details</h3>

  <?php echo $message; ?>

  <!-- Staff Information -->
  <div class="card">
    <div class="card-header bg-primary text-white">
      <strong>Staff Information</strong>
    </div>
    <div class="card-body">
      <p><strong>Name:</strong> <?php echo htmlspecialchars($staff['name']); ?></p>
      <p><strong>Staff Number:</strong> <?php echo htmlspecialchars($staff['staff_number']); ?></p>
      <p><strong>Address:</strong> <?php echo htmlspecialchars($staff['address']); ?></p>
      <p><strong>Phone:</strong> <?php echo htmlspecialchars($staff['phone']); ?></p>
      <p><strong>Status:</strong>
        <?php if ($staff['approval_status'] == 'Approved'): ?>
          <span class="badge bg-success">Approved</span>
        <?php elseif ($staff['approval_status'] == 'Rejected'): ?>
          <span class="badge bg-danger">Rejected</span>
        <?php else: ?>
          <span class="badge bg-warning text-dark">Pending</span>
        <?php endif; ?>
      </p>
    </div>
  </div>

  <!-- ✅ Credentials Section -->
  <div class="card">
    <div class="card-header bg-secondary text-white">
      <strong>Uploaded Credentials</strong>
    </div>
    <div class="card-body">
      <?php if ($credentials): ?>
        <ul class="list-group">
          <?php
          $docs = [
            'appointment_letter' => 'Appointment Letter',
            'promotion_letter1' => 'Promotion Letter 1',
            'promotion_letter2' => 'Promotion Letter 2',
            'promotion_letter3' => 'Promotion Letter 3',
            'promotion_letter4' => 'Promotion Letter 4',
            'promotion_letter5' => 'Promotion Letter 5',
            'first_degree' => 'First Degree Certificate',
            'second_degree' => 'Second Degree Certificate',
            'third_degree' => 'Third Degree Certificate',
            'olevel_certificate' => 'O-Level Certificate',
            'indigent_certificate' => 'Indigent Certificate',
            'birth_certificate' => 'Birth Certificate'
          ];

          foreach ($docs as $field => $label): ?>
            <li class="list-group-item">
              <strong><?php echo $label; ?>:</strong>
              <?php if (!empty($credentials[$field])): ?>
                <a href="../uploads/staff/<?php echo htmlspecialchars($credentials[$field]); ?>" target="_blank" class="text-decoration-none">View Document</a>
              <?php else: ?>
                <span class="text-muted">Not uploaded</span>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="text-muted">No credentials uploaded for this staff.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Approve / Reject Buttons -->
  <form method="POST" class="text-center mt-3">
    <button type="submit" name="action" value="approve" class="btn btn-approve me-2">
      <i class="bi bi-check-circle"></i> Approve
    </button>
    <button type="submit" name="action" value="reject" class="btn btn-reject">
      <i class="bi bi-x-circle"></i> Reject
    </button>
    <div class="mt-3">
      <a href="review_staff.php" class="btn btn-link"><i class="bi bi-arrow-left"></i> Back to List</a>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
