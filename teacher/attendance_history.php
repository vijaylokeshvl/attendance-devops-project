<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';

requireRole('Teacher');

$stmt = $pdo->prepare("
SELECT
    attendance_sessions.id,
    subjects.subject_name,
    attendance_sessions.lecture_name,
    attendance_sessions.lecture_date,
    attendance_sessions.start_time,
    attendance_sessions.end_time
FROM attendance_sessions
INNER JOIN subjects
    ON attendance_sessions.subject_id = subjects.id
WHERE attendance_sessions.teacher_id = ?
ORDER BY attendance_sessions.lecture_date DESC,
attendance_sessions.start_time DESC
");

$stmt->execute([$_SESSION['teacher_id']]);

$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Attendance History</title>

<link rel="stylesheet" href="../css/style.css">

</head>

<body>

<?php require_once '../includes/navbar.php'; ?>

<div class="container">

<div class="attendance-card">

<a href="dashboard.php" class="btn">← Back to Dashboard</a><br><br>

<h2>Attendance History</h2>

<table class="attendance-table">

<thead>

<tr>

<th>Date</th>
<th>Subject</th>
<th>Lecture</th>
<th>Start</th>
<th>End</th>
<th>View</th>

</tr>

</thead>

<tbody>

<?php if(count($sessions)>0): ?>

<?php foreach($sessions as $row): ?>

<tr>

<td><?= htmlspecialchars($row['lecture_date']) ?></td>

<td><?= htmlspecialchars($row['subject_name']) ?></td>

<td><?= htmlspecialchars($row['lecture_name']) ?></td>

<td><?= htmlspecialchars($row['start_time']) ?></td>

<td><?= htmlspecialchars($row['end_time']) ?></td>

<td>

<a
class="btn"
href="view_attendance.php?session_id=<?= $row['id']; ?>">

View

</a>

</td>

</tr>

<?php endforeach; ?>

<?php else: ?>

<tr>

<td colspan="6">

No attendance found.

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

<?php require_once '../includes/footer.php'; ?>

</body>

</html>