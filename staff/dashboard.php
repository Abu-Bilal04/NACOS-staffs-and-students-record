<?php
session_start();
include('../config/db_connect.php');

// Redirect if not logged in or not staff
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'staff') {
    header("Location: ../index.php");
    exit();
}

$staff_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch staff details
$query = "SELECT * FROM staff WHERE staff_id = '$staff_id'";
$result = mysqli_query($conn, $query);
$staff = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Dashboard</title>

  <!-- ✅ Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background-color: #f4f6f9;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    header {
      background-color: #003366;
      color: white;
      padding: 20px 0;
      text-align: center;
      box-shadow: 0 2px 5px rgba(0,0,0,0.3);
    }

    .logo-container {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 40px;
      flex-wrap: wrap;
    }

    .logo {
      width: 80px;
      height: 80px;
      object-fit: contain;
    }

    .system-title h1 {
      font-size: 1.6rem;
      margin: 0;
    }

    .system-title p {
      margin: 0;
      color: #cce0ff;
    }

    .dashboard-content {
      max-width: 1100px;
      margin: 40px auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }

    .card {
      border: none;
      border-radius: 12px;
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }

    footer {
      background-color: #003366;
      color: white;
      text-align: center;
      padding: 10px 0;
      margin-top: 50px;
    }
  </style>
</head>
<body>

  <!-- Header -->
  <header>
    <div class="logo-container">
      <img src="../images/nilest.webp" alt="Institution Logo" class="logo">
      <div class="system-title text-center">
        <h1>Staff and Student Record Management System</h1>
        <p>Department of Computer Science</p>
      </div>
      <img src="../images/nacos.webp" alt="Department Logo" class="logo">
    </div>
  </header>
  
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand" href="#">Staff Dashboard</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <span class="nav-link text-light">Welcome, <strong><?php echo htmlspecialchars($username); ?></strong></span>
          </li>
          <li class="nav-item">
            <a href="../logout.php" class="btn btn-light btn-sm ms-2">
              <i class="bi bi-box-arrow-right"></i> Logout
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Dashboard Content -->
  <div class="dashboard-content">
    <div class="text-center mb-4">
      <h3 class="text-primary"><i class="bi bi-person-badge"></i> Staff Dashboard</h3>
      <p class="text-muted">Manage your credentials and view your upload status.</p>
    </div>

    <div class="row g-4 justify-content-center">
      <!-- Upload Credentials -->
      <div class="col-md-4">
        <div class="card text-center bg-light">
          <div class="card-body">
            <i class="bi bi-upload display-4 text-primary"></i>
            <h5 class="mt-3">Upload Credentials</h5>
            <a href="upload_credentials.php" class="btn btn-primary btn-sm mt-2">Upload</a>
          </div>
        </div>
      </div>

      <!-- View Upload Status -->
      <div class="col-md-4">
        <div class="card text-center bg-light">
          <div class="card-body">
            <i class="bi bi-eye display-4 text-success"></i>
            <h5 class="mt-3">View Upload Status</h5>
            <a href="view_status.php" class="btn btn-success btn-sm mt-2">View</a>
          </div>
        </div>
      </div>

     
    </div>

    <hr class="my-4">

    <div class="mt-4">
      <h5>Staff Information</h5>
      <table class="table table-bordered table-striped mt-3">
        <tr><th>Name</th><td><?php echo htmlspecialchars($staff['name']); ?></td></tr>
        <tr><th>Staff Number</th><td><?php echo htmlspecialchars($staff['staff_number']); ?></td></tr>
        <tr><th>Address</th><td><?php echo htmlspecialchars($staff['address']); ?></td></tr>
        <tr><th>Phone</th><td><?php echo htmlspecialchars($staff['phone']); ?></td></tr>
        <tr><th>Status</th>
            <td>
              <?php
                $status = $staff['approval_status'];
                $badge = ($status == 'Approved') ? 'success' : (($status == 'Rejected') ? 'danger' : 'secondary');
                echo "<span class='badge bg-$badge'>$status</span>";
              ?>
            </td>
        </tr>
      </table>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
