<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header("Location: auth.php");
    exit();
}

$facultyName = $_SESSION['name'];

// Fetch all students
$sql = "SELECT u.id AS student_id, u.name AS student_name, s.year 
        FROM users u
        JOIN students s ON u.id = s.user_id
        ORDER BY s.year, u.name";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Faculty Dashboard</title>

</head>
<link rel="stylesheet" href="faculty_dashboard.css">
<body>
  <h1>Welcome, <?=htmlspecialchars($facultyName)?> (Faculty)</h1>
  <h3>All Registered Students and Their Weekly Progress</h3>

  <?php if ($result && $result->num_rows > 0): ?>
    <?php while ($student = $result->fetch_assoc()): ?>
      <h4><?=htmlspecialchars($student['student_name'])?> (Year: <?=htmlspecialchars($student['year'])?>)</h4>

      <?php
      // Fetch weekly progress for this student
      $stmt = $conn->prepare("SELECT week_ending, progress_text, file_path FROM weekly_progress WHERE user_id = ? ORDER BY week_ending DESC");
      $stmt->bind_param("i", $student['student_id']);
      $stmt->execute();
      $progress_result = $stmt->get_result();
      ?>

      <?php if ($progress_result && $progress_result->num_rows > 0): ?>
        <table class="progress-table">
          <thead>
            <tr>
              <th>Week Ending</th>
              <th>Progress Description</th>
              <th>File</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($progress = $progress_result->fetch_assoc()): ?>
              <tr>
                <td><?=htmlspecialchars($progress['week_ending'])?></td>
                <td><?=nl2br(htmlspecialchars($progress['progress_text']))?></td>
                <td>
                  <?php if (!empty($progress['file_path']) && file_exists($progress['file_path'])): ?>
                    <a href="<?=htmlspecialchars($progress['file_path'])?>" target="_blank" class="file-link">View/Download</a>
                  <?php else: ?>
                    No file
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No progress submitted yet.</p>
      <?php endif; ?>

    <?php endwhile; ?>
  <?php else: ?>
    <p>No students found.</p>
  <?php endif; ?>

  <p><a href="logout.php">Logout</a></p>
</body>
</html>
