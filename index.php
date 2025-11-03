<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff and Student Record Management System</title>

  <!-- ✅ Bootstrap 5 CDN -->
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
      padding: 20px 0;
      text-align: center;
      box-shadow: 0 3px 6px rgba(0,0,0,0.3);
    }

    .logo-container {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 40px;
      flex-wrap: wrap;
    }

    .logo {
      width: 90px;
      height: 90px;
      object-fit: contain;
      border-radius: 10px;
    }

    .system-title h1 {
      font-size: 1.8rem;
      margin: 0;
    }

    .system-title p {
      margin: 0;
      font-size: 1rem;
      color: #cce0ff;
    }

    .login-card {
      max-width: 420px;
      margin: 70px auto;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
      background-color: #fff;
    }

    .login-card .form-control:focus,
    .login-card .form-select:focus {
      border-color: #003366;
      box-shadow: 0 0 0 0.2rem rgba(0,51,102,0.25);
    }

    .btn-primary {
      background-color: #003366;
      border: none;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #002244;
      transform: translateY(-2px);
    }

    footer {
      background-color: #003366;
      color: white;
      text-align: center;
      padding: 10px 0;
      position: fixed;
      bottom: 0;
      width: 100%;
      font-size: 0.9rem;
    }
  </style>
</head>

<body>
  <!-- Header -->
  <header>
    <div class="logo-container">
      <img src="images/nilest.webp" alt="Institution Logo" class="logo">
      <div class="system-title text-center">
        <h1>Staff and Student Record Management System</h1>
        <p>Department of Computer Science</p>
      </div>
      <img src="images/nacos.webp" alt="Department Logo" class="logo">
    </div>
  </header>

  <!-- Login Card -->
  <div class="card login-card p-4">
    <h4 class="text-center text-primary mb-3">
      <i class="bi bi-lock-fill"></i> Login to Continue
    </h4>

    <?php if (isset($_SESSION['login_error'])): ?>
      <div class="alert alert-danger text-center py-2">
        <?= htmlspecialchars($_SESSION['login_error']); unset($_SESSION['login_error']); ?>
      </div>
    <?php endif; ?>

    <form action="login.php" method="POST">
      <!-- Select Role -->
      <div class="mb-3">
        <label class="form-label"><strong>Select Role</strong></label>
        <select name="role" id="role" class="form-select" required>
          <option value="">-- Select Role --</option>
          <option value="admin">Admin</option>
          <option value="staff">Staff</option>
          <option value="student">Student</option>
        </select>
      </div>

      <!-- Username/ID/Reg Number -->
      <div class="mb-3" id="usernameDiv" style="display: none;">
        <label class="form-label" id="usernameLabel"><strong>Username</strong></label>
        <input type="text" name="username" id="usernameInput" class="form-control" placeholder="Enter username">
      </div>

      <!-- Password -->
      <div class="mb-3" id="passwordDiv" style="display: none;">
        <label class="form-label"><strong>Password</strong></label>
        <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Enter password">
      </div>

      <div class="d-grid">
        <button type="submit" name="login" class="btn btn-primary">
          <i class="bi bi-box-arrow-in-right"></i> Login
        </button>
      </div>
    </form>
  </div>

  <footer>
    <p>&copy; <?= date("Y"); ?> Department of Computer Science | All Rights Reserved.</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const roleSelect = document.getElementById('role');
    const usernameDiv = document.getElementById('usernameDiv');
    const passwordDiv = document.getElementById('passwordDiv');
    const usernameLabel = document.getElementById('usernameLabel');
    const usernameInput = document.getElementById('usernameInput');

    roleSelect.addEventListener('change', () => {
      const role = roleSelect.value;

      if (role) {
        usernameDiv.style.display = 'block';
        passwordDiv.style.display = 'block';

        if (role === 'admin') {
          usernameLabel.textContent = 'Username';
          usernameInput.placeholder = 'Enter username';
        } else if (role === 'staff') {
          usernameLabel.textContent = 'Staff ID';
          usernameInput.placeholder = 'Enter staff ID';
        } else if (role === 'student') {
          usernameLabel.textContent = 'Reg Number';
          usernameInput.placeholder = 'Enter Reg Number';
        }
      } else {
        usernameDiv.style.display = 'none';
        passwordDiv.style.display = 'none';
      }
    });
  </script>
</body>
</html>
