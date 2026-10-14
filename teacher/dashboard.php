<?php

require_once '../includes/auth.php';

requireRole('Teacher');
require_once '../includes/db.php';

// ---------------------------
// Dashboard Statistics
// ---------------------------

$totalStudents = $pdo->query("
SELECT COUNT(*) FROM students
")->fetchColumn();

$totalSubjects = $pdo->query("
SELECT COUNT(*) FROM subjects
")->fetchColumn();

$totalSessions = $pdo->query("
SELECT COUNT(*) FROM attendance_sessions
")->fetchColumn();

$todaySessions = $pdo->prepare("
SELECT COUNT(*)
FROM attendance_sessions
WHERE lecture_date = CURDATE()
");

$todaySessions->execute();

$todayAttendance = $todaySessions->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Teacher Dashboard</title>

<link rel="stylesheet" href="../css/style.css">

</head>

<body>

<?php require_once '../includes/navbar.php'; ?>

<div class="container">

<div class="teacher-dashboard">

<div class="teacher-container">

<!-- ==========================
        HEADER
========================== -->

<div class="teacher-header">

<div>

<span class="dashboard-badge">

<i class="fa-solid fa-chalkboard-user"></i>

Teacher Panel

</span>

<h1>

Welcome,
<?= htmlspecialchars($_SESSION['username']); ?>

👋

</h1>

<p>

Manage attendance, monitor lectures and access reports from one place.

</p>

</div>

<div class="teacher-avatar">

<i class="fa-solid fa-user-tie"></i>

</div>

</div>

<!-- ==========================
        STATISTICS
========================== -->

<div class="teacher-stats">

<div class="teacher-stat-card">

<div class="teacher-icon blue">

<i class="fa-solid fa-users"></i>

</div>

<div>

<h2>

<?= $totalStudents; ?>

</h2>

<p>

Students

</p>

</div>

</div>

<div class="teacher-stat-card">

<div class="teacher-icon green">

<i class="fa-solid fa-book"></i>

</div>

<div>

<h2>

<?= $totalSubjects; ?>

</h2>

<p>

Subjects

</p>

</div>

</div>

<div class="teacher-stat-card">

<div class="teacher-icon orange">

<i class="fa-solid fa-calendar-check"></i>

</div>

<div>

<h2>

<?= $todayAttendance; ?>

</h2>

<p>

Today's Sessions

</p>

</div>

</div>

<div class="teacher-stat-card">

<div class="teacher-icon red">

<i class="fa-solid fa-folder-open"></i>

</div>

<div>

<h2>

<?= $totalSessions; ?>

</h2>

<p>

Total Sessions

</p>

</div>

</div>

</div>

<!-- ==========================
        QUICK ACTIONS
========================== -->

<h2 class="section-heading">

Quick Actions

</h2>

<div class="teacher-action-grid">

<a
href="mark_attendance.php"
class="teacher-action-card"
>

<div class="action-icon">

<i class="fa-solid fa-user-check"></i>

</div>

<h3>

Mark Attendance

</h3>

<p>

Take attendance quickly for today's lecture.

</p>

</a>

<a
href="attendance_history.php"
class="teacher-action-card"
>

<div class="action-icon history">

<i class="fa-solid fa-clock-rotate-left"></i>

</div>

<h3>

Attendance History

</h3>

<p>

View previously saved attendance records.

</p>

</a>

</div>

<!-- ==========================
        INFORMATION
========================== -->

<div class="teacher-info-card">

<h2>

<i class="fa-solid fa-lightbulb"></i>

Teaching Tips

</h2>

<ul>

<li>

Complete attendance before ending every lecture.

</li>

<li>

Verify the selected subject before saving attendance.

</li>

<li>

Review attendance history regularly for accuracy.

</li>

<li>

Use the search box to find students faster.

</li>

</ul>

</div>

</div>

</div>
<?php require_once '../includes/footer.php'; ?>

</body>

</html>