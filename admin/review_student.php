<?php
session_start();
include('../config/db_connect.php');

// Only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

//  Fetch all students (fixed table name)
$query = mysqli_query($conn, "SELECT * FROM student ORDER BY created_at DESC");

//  Handle query errors gracefully
if (!$query) {
    die("<div class='alert alert-danger text-center'>
        <strong>Database Error:</strong> " . mysqli_error($conn) . "
    </div>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Review Students - Admin</title>

  <!--  Bootstrap CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background-color: #f4f7fb;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .container {
      max-width: 1000px;
      margin-top: 50px;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }

    .table th {
      background-color: #003366;
      color: white;
    }

    .status-badge {
      padding: 6px 10px;
      border-radius: 8px;
      font-size: 0.85rem;
      font-weight: 500;
    }

    .status-pending {
      background-color: #fff3cd;
      color: #856404;
    }

    .status-approved {
      background-color: #d4edda;
      color: #155724;
    }

    .status-rejected {
      background-color: #f8d7da;
      color: #721c24;
    }

    .btn-view {
      background-color: #003366;
      color: white;
    }

    .btn-view:hover {
      background-color: #002244;
    }
  </style>
</head>
<body>

<div class="container">
  <h3 class="text-center text-primary mb-4"><i class="bi bi-people"></i> Review Registered Students</h3>

  <table class="table table-bordered table-striped table-hover align-middle">
    <thead>
      <tr class="text-center">
        <th>#</th>
        <th>Name</th>
        <th>Reg Number</th>
        <th>Level</th>
        <th>Phone</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if (mysqli_num_rows($query) > 0) {
          $sn = 1;
          while ($row = mysqli_fetch_assoc($query)) {
              $statusClass = "";
              switch ($row['approval_status']) {
                  case 'Approved': $statusClass = "status-approved"; break;
                  case 'Rejected': $statusClass = "status-rejected"; break;
                  default: $statusClass = "status-pending"; break;
              }
              echo "
              <tr>
                <td class='text-center'>{$sn}</td>
                <td>" . htmlspecialchars($row['name']) . "</td>
                <td>" . htmlspecialchars($row['reg_number']) . "</td>
                <td class='text-center'>" . htmlspecialchars($row['level']) . "</td>
                <td>" . htmlspecialchars($row['phone']) . "</td>
                <td class='text-center'><span class='status-badge $statusClass'>" . htmlspecialchars($row['approval_status']) . "</span></td>
                <td class='text-center'>
                  <a href='view_student.php?id={$row['student_id']}' class='btn btn-view btn-sm'>
                    <i class='bi bi-eye'></i> View
                  </a>
                </td>
              </tr>";
              $sn++;
          }
      } else {
          echo "<tr><td colspan='7' class='text-center text-muted'>No students found.</td></tr>";
      }
      ?>
    </tbody>
  </table>

  <div class="text-center mt-3">
    <a href="dashboard.php" class="btn btn-secondary">
      <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
