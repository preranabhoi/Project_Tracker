<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Project Tracker | Login/Register</title>
  <link rel="stylesheet" href="authh.css">
</head>
<body>
  <div class="container">
    <h2>Project Tracker</h2>

    <!-- Login Form -->
    <form action="login_handler.php" method="POST" class="form-section" id="loginForm">
      <label for="loginRole">Role:</label>
      <select name="role" id="loginRole" required>
        <option value="">Select Role</option>
        <option value="student">Student</option>
        <option value="faculty">Faculty</option>
      </select>

      <label>Email:</label>
      <input type="email" name="email" required>

      <label>Password:</label>
      <input type="password" name="password" required>

      <button type="submit">Login</button>

      <p class="form-switch-link">Don't have an account? <a href="#" onclick="showForm('register')">Register</a></p>
    </form>

    <!-- Register Form -->
    <form action="register_handler.php" method="POST" class="form-section" id="registerForm">
      <label for="registerRole">Role:</label>
      <select name="role" id="registerRole" required onchange="toggleStudentFields(this.value)">
        <option value="">Select Role</option>
        <option value="student">Student</option>
        <option value="faculty">Faculty</option>
      </select>

      <label>Name:</label>
      <input type="text" name="name" required>

      <label>Email:</label>
      <input type="email" name="email" required>

      <label>Password:</label>
      <input type="password" name="password" required>

      <div id="studentFields">
        <label>Year:</label>
        <select name="year">
          <option value="SY">SY</option>
          <option value="TY">TY</option>
          <option value="BTech">BTech</option>
        </select>
      </div>

      <button type="submit">Register</button>

      <p class="form-switch-link">Already have an account? <a href="#" onclick="showForm('login')">Login</a></p>
    </form>
  </div>

  <script>
    function showForm(formType) {
      document.getElementById('loginForm').classList.remove('active');
      document.getElementById('registerForm').classList.remove('active');
      if (formType === 'login') {
        document.getElementById('loginForm').classList.add('active');
      } else {
        document.getElementById('registerForm').classList.add('active');
      }
    }

    function toggleStudentFields(role) {
      const studentFields = document.getElementById('studentFields');
      if (role === 'student') {
        studentFields.classList.add('visible');
      } else {
        studentFields.classList.remove('visible');
      }
    }

    window.onload = () => showForm('login');
  </script>
</body>
</html>
