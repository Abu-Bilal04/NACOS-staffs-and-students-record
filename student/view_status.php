<?php
session_start();
include('../config/db_connect.php');

// ✅ Ensure user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// ✅ Fetch student record
$query = "SELECT * FROM student_credentials WHERE student_id = $student_id";
$result = mysqli_query($conn, $query);
$credentials = mysqli_fetch_assoc($result);
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
    .section-title { font-weight: 600; color: #003366; margin-bottom: 20px; }
    .status-uploaded { color: green; font-weight: 600; }
    .status-missing { color: red; font-weight: 600; }
    .btn-view { font-size: 0.9rem; }
  </style>
</head>
<body>

<div class="container">
  <h3 class="text-center text-primary mb-4"><i class="bi bi-eye"></i> View Uploaded Credentials</h3>

  <?php if (!$credentials): ?>
    <div class="alert alert-warning text-center">
      <i class="bi bi-exclamation-triangle"></i> You have not uploaded any credentials yet.
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-primary text-center">
          <tr>
            <th>Credential Name</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $studentFields = [
            'indigene_certificate' => 'Indigene Certificate',
            'batch_certificate' => 'Birth Certificate',
            'primary_certificate' => 'Primary Certificate',
            'olevel_certificate' => 'O-Level Certificate',
            'admission_letter' => 'Admission Letter',
            'recommendation_letter' => 'Recommendation Letter',
            'school_fees_payment' => 'School Fees Payment',
            'consultancy_fee_payment' => 'Consultancy Fee Payment',
            'tship_payment' => 'T-Ship Payment',
            'departmental_payment' => 'Departmental Payment'
        ];

        foreach ($studentFields as $field => $label):
            $file = $credentials[$field] ?? null;
            if (!empty($file)) {
                echo "
                <tr>
                    <td>$label</td>
                    <td class='text-center status-uploaded'><i class='bi bi-check-circle-fill'></i> Uploaded</td>
                    <td class='text-center'>
                        <a href='../uploads/students/$file' target='_blank' class='btn btn-success btn-sm btn-view'>
                            <i class='bi bi-eye'></i> View
                        </a>
                        <a href='../uploads/students/$file' download class='btn btn-outline-primary btn-sm btn-view'>
                            <i class='bi bi-download'></i> Download
                        </a>
                    </td>
                </tr>";
            } else {
                echo "
                <tr>
                    <td>$label</td>
                    <td class='text-center status-missing'><i class='bi bi-x-circle-fill'></i> Not Uploaded</td>
                    <td class='text-center'>
                        <span class='text-muted'>No file</span>
                    </td>
                </tr>";
            }
        endforeach;
        ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <div class="text-center mt-4">
    <a href="dashboard.php" class="btn btn-secondary">
      <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
