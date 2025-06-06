<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: auth.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch student name and year
$sql = "SELECT u.name, s.year FROM users u JOIN students s ON u.id = s.user_id WHERE u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Student record not found.";
    exit();
}

$row = $result->fetch_assoc();

$message = "";
$show_tick = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $progress_text = $_POST['progress_text'] ?? '';
    $week_ending = $_POST['week_ending'] ?? '';

    if (empty($progress_text) || empty($week_ending)) {
        $message = "<p class='message error'>Please fill in all required fields.</p>";
    } else {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_path = null;

        if (isset($_FILES['progress_file']) && $_FILES['progress_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['progress_file']['tmp_name'];
            $file_name = basename($_FILES['progress_file']['name']);
            $target_file = $upload_dir . time() . '_' . preg_replace("/[^a-zA-Z0-9._-]/", "_", $file_name);

            if (move_uploaded_file($file_tmp, $target_file)) {
                $file_path = $target_file;
            } else {
                $message = "<p class='message error'>File upload failed.</p>";
            }
        }

        if (!$message) {
            $sql = "INSERT INTO weekly_progress (user_id, week_ending, progress_text, file_path) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isss", $user_id, $week_ending, $progress_text, $file_path);
            if ($stmt->execute()) {
                $message = "<p class='message success'>Progress updated successfully.</p>";
                $show_tick = true;
            } else {
                $message = "<p class='message error'>Failed to save progress.</p>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Dashboard</title>
  
  <style>
    * {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: linear-gradient(to right, #f0f4f7, #e1ecf4);
  padding: 40px 20px;
  max-width: 700px;
  margin: 0 auto;
  color: #ff0000;
  position: relative;
}

/* Success Tick Box */
.tick-success {
  position: fixed;
  top: 20px;
  right: 20px;
  background-color: #4caf50;
  color: white;
  font-size: 3rem;
  width: 60px;
  height: 60px;
  text-align: center;
  line-height: 60px;
  border-radius: 50%;
  box-shadow: 0 0 15px rgba(76, 175, 80, 0.4);
  display: none;
  z-index: 1000;
  animation: pop 0.4s ease;
}

@keyframes pop {
  0% {
    transform: scale(0.4);
    opacity: 0;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

/* Headings */
h1 {
  font-size: 2.2rem;
  margin-bottom: 5px;
  color: #4a148c;
}

h3 {
  font-weight: normal;
  color: #666;
  margin-top: 0;
  margin-bottom: 25px;
}

/* Feedback messages */
p.message {
  margin-top: 20px;
  font-weight: 600;
  font-size: 1rem;
  padding: 12px 15px;
  border-radius: 6px;
  background-color: #f9f9f9;
  border-left: 5px solid #ccc;
}

p.message.error {
  color: #d32f2f;
  background-color: #fdecea;
  border-left-color: #e53935;
}

p.message.success {
  color: #2e7d32;
  background-color: #e8f5e9;
  border-left-color: #388e3c;
}

/* Form container */
form {
  background: #ffffff;
  padding: 30px 25px;
  border-radius: 12px;
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
  margin-bottom: 40px;
}

form:hover {
  transform: translateY(-2px);
  transition: transform 0.3s ease;
}

/* Labels */
label {
  display: block;
  margin-top: 20px;
  font-weight: 600;
  color: #444;
}

/* Inputs and textarea */
input[type="date"],
input[type="file"],
textarea {
  width: 100%;
  padding: 12px 14px;
  margin-top: 8px;
  border: 1.5px solid #ccc;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.3s, box-shadow 0.3s;
}

input[type="date"]:focus,
input[type="file"]:focus,
textarea:focus {
  outline: none;
  border-color: #7b1fa2;
  box-shadow: 0 0 8px rgba(123, 31, 162, 0.4);
}

/* Textarea */
textarea {
  min-height: 130px;
  resize: vertical;
}

/* Submit button */
button[type="submit"] {
  margin-top: 30px;
  background: linear-gradient(135deg, #7b1fa2, #512da8);
  color: white;
  border: none;
  padding: 14px 28px;
  font-size: 1.1rem;
  font-weight: 700;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.3s, transform 0.2s;
}

button[type="submit"]:hover {
  background: linear-gradient(135deg, #6a1b9a, #4527a0);
  transform: translateY(-2px);
}

/* Logout button at bottom */
.logout-button {
  display: block;
  margin: 0 auto;
  background-color: #dc3545;
  color: white;
  padding: 12px 20px;
  font-size: 1rem;
  font-weight: 600;
  border-radius: 7px;
  text-align: center;
  text-decoration: none;
  transition: background-color 0.3s;
  max-width: 200px;
}

.logout-button:hover {
  background-color: #c82333;
}

  </style>
</head>

<body>
  
  <h1>Welcome, <?= htmlspecialchars($row['name']) ?> (Student)</h1>
  <h3>Year: <?= htmlspecialchars($row['year']) ?></h3>

  <?= $message ?>

  <form method="POST" enctype="multipart/form-data">
    <label for="week_ending">Week Ending Date (required):</label>
    <input type="date" id="week_ending" name="week_ending" required>

    <label for="progress_text">Weekly Progress (required):</label>
    <textarea id="progress_text" name="progress_text" required placeholder="Describe what you did this week..."></textarea>

    <label for="progress_file">Upload File (optional):</label>
    <input type="file" id="progress_file" name="progress_file" accept=".pdf,.doc,.docx,.txt,.zip,.rar,.7z,.png,.jpg,.jpeg,.gif,.py,.js,.java,.c,.cpp,.php">

    <button type="submit">Submit Progress</button>
  </form>

  <!-- Logout button at bottom -->
  <a class="logout-button" href="logout.php">Logout</a>

  <!-- Tick animation on successful submit -->
  <div class="tick-success" id="tickSuccess">✔</div>

  <script>
    window.addEventListener('DOMContentLoaded', () => {
      <?php if ($show_tick): ?>
        const tick = document.getElementById('tickSuccess');
        tick.style.display = 'block';
        setTimeout(() => {
          tick.style.display = 'none';
        }, 3000);
      <?php endif; ?>
    });
  </script>
</body>


</html>
