<?php
session_start();
include('../config/db_connect.php');

// Ensure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch all staff
$result = mysqli_query($conn, "SELECT * FROM staff ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Review Staff - Admin</title>

  <!-- ✅ Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background-color: #f4f7fb;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .container {
      max-width: 1000px;
      margin-top: 60px;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .table thead {
      background-color: #003366;
      color: white;
    }

    .btn-view {
      background-color: #0d6efd;
      color: white;
    }

    .btn-view:hover {
      background-color: #0b5ed7;
    }

    .back-link {
      text-decoration: none;
      color: #003366;
    }

    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="text-center mb-4">
    <h3 class="text-primary"><i class="bi bi-person-lines-fill"></i> Review Registered Staff</h3>
    <p class="text-muted">Click “View” to review staff details and credentials</p>
  </div>

  <div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Staff Number</th>
          <th>Phone</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
          <?php $i = 1; while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td><?php echo $i++; ?></td>
              <td><?php echo htmlspecialchars($row['name']); ?></td>
              <td><?php echo htmlspecialchars($row['staff_number']); ?></td>
              <td><?php echo htmlspecialchars($row['phone']); ?></td>
              <td>
                <?php if ($row['approval_status'] == 'Approved'): ?>
                  <span class="badge bg-success">Approved</span>
                <?php elseif ($row['approval_status'] == 'Rejected'): ?>
                  <span class="badge bg-danger">Rejected</span>
                <?php else: ?>
                  <span class="badge bg-warning text-dark">Pending</span>
                <?php endif; ?>
              </td>
              <td>
                <a href="view_staff.php?id=<?php echo $row['staff_id']; ?>" class="btn btn-view btn-sm">
                  <i class="bi bi-eye"></i> View
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="text-center text-muted">No staff records found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="text-center mt-3">
    <a href="dashboard.php" class="back-link"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
