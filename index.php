<?php

require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/db.php';

/* ==========================
   HOME PAGE STATISTICS
========================== */

$totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();

$totalTeachers = $pdo->query("SELECT COUNT(*) FROM teachers")->fetchColumn();

$totalSubjects = $pdo->query("SELECT COUNT(*) FROM subjects")->fetchColumn();

$totalSessions = $pdo->query("SELECT COUNT(*) FROM attendance_sessions")->fetchColumn();

?>

<main class="home-page">

<!-- ==========================
        HERO SECTION
========================== -->

<section class="hero">

<div class="hero-overlay"></div>

<div class="hero-container">

<div class="hero-content">

<span class="hero-badge">

🚀 Smart Attendance Solution

</span>

<h1>

Attendance<br>

Management System

</h1>

<p>

A modern web-based Attendance Management System developed
using PHP, MySQL, HTML, CSS and JavaScript.

Manage Students, Teachers, Subjects and Attendance
through one secure platform.

</p>

<div class="hero-buttons">

<a href="login.php" class="hero-btn">

Login Now →

</a>

<a href="#features" class="hero-btn-outline">

Explore Features

</a>

</div>

</div>

<div class="hero-image">

<div class="floating-card">

<h2>Live Statistics</h2>

<div class="mini-stat">

<span>Total Students</span>

<strong>

<?= $totalStudents ?>

</strong>

</div>

<div class="mini-stat">

<span>Total Teachers</span>

<strong>

<?= $totalTeachers ?>

</strong>

</div>

<div class="mini-stat">

<span>Total Subjects</span>

<strong>

<?= $totalSubjects ?>

</strong>

</div>

<div class="mini-stat">

<span>Attendance Sessions</span>

<strong>

<?= $totalSessions ?>

</strong>

</div>

</div>

</div>

</div>

</section>

<!-- ==========================
      QUICK STATISTICS
========================== -->

<section class="home-stats">

<div class="stats-container">

<div class="home-stat-card">

<h2>

<?= $totalStudents ?>

</h2>

<p>

Students

</p>

</div>

<div class="home-stat-card">

<h2>

<?= $totalTeachers ?>

</h2>

<p>

Teachers

</p>

</div>

<div class="home-stat-card">

<h2>

<?= $totalSubjects ?>

</h2>

<p>

Subjects

</p>

</div>

<div class="home-stat-card">

<h2>

<?= $totalSessions ?>

</h2>

<p>

Attendance Sessions

</p>

</div>

</div>

</section>

<!-- ==========================
      FEATURES SECTION
========================== -->

<section
id="features"
class="features-section">

<div class="section-title-home">

<h2>

Why Choose Our System?

</h2>

<p>

Everything required to manage attendance professionally.

</p>

</div>

<div class="features-grid">

<div class="feature-card">

<div class="feature-icon">

👨‍🎓

</div>

<h3>

Student Management

</h3>

<p>

Add, edit, search and manage all students easily.

</p>

</div>

<div class="feature-card">

<div class="feature-icon">

👨‍🏫

</div>

<h3>

Teacher Portal

</h3>

<p>

Dedicated teacher dashboard with secure login.

</p>

</div>

<div class="feature-card">

<div class="feature-icon">

📚

</div>

<h3>

Subject Management

</h3>

<p>

Organize all academic subjects in one place.

</p>

</div>

<div class="feature-card">

<div class="feature-icon">

📊

</div>

<h3>

Attendance Reports

</h3>

<p>

Generate accurate attendance reports instantly.

</p>

</div>
</div>

</section>

<!-- ==========================
        HOW IT WORKS
========================== -->

<section class="workflow-section">

<div class="section-title-home">

<h2>

How It Works

</h2>

<p>

Simple steps to manage attendance efficiently.

</p>

</div>

<div class="workflow">

<div class="workflow-card">

<div class="workflow-icon">

🔐

</div>

<h3>

Login

</h3>

<p>

Login securely as Admin, Teacher or Student.

</p>

</div>

<div class="workflow-arrow">

➜

</div>

<div class="workflow-card">

<div class="workflow-icon">

📚

</div>

<h3>

Select Subject

</h3>

<p>

Teacher selects the subject and lecture.

</p>

</div>

<div class="workflow-arrow">

➜

</div>

<div class="workflow-card">

<div class="workflow-icon">

✅

</div>

<h3>

Mark Attendance

</h3>

<p>

Mark Present or Absent for every student.

</p>

</div>

<div class="workflow-arrow">

➜

</div>

<div class="workflow-card">

<div class="workflow-icon">

📊

</div>

<h3>

Generate Reports

</h3>

<p>

View attendance reports instantly.

</p>

</div>

</div>

</section>

<!-- ==========================
        TECHNOLOGIES
========================== -->

<section id="technologies" class="tech-section">

<div class="section-title-home">

<h2>

Technologies Used

</h2>

<p>

Built using modern web technologies.

</p>

</div>

<div class="tech-grid">

<div class="tech-card">

🌐

<h3>HTML5</h3>

</div>

<div class="tech-card">

🎨

<h3>CSS3</h3>

</div>

<div class="tech-card">

⚡

<h3>JavaScript</h3>

</div>

<div class="tech-card">

🐘

<h3>PHP</h3>

</div>

<div class="tech-card">

🗄

<h3>MySQL</h3>

</div>

</div>

</section>

<!-- ==========================
        DEVELOPER
========================== -->

<section class="developer-section">

<div class="developer-card">

<h2>

Developed by

</h2>

<h1>

<a href="https://realayushsahu-ux.github.io/portfolio-v2/" >Ayush Sahu</a>
</h1>


<p>

Bachelor of Computer Applications (BCA)

</p>

<p>

Attendance Management System

Major Project

</p>

<div class="developer-line"></div>

<p>

Designed & Developed using

PHP • MySQL • HTML • CSS • JavaScript

</p>

</div>

</section>

</main>

<?php

require_once 'includes/footer.php';

?>