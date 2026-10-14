<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';

requireRole('Admin');

/* ==========================
   FETCH ATTENDANCE SESSIONS
========================== */

$stmt = $pdo->prepare("
SELECT
attendance_sessions.id,
attendance_sessions.lecture_name,
attendance_sessions.lecture_date,
attendance_sessions.start_time,
attendance_sessions.end_time,
subjects.subject_name,
teachers.full_name AS teacher_name

FROM attendance_sessions

INNER JOIN subjects
ON attendance_sessions.subject_id = subjects.id

INNER JOIN teachers
ON attendance_sessions.teacher_id = teachers.id

ORDER BY attendance_sessions.lecture_date DESC,
attendance_sessions.start_time DESC
");

$stmt->execute();

$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalSessions = count($sessions);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Attendance Report</title>

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

📊 Attendance Report

</h1>

<p>

View all attendance sessions recorded by teachers.

</p>

</div>

<a
href="dashboard.php"
class="dashboard-btn">

← Dashboard

</a>

</div>

<!-- ==========================
STATISTICS
========================== -->

<div class="stats-grid">

<div class="stat-card">

<h3>

Attendance Sessions

</h3>

<h1>

<?= $totalSessions ?>

</h1>

</div>

</div>

<!-- ==========================
SEARCH
========================== -->

<div class="student-list-card">

<div class="section-title">

<h2>

Attendance History

</h2>

<p>

Search by Subject, Teacher or Lecture Name.

</p>

</div>

<div class="search-form">

<input
type="text"
id="searchAttendance"
placeholder="🔍 Search Attendance..."
>

</div>

<div class="table-wrapper">

<table
class="students-table"
id="attendanceTable">

<thead>

<tr>

<th>Date</th>

<th>Subject</th>

<th>Teacher</th>

<th>Lecture</th>

<th>Start</th>

<th>End</th>

<th>Action</th>

</tr>

</thead>

<tbody>
    <?php if (!empty($sessions)): ?>

    <?php foreach ($sessions as $row): ?>

        <tr>

            <td>

                <?= date("d M Y", strtotime($row['lecture_date'])) ?>

            </td>

            <td>

                <?= htmlspecialchars($row['subject_name']) ?>

            </td>

            <td>

                <?= htmlspecialchars($row['teacher_name']) ?>

            </td>

            <td>

                <?= htmlspecialchars($row['lecture_name']) ?>

            </td>

            <td>

                <?= date("h:i A", strtotime($row['start_time'])) ?>

            </td>

            <td>

                <?= date("h:i A", strtotime($row['end_time'])) ?>

            </td>

            <td>

                <a
                    href="view_attendance.php?session_id=<?= $row['id'] ?>"
                    class="primary-btn">

                    👁 View

                </a>

            </td>

        </tr>

    <?php endforeach; ?>

<?php else: ?>

<tr>

<td colspan="7" class="no-data">

📋 No Attendance Records Found

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</div>

</main>

<?php require_once '../includes/footer.php'; ?>

<script>

const searchInput = document.getElementById("searchAttendance");

const table = document.getElementById("attendanceTable");

const rows = table.getElementsByTagName("tr");

searchInput.addEventListener("keyup", function(){

    const value = this.value.toLowerCase();

    for(let i = 1; i < rows.length; i++){

        const subject = rows[i].cells[1]?.textContent.toLowerCase() || "";

        const teacher = rows[i].cells[2]?.textContent.toLowerCase() || "";

        const lecture = rows[i].cells[3]?.textContent.toLowerCase() || "";

        if(

            subject.includes(value) ||

            teacher.includes(value) ||

            lecture.includes(value)

        ){

            rows[i].style.display = "";

        }

        else{

            rows[i].style.display = "none";

        }

    }

});

</script>

</body>

</html>