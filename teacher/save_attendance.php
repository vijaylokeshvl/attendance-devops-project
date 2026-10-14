<?php
session_start();

require_once '../includes/auth.php';
require_once '../includes/db.php';

requireRole('Teacher');

// -------------------------------------
// Allow POST Requests Only
// -------------------------------------

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: mark_attendance.php");
    exit;
}

// -------------------------------------
// Get Teacher ID
// -------------------------------------

$teacherId = $_SESSION['teacher_id'];

// -------------------------------------
// Get Form Data
// -------------------------------------

$subjectId    = trim($_POST['subject'] ?? '');
$lectureName  = trim($_POST['lecture_name'] ?? '');
$startTime    = trim($_POST['start_time'] ?? '');
$endTime      = trim($_POST['end_time'] ?? '');
$attendance   = $_POST['attendance'] ?? [];

// Today's Date

$lectureDate = date("Y-m-d");

// -------------------------------------
// Validation
// -------------------------------------

if (
    empty($subjectId) ||
    empty($lectureName) ||
    empty($startTime) ||
    empty($endTime)
) {

    $_SESSION['error'] = "Please fill all required fields.";

    header("Location: mark_attendance.php");
    exit;
}

// Lecture Name Length

if (strlen($lectureName) > 100) {

    $_SESSION['error'] = "Lecture name is too long.";

    header("Location: mark_attendance.php");
    exit;
}

// Attendance Selected

if (empty($attendance)) {

    $_SESSION['error'] = "Please mark attendance.";

    header("Location: mark_attendance.php");
    exit;
}

// Check Every Student Has Attendance

foreach ($attendance as $studentId => $status) {

    if ($status !== "Present" && $status !== "Absent") {

        $_SESSION['error'] = "Invalid attendance selection.";

        header("Location: mark_attendance.php");
        exit;
    }

}

// -------------------------------------
// Start Database Transaction
// -------------------------------------

try {

    $pdo->beginTransaction();

    // ---------------------------------
    // Insert Attendance Session
    // ---------------------------------

    $sessionQuery = $pdo->prepare("

        INSERT INTO attendance_sessions
        (
            subject_id,
            teacher_id,
            lecture_name,
            lecture_date,
            start_time,
            end_time
        )

        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
        )

    ");

    $sessionQuery->execute([

        $subjectId,
        $teacherId,
        $lectureName,
        $lectureDate,
        $startTime,
        $endTime

    ]);

    // Get Session ID

    $sessionId = $pdo->lastInsertId();

    // ---------------------------------
    // Save Attendance of Every Student
    // ---------------------------------

    $attendanceQuery = $pdo->prepare("
        INSERT INTO attendance
        (
            session_id,
            student_id,
            status
        )
        VALUES
        (
            ?,
            ?,
            ?
        )
    ");

    foreach ($attendance as $studentId => $status) {

        $attendanceQuery->execute([
            $sessionId,
            $studentId,
            $status
        ]);

    }

    // ---------------------------------
    // Commit Transaction
    // ---------------------------------

    $pdo->commit();

    $_SESSION['success'] = "Attendance saved successfully.";

} catch (Exception $e) {

    $pdo->rollBack();

    $_SESSION['error'] = "Failed to save attendance.";

}

header("Location: mark_attendance.php");
exit;