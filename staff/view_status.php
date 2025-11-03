<?php
session_start();
include('../config/db_connect.php');

// ✅ Ensure user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$message = "";

// Fetch credentials based on role
if ($role == 'student') {
    $table = 'student_credentials';
    $dir = '../uploads/students/';
    $fields = [
        'primary_certificate' => 'Primary Certificate',
        'batch_certificate' => 'Batch Certificate',
        'olevel_certificate' => 'O-Level Certificate',
        'admission_letter' => 'Admission Letter',
        'recommendation_letter' => 'Recommendation Letter',
        'school_fees_payment' => 'School Fees Payment',
        'consultancy_fee_payment' => 'Consultancy Fee Payment',
        'tship_payment' => 'T-Ship Payment',
        'departmental_payment' => 'Departmental Payment'
    ];
} elseif ($role == 'staff') {
    $table = 'staff_credentials';
    $dir = '../uploads/staff/';
    $fields = [
        'appointment_letter' => 'Appointment Letter',
        'promotion_letter1' => 'Promotion Letter 1',
        'promotion_letter2' => 'Promotion Letter 2',
        'promotion_letter3' => 'Promotion Letter 3',
        'promotion_letter4' => 'Promotion Letter 4',
        'promotion_letter5' => 'Promotion Letter 5',
        'first_degree' => 'First Degree',
        'second_degree' => 'Second Degree',
        'third_degree' => 'Third Degree',
        'olevel_certificate' => 'O-Level Certificate',
        'indigent_certificate' => 'Indigent Certificate',
        'birth_certificate' => 'Birth Certificate'
    ];
} else {
    die("Invalid role.");
}

// Fetch user credentials
$query = mysqli_query($conn, "SELECT * FROM $table WHERE " . ($role=='student'?'student_id':'staff_id') . "=$user_id");
$credentials = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Upload Status</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #f4f7fb; font-family: 'Segoe UI', sans-serif; }
    .container { max-width: 900px; margin: 50px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .status-badge { padding: 5px 10px; border-radius: 8px; font-weight: 500; font-size: 0.9rem; }
    .uploaded { background-color: #d4edda; color: #155724; }
    .missing { background-color: #f8d7da; color: #721c24; }
  </style>
</head>
<body>

<div class="container">
  <h3 class="text-center text-primary mb-4"><i class="bi bi-eye"></i> Uploaded Credentials Status</h3>

  <?php if ($credentials): ?>
    <table class="table table-bordered table-hover">
      <thead>
        <tr class="text-center bg-primary text-white">
          <th>#</th>
          <th>Document</th>
          <th>Status</th>
          <th>File</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sn = 1;
        foreach ($fields as $key => $label) {
            $file = $credentials[$key] ?? '';
            $statusClass = !empty($file) ? 'uploaded' : 'missing';
            $statusText = !empty($file) ? 'Uploaded' : 'Missing';
            echo "<tr class='text-center'>
                    <td>{$sn}</td>
                    <td>{$label}</td>
                    <td><span class='status-badge {$statusClass}'>{$statusText}</span></td>
                    <td>";
            if (!empty($file)) {
                echo "<a href='{$dir}{$file}' target='_blank' class='btn btn-sm btn-info'><i class='bi bi-eye'></i> View</a>";
            } else {
                echo "-";
            }
            echo "</td></tr>";
            $sn++;
        }
        ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="alert alert-warning text-center">No credentials uploaded yet.</div>
  <?php endif; ?>

  <div class="text-center mt-4">
    <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
