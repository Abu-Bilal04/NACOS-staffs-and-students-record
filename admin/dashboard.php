<?php
session_start();
include('../config/db_connect.php');

//  Check login
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../index.php");
    exit();
}

$username = $_SESSION['username'];
$role = ucfirst($_SESSION['role']); // Capitalize role name

//  Fetch stats (only for admin)
$total_students = $pending_students = $approved_students = $rejected_students = 0;
$total_staff = $pending_staff = $approved_staff = $rejected_staff = 0;

if ($role === "Admin") {
    // Students
    $total_students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM student"))['total'];
    $pending_students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM student WHERE approval_status='Pending'"))['total'];
    $approved_students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM student WHERE approval_status='Approved'"))['total'];
    $rejected_students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM student WHERE approval_status='Rejected'"))['total'];

    // Staff
    $total_staff = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM staff"))['total'];
    $pending_staff = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM staff WHERE approval_status='Pending'"))['total'];
    $approved_staff = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM staff WHERE approval_status='Approved'"))['total'];
    $rejected_staff = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM staff WHERE approval_status='Rejected'"))['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $role; ?> Dashboard</title>

  <!--  Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background-color: #f4f7fb;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    header {
      background-color: #003366;
      color: white;
      padding: 15px 0;
      text-align: center;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
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
      font-size: 1rem;
    }

    .dashboard-content {
      max-width: 1200px;
      margin: 40px auto;
      background: white;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    footer {
      background-color: #003366;
      color: white;
      text-align: center;
      padding: 10px;
      position: fixed;
      bottom: 0;
      width: 100%;
    }

    .card {
      border: none;
      border-radius: 12px;
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    .stat-card h5 {
      font-size: 1.1rem;
      font-weight: 600;
    }

    .stat-number {
      font-size: 1.6rem;
      font-weight: bold;
    }

    .section-title {
      margin-top: 40px;
      margin-bottom: 20px;
      color: #003366;
      font-weight: bold;
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
      <a class="navbar-brand" href="#"><?php echo $role; ?> Dashboard</a>
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

  <!-- Dashboard Body -->
  <div class="dashboard-content">

    <div class="text-center mb-4">
      <h3 class="text-primary"><i class="bi bi-speedometer2"></i> <?php echo $role; ?> Portal</h3>
      <p class="text-muted">Choose an action below or review system statistics</p>
    </div>

    <?php if ($role == "Admin") { ?>
      <!--  Admin Stats Section -->
      <h5 class="section-title"><i class="bi bi-bar-chart"></i> System Overview</h5>
      <div class="row g-4">
        <!-- Students -->
        <div class="col-md-3">
          <div class="card text-center stat-card bg-light border-primary">
            <div class="card-body">
              <i class="bi bi-mortarboard text-primary display-6"></i>
              <h5>Students</h5>
              <p class="stat-number text-primary"><?php echo $total_students; ?></p>
              <p class="mb-1"><span class="text-warning">Pending:</span> <?php echo $pending_students; ?></p>
              <p class="mb-1"><span class="text-success">Approved:</span> <?php echo $approved_students; ?></p>
              <p class="mb-0"><span class="text-danger">Rejected:</span> <?php echo $rejected_students; ?></p>
            </div>
          </div>
        </div>

        <!-- Staff -->
        <div class="col-md-3">
          <div class="card text-center stat-card bg-light border-success">
            <div class="card-body">
              <i class="bi bi-person-badge text-success display-6"></i>
              <h5>Staff</h5>
              <p class="stat-number text-success"><?php echo $total_staff; ?></p>
              <p class="mb-1"><span class="text-warning">Pending:</span> <?php echo $pending_staff; ?></p>
              <p class="mb-1"><span class="text-success">Approved:</span> <?php echo $approved_staff; ?></p>
              <p class="mb-0"><span class="text-danger">Rejected:</span> <?php echo $rejected_staff; ?></p>
            </div>
          </div>
        </div>
      </div>

      <!--  Action Cards -->
      <h5 class="section-title mt-5"><i class="bi bi-grid"></i> Admin Actions</h5>
      <div class="row g-4">
        <div class="col-md-3">
          <div class="card text-center bg-light">
            <div class="card-body">
              <i class="bi bi-person-plus display-4 text-primary"></i>
              <h5 class="mt-3">Register Student</h5>
              <a href="register_student.php" class="btn btn-primary btn-sm mt-2">Open</a>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card text-center bg-light">
            <div class="card-body">
              <i class="bi bi-person-badge display-4 text-success"></i>
              <h5 class="mt-3">Register Staff</h5>
              <a href="register_staff.php" class="btn btn-success btn-sm mt-2">Open</a>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card text-center bg-light">
            <div class="card-body">
              <i class="bi bi-journal-check display-4 text-secondary"></i>
              <h5 class="mt-3">Review Students</h5>
              <a href="review_student.php" class="btn btn-secondary btn-sm mt-2">Open</a>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card text-center bg-light">
            <div class="card-body">
              <i class="bi bi-journal-text display-4 text-warning"></i>
              <h5 class="mt-3">Review Staff</h5>
              <a href="review_staff.php" class="btn btn-warning btn-sm mt-2">Open</a>
            </div>
          </div>
        </div>
      </div>

    <?php } else { ?>
      <!--  Staff / Student Dashboard -->
      <div class="row g-4 justify-content-center">
        <div class="col-md-4">
          <div class="card text-center bg-light">
            <div class="card-body">
              <i class="bi bi-upload display-4 text-primary"></i>
              <h5 class="mt-3">Upload Credentials</h5>
              <a href="upload_credentials.php" class="btn btn-primary btn-sm mt-2">Upload</a>
            </div>
          </div>
        </div>

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
    <?php } ?>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
