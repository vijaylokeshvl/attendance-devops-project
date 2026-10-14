<?php
session_start();

require_once 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

// ----------------------
// Get & Clean Input
// ----------------------

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$role     = trim($_POST['role'] ?? '');

// ----------------------
// Validation
// ----------------------

if (empty($username) || empty($password) || empty($role)) {

    $_SESSION['error'] = "All fields are required.";

    header("Location: login.php");
    exit;
}

// ====================================================
// ADMIN LOGIN
// ====================================================

if ($role === "Admin") {

    $stmt = $pdo->prepare("
        SELECT *
        FROM users
        WHERE username = ?
        AND role = 'Admin'
        LIMIT 1
    ");

    $stmt->execute([$username]);

    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {

        session_regenerate_id(true);

        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['username'] = $admin['username'];
        $_SESSION['role'] = "Admin";

        header("Location: admin/dashboard.php");
        exit;
    }
}

// ====================================================
// TEACHER LOGIN
// ====================================================

if ($role === "Teacher") {

    $stmt = $pdo->prepare("
        SELECT *
        FROM teachers
        WHERE username = ?
        LIMIT 1
    ");

    $stmt->execute([$username]);

    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($teacher && password_verify($password, $teacher['password'])) {

        session_regenerate_id(true);

        $_SESSION['teacher_id'] = $teacher['id'];
        $_SESSION['username'] = $teacher['username'];
        $_SESSION['teacher_name'] = $teacher['full_name'];
        $_SESSION['role'] = "Teacher";

        header("Location: teacher/dashboard.php");
        exit;
    }
}

// ====================================================
// STUDENT LOGIN
// ====================================================

if ($role === "Student") {

    $stmt = $pdo->prepare("
        SELECT *
        FROM students
        WHERE roll_no = ?
        LIMIT 1
    ");

    $stmt->execute([$username]);

    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student && password_verify($password, $student['password'])) {

        session_regenerate_id(true);

        $_SESSION['student_id'] = $student['id'];
        $_SESSION['student_name'] = $student['full_name'];
        $_SESSION['role'] = "Student";

        header("Location: student/dashboard.php");
        exit;
    }
}

// ====================================================
// INVALID LOGIN
// ====================================================

$_SESSION['error'] = "Invalid username or password.";

header("Location: login.php");
exit;