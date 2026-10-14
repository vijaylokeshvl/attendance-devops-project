<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';

requireRole('Student');

$studentId = $_SESSION['student_id'];

// ----------------------------
// Student Details
// ----------------------------

$stmt = $pdo->prepare("
SELECT
    full_name,
    roll_no,
    semester
FROM students
WHERE id=?
");

$stmt->execute([$studentId]);

$student = $stmt->fetch(PDO::FETCH_ASSOC);

// ----------------------------
// Overall Statistics
// ----------------------------

$total = $pdo->prepare("
SELECT COUNT(*) FROM attendance
WHERE student_id=?
");

$total->execute([$studentId]);

$totalClasses = $total->fetchColumn();

$present = $pdo->prepare("
SELECT COUNT(*) FROM attendance
WHERE student_id=?
AND status='Present'
");

$present->execute([$studentId]);

$presentCount = $present->fetchColumn();

$absentCount = $totalClasses - $presentCount;

$percentage = 0;

if($totalClasses>0){

    $percentage = round(($presentCount/$totalClasses)*100,2);

}

// ----------------------------
// Subject Wise Attendance
// ----------------------------

$subjectAttendance = $pdo->prepare("

SELECT

subjects.subject_name,

SUM(CASE WHEN attendance.status='Present' THEN 1 ELSE 0 END) AS present,

COUNT(attendance.id) AS total

FROM attendance

INNER JOIN attendance_sessions

ON attendance.session_id=attendance_sessions.id

INNER JOIN subjects

ON attendance_sessions.subject_id=subjects.id

WHERE attendance.student_id=?

GROUP BY subjects.subject_name

ORDER BY subjects.subject_name

");

$subjectAttendance->execute([$studentId]);

$subjects = $subjectAttendance->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Student Dashboard</title>

<link rel="stylesheet" href="../css/style.css">

</head>

<body>

<?php require_once '../includes/navbar.php'; ?>

<div class="container">

<div class="student-dashboard">

<div class="student-container">

<!-- ==========================
        HERO SECTION
========================== -->

<div class="student-header">

<div>

<span class="student-badge">

<i class="fa-solid fa-user-graduate"></i>

Student Dashboard

</span>

<h1>

Welcome,

<?= htmlspecialchars($student['full_name']); ?>

👋

</h1>

<p>

Track your attendance and monitor your academic progress.

</p>

</div>

<div class="student-avatar">

<i class="fa-solid fa-user"></i>

</div>

</div>

<!-- ==========================
        PROFILE
========================== -->

<div class="student-profile">

<div class="profile-item">

<h4>

Roll Number

</h4>

<p>

<?= htmlspecialchars($student['roll_no']); ?>

</p>

</div>

<div class="profile-item">

<h4>

Semester

</h4>

<p>

<?= htmlspecialchars($student['semester']); ?>

</p>

</div>

<div class="profile-item">

<h4>

Attendance Status

</h4>

<p>

<?php

if($percentage>=90){

echo "<span class='status excellent'>Excellent</span>";

}elseif($percentage>=75){

echo "<span class='status good'>Good</span>";

}elseif($percentage>=50){

echo "<span class='status average'>Average</span>";

}else{

echo "<span class='status poor'>Needs Improvement</span>";

}

?>

</p>

</div>

</div>

<!-- ==========================
        STATISTICS
========================== -->

<div class="student-stats">

<div class="student-stat-card">

<div class="student-icon blue">

<i class="fa-solid fa-book-open"></i>

</div>

<div>

<h2>

<?= $totalClasses; ?>

</h2>

<p>

Total Classes

</p>

</div>

</div>

<div class="student-stat-card">

<div class="student-icon green">

<i class="fa-solid fa-circle-check"></i>

</div>

<div>

<h2>

<?= $presentCount; ?>

</h2>

<p>

Present

</p>

</div>

</div>

<div class="student-stat-card">

<div class="student-icon red">

<i class="fa-solid fa-circle-xmark"></i>

</div>

<div>

<h2>

<?= $absentCount; ?>

</h2>

<p>

Absent

</p>

</div>

</div>

<div class="student-stat-card">

<div class="student-icon orange">

<i class="fa-solid fa-chart-line"></i>

</div>

<div>

<h2>

<?= $percentage; ?>%

</h2>

<p>

Attendance

</p>

</div>

</div>

</div>

<!-- ==========================
        SUBJECT TABLE
========================== -->

<div class="subject-card">

<div class="subject-header">

<h2>

<i class="fa-solid fa-book"></i>

Subject-wise Attendance

</h2>

</div>

<div class="table-responsive">

<table class="attendance-table modern-table">

<thead>

<tr>

<th>Subject</th>

<th>Present</th>

<th>Total</th>

<th>Attendance %</th>

</tr>

</thead>

<tbody>

<?php if(count($subjects)>0): ?>

<?php foreach($subjects as $row):

$per=0;

if($row['total']>0){

$per=round(($row['present']/$row['total'])*100,2);

}

?>

<tr>

<td>

<?= htmlspecialchars($row['subject_name']); ?>

</td>

<td>

<?= $row['present']; ?>

</td>

<td>

<?= $row['total']; ?>

</td>

<td>

<span class="percentage-badge">

<?= $per; ?>%

</span>

</td>

</tr>

<?php endforeach; ?>

<?php else: ?>

<tr>

<td colspan="4" style="text-align:center;">

No Attendance Available

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

<!-- ==========================
        STUDENT TIPS
========================== -->

<div class="student-tips">

<h2>

<i class="fa-solid fa-lightbulb"></i>

Attendance Tips

</h2>

<ul>

<li>Maintain at least 75% attendance every semester.</li>

<li>Attend lectures regularly to improve your academic performance.</li>

<li>Review your attendance weekly.</li>

<li>Contact your teacher if you notice any attendance discrepancy.</li>

</ul>

</div>

</div>

</div>
<?php require_once '../includes/footer.php'; ?>

</body>

</html>