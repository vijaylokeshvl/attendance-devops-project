<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';

requireRole('Admin');

/* ==========================
   VALIDATE SESSION ID
========================== */

if (!isset($_GET['session_id']) || !is_numeric($_GET['session_id'])) {

    die("Invalid Attendance Session.");

}

$sessionId = (int)$_GET['session_id'];


/* ==========================
   SESSION DETAILS
========================== */

$sessionStmt = $pdo->prepare("

SELECT

attendance_sessions.*,

subjects.subject_name,

teachers.full_name AS teacher_name

FROM attendance_sessions

INNER JOIN subjects
ON attendance_sessions.subject_id = subjects.id

INNER JOIN teachers
ON attendance_sessions.teacher_id = teachers.id

WHERE attendance_sessions.id = ?

");

$sessionStmt->execute([$sessionId]);

$session = $sessionStmt->fetch(PDO::FETCH_ASSOC);

if (!$session){

    die("Attendance Session Not Found.");

}


/* ==========================
   STUDENT ATTENDANCE
========================== */

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

$students = $attendanceStmt->fetchAll(PDO::FETCH_ASSOC);


/* ==========================
   STATISTICS
========================== */

$totalStudents = count($students);

$present = 0;

$absent = 0;

foreach($students as $student){

    if($student['status']=="Present"){

        $present++;

    }else{

        $absent++;

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Attendance Details</title>

<link rel="stylesheet"
href="../css/style.css">

</head>

<body>

<?php require_once '../includes/navbar.php'; ?>

<main class="students-page">

<div class="students-container">

<!-- ==========================
PAGE HEADER
========================== -->

<div class="page-header">

<div>

<span class="page-label">

ADMIN PANEL

</span>

<h1>

📋 Attendance Details

</h1>

<p>

Complete attendance report of this lecture.

</p>

</div>

<a
href="attendance.php"
class="dashboard-btn">

← Back

</a>

</div>

<!-- ==========================
STATISTICS
========================== -->

<div class="stats-grid">

<div class="stat-card">

<h3>Total Students</h3>

<h1><?= $totalStudents ?></h1>

</div>

<div class="stat-card">

<h3>Present</h3>

<h1><?= $present ?></h1>

</div>

<div class="stat-card">

<h3>Absent</h3>

<h1><?= $absent ?></h1>

</div>

</div>

<!-- ==========================
SESSION DETAILS
========================== -->

<div class="student-list-card">

<div class="section-title">

<h2>

Lecture Information

</h2>

<p>

Session Details

</p>

</div>

<div class="form-grid">

<div class="form-group">

<label>Teacher</label>

<input
type="text"
value="<?= htmlspecialchars($session['teacher_name']) ?>"
readonly>

</div>

<div class="form-group">

<label>Subject</label>

<input
type="text"
value="<?= htmlspecialchars($session['subject_name']) ?>"
readonly>

</div>

<div class="form-group">

<label>Lecture</label>

<input
type="text"
value="<?= htmlspecialchars($session['lecture_name']) ?>"
readonly>

</div>

<div class="form-group">

<label>Date</label>

<input
type="text"
value="<?= date("d M Y",strtotime($session['lecture_date'])) ?>"
readonly>

</div>

<div class="form-group">

<label>Start Time</label>

<input
type="text"
value="<?= date("h:i A",strtotime($session['start_time'])) ?>"
readonly>

</div>

<div class="form-group">

<label>End Time</label>

<input
type="text"
value="<?= date("h:i A",strtotime($session['end_time'])) ?>"
readonly>

</div>

</div>

<hr style="margin:25px 0;">

<div class="section-title">

<h2>

Student Attendance

</h2>

<p>

Search student by Roll No or Name.

</p>

</div>

<div class="search-form">

<input
type="text"
id="searchStudent"
placeholder="🔍 Search Student...">

</div>

<div class="table-wrapper">

<table
class="students-table"
id="attendanceTable">

<thead>

<tr>

<th>Roll No</th>

<th>Student Name</th>

<th>Status</th>

</tr>

</thead>

<tbody>
    <?php if (!empty($students)): ?>

    <?php foreach ($students as $student): ?>

    <tr>

        <td>

            <?= htmlspecialchars($student['roll_no']) ?>

        </td>

        <td>

            <?= htmlspecialchars($student['full_name']) ?>

        </td>

        <td>

            <?php if($student['status']=="Present"): ?>

                <span class="status-badge present-badge">

                    ✓ Present

                </span>

            <?php else: ?>

                <span class="status-badge absent-badge">

                    ✗ Absent

                </span>

            <?php endif; ?>

        </td>

    </tr>

    <?php endforeach; ?>

<?php else: ?>

<tr>

<td colspan="3" class="no-data">

📋 No Attendance Found

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

<div class="form-actions" style="margin-top:25px;">

<a
href="attendance.php"
class="secondary-btn">

← Back to Attendance

</a>

<button
onclick="window.print()"
class="primary-btn"
type="button">

🖨 Print Report

</button>

</div>

</div>

</div>

</main>

<?php require_once '../includes/footer.php'; ?>

<script>

const searchInput = document.getElementById("searchStudent");

const table = document.getElementById("attendanceTable");

const rows = table.getElementsByTagName("tr");

searchInput.addEventListener("keyup", function(){

    const value = this.value.toLowerCase();

    for(let i=1;i<rows.length;i++){

        const roll = rows[i].cells[0]?.textContent.toLowerCase() || "";

        const name = rows[i].cells[1]?.textContent.toLowerCase() || "";

        if(

            roll.includes(value) ||

            name.includes(value)

        ){

            rows[i].style.display="";

        }

        else{

            rows[i].style.display="none";

        }

    }

});

</script>

</body>

</html>