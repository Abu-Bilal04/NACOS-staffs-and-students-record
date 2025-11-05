<?php
session_start();
include('../config/db_connect.php');

//  Ensure only logged-in students can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$message = "";

//  Fetch existing credentials if any
$existing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM student_credentials WHERE student_id = $student_id"));

//  Handle form submission
if (isset($_POST['upload'])) {
    $uploadDir = '../uploads/students/';
    $maxFileSize = 100 * 1024; // 100 KB limit

    $fields = [
        'primary_certificate',
        'batch_certificate',
        'olevel_certificate',
        'admission_letter',
        'recommendation_letter',
        'school_fees_payment',
        'consultancy_fee_payment',
        'tship_payment',
        'departmental_payment',
        'indigene_certificate' // 🆕 Added field
    ];

    $values = [];

    foreach ($fields as $field) {
        if (!empty($_FILES[$field]['name'])) {
            // ✅ Check file size
            if ($_FILES[$field]['size'] > $maxFileSize) {
                $message .= "<div class='alert alert-danger'>
                    The file for <strong>$field</strong> exceeds 100 KB. Please upload a smaller file.
                </div>";
                continue;
            }

            $fileName = time() . "_" . basename($_FILES[$field]['name']);
            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES[$field]['tmp_name'], $targetFile)) {
                $values[$field] = $fileName;
            } else {
                $message .= "<div class='alert alert-danger'>Error uploading <strong>$field</strong>.</div>";
            }
        }
    }

    //  If student already has a record
    if ($existing) {
        $updateFields = [];
        foreach ($values as $k => $v) {
            $updateFields[] = "$k='$v'";
        }
        if (!empty($updateFields)) {
            $sql = "UPDATE student_credentials SET " . implode(",", $updateFields) . " WHERE student_id=$student_id";
            if (mysqli_query($conn, $sql)) {
                $message .= "<div class='alert alert-success'>Credentials updated successfully!</div>";
            } else {
                $message .= "<div class='alert alert-danger'>Database error: " . mysqli_error($conn) . "</div>";
            }
        } else {
            $message .= "<div class='alert alert-warning'>No new valid file selected.</div>";
        }
    } else {
        //  Insert new record
        if (!empty($values)) {
            $columns = implode(",", array_keys($values));
            $vals = "'" . implode("','", array_values($values)) . "'";
            $sql = "INSERT INTO student_credentials (student_id, $columns) VALUES ($student_id, $vals)";
            if (mysqli_query($conn, $sql)) {
                $message .= "<div class='alert alert-success'>Credentials uploaded successfully!</div>";
            } else {
                $message .= "<div class='alert alert-danger'>Database error: " . mysqli_error($conn) . "</div>";
            }
        } else {
            $message .= "<div class='alert alert-warning'>No valid file uploaded (check size limits).</div>";
        }
    }

    // Refresh existing record
    $existing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM student_credentials WHERE student_id = $student_id"));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Upload Student Credentials</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #f4f7fb; font-family: 'Segoe UI', sans-serif; }
    .container { max-width: 800px; margin: 50px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .section-title { font-weight: 600; color: #003366; margin-bottom: 20px; }
  </style>
</head>
<body>
<div class="container">
  <h3 class="text-center text-primary mb-4"><i class="bi bi-upload"></i> Upload Student Credentials</h3>
  <?php echo $message; ?>

  <form method="POST" enctype="multipart/form-data">
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

    $emptyFound = false;

    foreach ($studentFields as $name => $label) {
        $existingFile = $existing[$name] ?? null;

        if (empty($existingFile)) {
            $emptyFound = true;
            echo "
            <div class='mb-3'>
              <label class='form-label'>$label</label>
              <input type='file' name='$name' class='form-control' accept='.pdf,.jpg,.jpeg,.png'>
              <small class='text-muted'>Max size: 100 KB</small>
            </div>";
        } else {
            echo "
            <div class='mb-3'>
              <label class='form-label text-success'>$label (Already Uploaded)</label><br>
              <a href='../uploads/students/$existingFile' target='_blank' class='btn btn-sm btn-outline-success'>
                <i class='bi bi-eye'></i> View
              </a>
            </div>";
        }
    }

    if (!$emptyFound) {
        echo "<div class='alert alert-info text-center'>All credentials have been uploaded</div>";
    }
    ?>

    <?php if ($emptyFound): ?>
    <div class="d-grid mt-4">
      <button type="submit" name="upload" class="btn btn-primary">
        <i class="bi bi-upload"></i> Upload
      </button>
    </div>
    <?php endif; ?>
  </form>

  <div class="text-center mt-4">
    <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
