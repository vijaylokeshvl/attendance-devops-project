<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';

requireRole('Teacher');

// -------------------------
// Fetch Subjects
// -------------------------

$subjectStmt = $pdo->query("
SELECT id, subject_name
FROM subjects
ORDER BY subject_name ASC
");

$subjects = $subjectStmt->fetchAll(PDO::FETCH_ASSOC);

// -------------------------
// Fetch Students
// -------------------------

$studentStmt = $pdo->query("
SELECT
id,
roll_no,
full_name
FROM students
ORDER BY roll_no ASC
");

$students = $studentStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Mark Attendance</title>

<link rel="stylesheet" href="../css/style.css">

</head>

<body>

<?php require_once '../includes/navbar.php'; ?>

<div class="attendance-page">

<div class="attendance-container">

<?php if(isset($_SESSION['success'])): ?>

<div class="success-message">

<i class="fa-solid fa-circle-check"></i>

<?= $_SESSION['success']; ?>

</div>

<?php unset($_SESSION['success']); ?>

<?php endif; ?>


<?php if(isset($_SESSION['error'])): ?>

<div class="error-message">

<i class="fa-solid fa-circle-exclamation"></i>

<?= $_SESSION['error']; ?>

</div>

<?php unset($_SESSION['error']); ?>

<?php endif; ?>


<!-- =====================================
            PAGE HEADER
====================================== -->

<div class="attendance-header">

<div>

<a href="dashboard.php" class="back-dashboard">

<i class="fa-solid fa-arrow-left"></i>

Dashboard

</a>

<h1>

<i class="fa-solid fa-user-check"></i>

Mark Attendance

</h1>

<p>

Record attendance quickly and accurately for today's lecture.

</p>

</div>

<div class="teacher-badge">

<i class="fa-solid fa-chalkboard-user"></i>

<a href="dashboard.php"><=Teacher Panel</a>
</div>

</div>


<!-- =====================================
            STATISTICS
====================================== -->

<div class="attendance-stats">

<div class="attendance-stat-card">

<div class="stat-icon blue">

<i class="fa-solid fa-users"></i>

</div>

<div>

<h2>

<?= count($students); ?>

</h2>

<p>

Students

</p>

</div>

</div>

<div class="attendance-stat-card">

<div class="stat-icon green">

<i class="fa-solid fa-circle-check"></i>

</div>

<div>

<h2 id="presentCount">

0

</h2>

<p>

Present

</p>

</div>

</div>

<div class="attendance-stat-card">

<div class="stat-icon red">

<i class="fa-solid fa-circle-xmark"></i>

</div>

<div>

<h2 id="absentCount">

0

</h2>

<p>

Absent

</p>

</div>

</div>

<div class="attendance-stat-card">

<div class="stat-icon orange">

<i class="fa-solid fa-chart-pie"></i>

</div>

<div>

<h2 id="attendancePercent">

0%

</h2>

<p>

Attendance

</p>

</div>

</div>

</div>


<!-- =====================================
            MAIN CARD
====================================== -->

<div class="attendance-main-card">

<div class="card-title">

<i class="fa-solid fa-book-open"></i>

Lecture Information

</div>

<form
id="attendanceForm"
action="save_attendance.php"
method="POST"
>

<div class="attendance-form-grid">

<!-- Subject -->

<div class="modern-input">

<label>

Subject

</label>

<div class="input-box">

<i class="fa-solid fa-book"></i>

<select
name="subject"
id="subject"
required
>

<option value="">

Select Subject

</option>

<?php foreach($subjects as $subject): ?>

<option value="<?= $subject['id']; ?>">

<?= htmlspecialchars($subject['subject_name']); ?>

</option>

<?php endforeach; ?>

</select>

</div>

</div>


<!-- Lecture -->

<div class="modern-input">

<label>

Lecture Name

</label>

<div class="input-box">

<i class="fa-solid fa-pen"></i>

<input
type="text"
name="lecture_name"
id="lecture_name"
maxlength="100"
placeholder="Enter Lecture Name"
required
>

</div>

</div>


<!-- Start -->

<div class="modern-input">

<label>

Start Time

</label>

<div class="input-box">

<i class="fa-solid fa-clock"></i>

<input
type="time"
name="start_time"
id="start_time"
required
>

</div>

</div>


<!-- End -->

<div class="modern-input">

<label>

End Time

</label>

<div class="input-box">

<i class="fa-solid fa-hourglass-end"></i>

<input
type="time"
name="end_time"
id="end_time"
required
>

</div>

</div>

</div>

<hr class="attendance-divider">

<!-- =====================================
            SEARCH & ACTIONS
====================================== -->

<div class="attendance-toolbar">

    <div class="attendance-search">

        <i class="fa-solid fa-magnifying-glass"></i>

        <input
            type="text"
            id="searchStudent"
            placeholder="Search by Roll No or Student Name..."
        >

    </div>

    <div class="attendance-actions">

        <button
            type="button"
            id="allPresent"
            class="action-btn present-btn"
        >

            <i class="fa-solid fa-circle-check"></i>

            All Present

        </button>

        <button
            type="button"
            id="allAbsent"
            class="action-btn absent-btn"
        >

            <i class="fa-solid fa-circle-xmark"></i>

            All Absent

        </button>

        <button
            type="button"
            id="clearAttendance"
            class="action-btn clear-btn"
        >

            <i class="fa-solid fa-eraser"></i>

            Clear

        </button>

    </div>

</div>

<!-- =====================================
            STUDENT TABLE
====================================== -->

<div class="attendance-table-card">

<div class="table-header">

<div>

<h2>

<i class="fa-solid fa-users"></i>

Student Attendance

</h2>

<p>

Total Students :

<strong>

<?= count($students); ?>

</strong>

</p>

</div>

<div class="attendance-progress">

<div class="progress-bar">

<div
id="attendanceProgress"
class="progress-fill"
style="width:0%;"
>

</div>

</div>

<small>

Attendance Progress

</small>

</div>

</div>

<div class="table-wrapper">

<table class="attendance-table">

<thead>

<tr>

<th width="15%">

Roll No

</th>

<th>

Student Name

</th>

<th width="18%">

Present

</th>

<th width="18%">

Absent

</th>

</tr>

</thead>

<tbody>

<?php foreach($students as $student): ?>

<tr>

<td>

<span class="roll-badge">

<?= htmlspecialchars($student['roll_no']); ?>

</span>

</td>

<td>

<div class="student-info">

<div class="student-avatar">

<?= strtoupper(substr($student['full_name'],0,1)); ?>

</div>

<div>

<strong>

<?= htmlspecialchars($student['full_name']); ?>

</strong>

</div>

</div>

</td>

<td>

<label class="radio-card">

<input
type="radio"
name="attendance[<?= $student['id']; ?>]"
value="Present"
>

<span>

<i class="fa-solid fa-check"></i>

Present

</span>

</label>

</td>

<td>

<label class="radio-card absent">

<input
type="radio"
name="attendance[<?= $student['id']; ?>]"
value="Absent"
>

<span>

<i class="fa-solid fa-xmark"></i>

Absent

</span>

</label>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>

<!-- =====================================
            SAVE BUTTON
====================================== -->

<div class="attendance-save">

<button
type="submit"
class="save-attendance-btn"
id="saveAttendance"
disabled
>

<i class="fa-solid fa-floppy-disk"></i>

Save Attendance

</button>

</div>

</form>

</div>

</div>

</div>

<script src="../js/script.js"></script>

<?php require_once '../includes/footer.php'; ?>

</body>

</html>