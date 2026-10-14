<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';

requireRole('Teacher');

// -----------------------------
// Validate Session ID
// -----------------------------

if (!isset($_GET['session_id']) || !is_numeric($_GET['session_id'])) {
    die("Invalid Attendance Session.");
}

$sessionId = (int) $_GET['session_id'];

// -----------------------------
// Fetch Attendance Session
// -----------------------------

$sessionStmt = $pdo->prepare("
SELECT
    attendance_sessions.*,
    subjects.subject_name
FROM attendance_sessions
INNER JOIN subjects
    ON attendance_sessions.subject_id = subjects.id
WHERE attendance_sessions.id = ?
AND attendance_sessions.teacher_id = ?
");

$sessionStmt->execute([
    $sessionId,
    $_SESSION['teacher_id']
]);

$session = $sessionStmt->fetch(PDO::FETCH_ASSOC);

if (!$session) {
    die("Attendance record not found.");
}

// -----------------------------
// Fetch Students Attendance
// -----------------------------

$attendanceStmt = $pdo->prepare("
SELECT
    students.roll_no,
    students.full_name,
    attendance.status
FROM attendance
INNER JOIN students
    ON attendance.student_id = students.id
WHERE attendance.session_id = ?
ORDER BY students.roll_no ASC
");

$attendanceStmt->execute([$sessionId]);

$attendanceList = $attendanceStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>View Attendance</title>

<link rel="stylesheet" href="../css/style.css">

</head>

<body>

<?php require_once '../includes/navbar.php'; ?>

<div class="container">

<div class="attendance-card">

<h2>Attendance Details</h2>

<p><strong>Subject:</strong> <?= htmlspecialchars($session['subject_name']) ?></p>

<p><strong>Lecture:</strong> <?= htmlspecialchars($session['lecture_name']) ?></p>

<p><strong>Date:</strong> <?= htmlspecialchars($session['lecture_date']) ?></p>

<p><strong>Time:</strong>
<?= htmlspecialchars($session['start_time']) ?>
-
<?= htmlspecialchars($session['end_time']) ?>
</p>

<hr>

<table class="attendance-table">

<thead>

<tr>

<th>Roll No</th>
<th>Student Name</th>
<th>Status</th>

</tr>

</thead>

<tbody>

<?php foreach ($attendanceList as $student): ?>

<tr>

<td><?= htmlspecialchars($student['roll_no']) ?></td>

<td><?= htmlspecialchars($student['full_name']) ?></td>

<td>

<?php if ($student['status'] == 'Present'): ?>

<span style="color:green;font-weight:bold;">

Present

</span>

<?php else: ?>

<span style="color:red;font-weight:bold;">

Absent

</span>

<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<br>

<a href="attendance_history.php" class="btn">

← Back to History

</a>

</div>

</div>

<?php require_once '../includes/footer.php'; ?>

</body>

</html>