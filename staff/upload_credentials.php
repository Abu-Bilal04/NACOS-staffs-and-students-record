<?php
session_start();
include('../config/db_connect.php');

// Ensure user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$message = "";

// ===================================
// Handle form submission
// ===================================
if (isset($_POST['upload'])) {
    $maxFileSize = 100 * 1024; // 100 KB limit

    if ($role == 'student') {
        $uploadDir = '../uploads/students/';
        $table = 'student_credentials';
        $id_field = 'student_id';
        $fields = [
            'primary_certificate',
            'batch_certificate',
            'olevel_certificate',
            'admission_letter',
            'recommendation_letter',
            'school_fees_payment',
            'consultancy_fee_payment',
            'tship_payment',
            'departmental_payment'
        ];
    } else {
        $uploadDir = '../uploads/staff/';
        $table = 'staff_credentials';
        $id_field = 'staff_id';
        $fields = [
            'appointment_letter',
            'promotion_letter1',
            'promotion_letter2',
            'promotion_letter3',
            'promotion_letter4',
            'promotion_letter5',
            'first_degree',
            'second_degree',
            'third_degree',
            'olevel_certificate',
            'indigent_certificate',
            'birth_certificate',
            'confirmation_letter',
            'regularization_letter',
            'other_relevant_documents'
        ];
    }

    $values = [];
    foreach ($fields as $field) {
        if (!empty($_FILES[$field]['name'])) {
            // Check file size
            if ($_FILES[$field]['size'] > $maxFileSize) {
                $message .= "<div class='alert alert-danger'>
                    File too large for <strong>$field</strong>. Maximum size is 100 KB.
                </div>";
                continue;
            }

            // Secure upload
            $fileName = time() . "_" . basename($_FILES[$field]['name']);
            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES[$field]['tmp_name'], $targetFile)) {
                $values[$field] = $fileName;
            } else {
                $message .= "<div class='alert alert-danger'>Error uploading <strong>$field</strong>.</div>";
            }
        }
    }

    // Check if record exists
    $check = mysqli_query($conn, "SELECT * FROM $table WHERE $id_field=$user_id");
    if (mysqli_num_rows($check) > 0) {
        // Update existing record
        $updateFields = [];
        foreach ($values as $k => $v) {
            $updateFields[] = "$k='$v'";
        }
        if (!empty($updateFields)) {
            $sql = "UPDATE $table SET " . implode(",", $updateFields) . " WHERE $id_field=$user_id";
            mysqli_query($conn, $sql);
            $message .= "<div class='alert alert-success'>Credentials updated successfully!</div>";
        }
    } else {
        // Insert new record
        if (!empty($values)) {
            $columns = implode(",", array_keys($values));
            $vals = "'" . implode("','", array_values($values)) . "'";
            $sql = "INSERT INTO $table ($id_field, $columns) VALUES ($user_id, $vals)";
            if (mysqli_query($conn, $sql)) {
                $message .= "<div class='alert alert-success'>Credentials uploaded successfully!</div>";
            } else {
                $message .= "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
            }
        }
    }
}

// ===================================
// Fetch existing credentials
// ===================================
if ($role == 'student') {
    $table = 'student_credentials';
    $id_field = 'student_id';
} else {
    $table = 'staff_credentials';
    $id_field = 'staff_id';
}

$existing = mysqli_query($conn, "SELECT * FROM $table WHERE $id_field=$user_id");
$uploaded = mysqli_fetch_assoc($existing) ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Upload Credentials</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #f4f7fb; font-family: 'Segoe UI', sans-serif; }
    .container { max-width: 900px; margin: 50px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .section-title { font-weight: 600; color: #003366; margin-top: 20px; margin-bottom: 10px; }
    .badge-uploaded { background-color: #d4edda; color: #155724; padding: 6px 10px; border-radius: 6px; font-size: 0.85rem; }
  </style>
</head>
<body>
<div class="container">
  <h3 class="text-center text-primary mb-4"><i class="bi bi-upload"></i> Upload Credentials</h3>

  <?php echo $message; ?>

  <form method="POST" enctype="multipart/form-data">
    <?php if ($role == 'student'): ?>
      <div class="section-title">Student Credentials</div>
      <?php
      $studentFields = [
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
      foreach ($studentFields as $name => $label) {
          if (!empty($uploaded[$name])) {
              echo "<div class='mb-3 d-flex justify-content-between align-items-center'>
                        <div><strong>$label</strong></div>
                        <span class='badge-uploaded'><i class='bi bi-check-circle'></i> Uploaded</span>
                    </div>";
          } else {
              echo "<div class='mb-3'><label class='form-label'>$label</label>
                    <input type='file' name='$name' class='form-control' accept='.pdf,.jpg,.jpeg,.png'></div>";
          }
      }
      ?>

    <?php elseif ($role == 'staff'): ?>
      <div class="section-title">Staff Credentials</div>
      <?php
      $staffFields = [
          'appointment_letter' => 'Appointment Letter',
          'promotion_letter1' => 'First Promotion',
          'promotion_letter2' => 'Second Promotion',
          'promotion_letter3' => 'Third Promotion',
          'promotion_letter4' => 'Fourth Promotion',
          'promotion_letter5' => 'Fifth Promotion',
          'first_degree' => 'First Degree',
          'second_degree' => 'Second Degree',
          'third_degree' => 'Third Degree',
          'olevel_certificate' => 'O-Level Certificate',
          'indigent_certificate' => 'Indigent Certificate',
          'birth_certificate' => 'Birth Certificate',
          'confirmation_letter' => 'Confirmation Letter',
          'regularization_letter' => 'Regularization Letter',
          'other_relevant_documents' => 'Other Relevant Documents'
      ];
      foreach ($staffFields as $name => $label) {
          if (!empty($uploaded[$name])) {
              echo "<div class='mb-3 d-flex justify-content-between align-items-center'>
                        <div><strong>$label</strong></div>
                        <span class='badge-uploaded'><i class='bi bi-check-circle'></i> Uploaded</span>
                    </div>";
          } else {
              echo "<div class='mb-3'><label class='form-label'>$label</label>
                    <input type='file' name='$name' class='form-control' accept='.pdf,.jpg,.jpeg,.png'></div>";
          }
      }
      ?>
    <?php endif; ?>

    <div class="d-grid mt-4">
      <button type="submit" name="upload" class="btn btn-primary">
        <i class="bi bi-upload"></i> Upload
      </button>
    </div>
  </form>

  <div class="text-center mt-3">
    <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
