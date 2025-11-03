<?php
session_start();
include('../config/db_connect.php');

// Ensure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$message = "";

// Handle form submission
if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $reg_number = mysqli_real_escape_string($conn, $_POST['reg_number']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $level = mysqli_real_escape_string($conn, $_POST['level']);
    $password = password_hash($phone, PASSWORD_DEFAULT);

    // Check if student already exists (by reg_number OR phone)
    $check = mysqli_query($conn, "SELECT * FROM student WHERE reg_number='$reg_number' OR phone='$phone'");
    if (mysqli_num_rows($check) > 0) {
        $message = "<div class='alert alert-danger text-center'><i class='bi bi-x-circle'></i> Student already exists with this Reg Number or Phone!</div>";
    } else {
        $sql = "INSERT INTO student (name, reg_number, address, phone, level, password) 
                VALUES ('$name', '$reg_number', '$address', '$phone', '$level', '$password')";
        if (mysqli_query($conn, $sql)) {
            $message = "<div class='alert alert-success text-center'><i class='bi bi-check-circle'></i> Student registered successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger text-center'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Student - Admin</title>

  <!-- ✅ Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background-color: #f4f7fb;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .container {
      max-width: 650px;
      margin-top: 60px;
      background: white;
      padding: 35px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .btn-primary {
      background-color: #003366;
      border-color: #003366;
    }

    .btn-primary:hover {
      background-color: #002244;
    }

    .back-link {
      text-decoration: none;
      color: #003366;
    }

    .back-link:hover {
      text-decoration: underline;
    }

    .form-label {
      font-weight: 500;
    }
  </style>
</head>
<body>

  <div class="container">
    <div class="text-center mb-4">
      <h3 class="text-primary"><i class="bi bi-person-plus"></i> Register New Student</h3>
      <p class="text-muted">Fill in the student details below</p>
    </div>

    <?php echo $message; ?>

    <form method="POST" action="">
      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" placeholder="Enter full name" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Reg Number</label>
        <input type="text" name="reg_number" class="form-control" placeholder="Enter registration number" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Address</label>
        <input type="text" name="address" class="form-control" placeholder="Enter address" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Phone Number</label>
        <input type="text" name="phone" class="form-control" placeholder="Enter phone number" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Level</label>
        <select name="level" class="form-select" required>
          <option value="">-- Select Level --</option>
          <option value="ND1">ND1</option>
          <option value="ND2">ND2</option>
          <option value="HND1">HND1</option>
          <option value="HND2">HND2</option>
        </select>
      </div>


      <div class="d-grid">
        <button type="submit" name="register" class="btn btn-primary">
          <i class="bi bi-save"></i> Register Student
        </button>
      </div>

      <div class="text-center mt-3">
        <a href="dashboard.php" class="back-link"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
      </div>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
