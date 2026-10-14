<?php

require_once '../includes/auth.php';
requireRole('Admin');

require_once '../includes/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

/* ==========================
   Dashboard Statistics
========================== */

$totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();

$totalTeachers = $pdo->query("SELECT COUNT(*) FROM teachers")->fetchColumn();

$totalSubjects = $pdo->query("SELECT COUNT(*) FROM subjects")->fetchColumn();

$totalSessions = $pdo->query("SELECT COUNT(*) FROM attendance_sessions")->fetchColumn();

?>

<main class="dashboard-page">

<div class="dashboard-container">

<!-- ==========================
     Dashboard Header
========================== -->

<section class="dashboard-header">

    <div>

        <span class="dashboard-label">
            ADMIN PANEL
        </span>

        <h1>
            Admin Dashboard
        </h1>

        <p>

            Welcome back,

            <strong>

            <?= htmlspecialchars($_SESSION['username']); ?>

            </strong>

            👋

        </p>

    </div>

    <a
        href="../logout.php"
        class="logout-btn"
    >
        Logout
    </a>

</section>

<!-- ==========================
     LIVE STATISTICS
========================== -->

<section class="stats-grid">

    <div class="stat-card">

        <h3>Total Students</h3>

        <h1>

            <?= $totalStudents; ?>

        </h1>

    </div>

    <div class="stat-card">

        <h3>Total Teachers</h3>

        <h1>

            <?= $totalTeachers; ?>

        </h1>

    </div>

    <div class="stat-card">

        <h3>Total Subjects</h3>

        <h1>

            <?= $totalSubjects; ?>

        </h1>

    </div>

    <div class="stat-card">

        <h3>Attendance Sessions</h3>

        <h1>

            <?= $totalSessions; ?>

        </h1>

    </div>

</section>

<!-- ==========================
     MANAGEMENT CARDS
========================== -->

<section class="dashboard-grid">

<!-- STUDENTS CARD -->

<article class="dashboard-card">

<div class="card-icon">

👨‍🎓

</div>

<h2>

Students

</h2>

<p>

Add, edit, delete and search student records.

</p>

<a
href="students.php"
class="dashboard-btn"
>

Manage Students

<span>→</span>

</a>

</article>
<!-- ==========================
     TEACHERS CARD
========================== -->

<article class="dashboard-card">

    <div class="card-icon">
        👨‍🏫
    </div>

    <h2>
        Teachers
    </h2>

    <p>
        Add, edit, update and manage teacher accounts and login access.
    </p>

    <a
        href="teachers.php"
        class="dashboard-btn"
    >
        Manage Teachers
        <span>→</span>
    </a>

</article>


<!-- ==========================
     SUBJECTS CARD
========================== -->

<article class="dashboard-card">

    <div class="card-icon">
        📚
    </div>

    <h2>
        Subjects
    </h2>

    <p>
        View and manage all subjects available in the attendance system.
    </p>

    <a
        href="subjects.php"
        class="dashboard-btn"
    >
        Manage Subjects
        <span>→</span>
    </a>

</article>


<!-- ==========================
     ATTENDANCE CARD
========================== -->

<article class="dashboard-card">

    <div class="card-icon">
        📊
    </div>

    <h2>
        Attendance
    </h2>

    <p>
        View attendance records, attendance history and reports.
    </p>

    <a
        href="attendance.php"
        class="dashboard-btn"
    >
        View Attendance
        <span>→</span>
    </a>

</article>

</section>
</div>

</main>

<?php

require_once '../includes/footer.php';

?>