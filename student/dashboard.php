<?php
session_start();
include('../config/db_connect.php');

// ✅ Ensure user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

$username = $_SESSION['username'];
$student_id = $_SESSION['user_id'];

// ✅ Fetch student's approval status
$query = "SELECT approval_status FROM student WHERE student_id = '$student_id'";
$result = mysqli_query($conn, $query);
$student = mysqli_fetch_assoc($result);
$status = $student['approval_status'] ?? 'Pending';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard</title>

  <!-- ✅ Bootstrap 5 & Icons -->
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
      max-width: 900px;
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

    .status-card {
      text-align: center;
      padding: 20px;
      border-radius: 12px;
    }

    .status-badge {
      font-size: 1.2rem;
      padding: 10px 20px;
      border-radius: 30px;
    }

    .section-title {
      color: #003366;
      font-weight: bold;
      margin-top: 20px;
      margin-bottom: 15px;
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
      <a class="navbar-brand" href="#">Student Dashboard</a>
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
      <h3 class="text-primary"><i class="bi bi-speedometer2"></i> Student Portal</h3>
      <p class="text-muted">Manage your uploads and check your approval status</p>
    </div>

    <!-- ✅ Student Status -->
    <div class="status-card bg-light">
      <h5 class="section-title"><i class="bi bi-person-check"></i> Approval Status</h5>
      <?php
        $badgeClass = match ($status) {
            'Approved' => 'bg-success',
            'Rejected' => 'bg-danger',
            default => 'bg-warning text-dark',
        };
      ?>
      <p class="mt-3">
        <span class="badge status-badge <?php echo $badgeClass; ?>">
          <?php echo htmlspecialchars($status); ?>
        </span>
      </p>
    </div>

    <!-- ✅ Actions -->
    <h5 class="section-title mt-5"><i class="bi bi-grid"></i> Quick Actions</h5>
    <div class="row g-4 justify-content-center">
      <div class="col-md-5">
        <div class="card text-center bg-light">
          <div class="card-body">
            <i class="bi bi-upload display-4 text-primary"></i>
            <h5 class="mt-3">Upload Credentials</h5>
            <a href="upload_credentials.php" class="btn btn-primary btn-sm mt-2">Upload</a>
          </div>
        </div>
      </div>

      <div class="col-md-5">
        <div class="card text-center bg-light">
          <div class="card-body">
            <i class="bi bi-eye display-4 text-success"></i>
            <h5 class="mt-3">View Upload Status</h5>
            <a href="view_status.php" class="btn btn-success btn-sm mt-2">View</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
