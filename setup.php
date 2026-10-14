<?php

require_once __DIR__ . '/includes/db.php';

// =========================
// Create Admin
// =========================

$check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$check->execute(['admin']);

if (!$check->fetch()) {

    $stmt = $pdo->prepare("
        INSERT INTO users (username,password,role)
        VALUES (?,?,?)
    ");

    $stmt->execute([
        'admin',
        password_hash('admin123', PASSWORD_DEFAULT),
        'Admin'
    ]);
}

// =========================
// Create Teacher
// =========================

$check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$check->execute(['teacher']);

if (!$check->fetch()) {

    $stmt = $pdo->prepare("
        INSERT INTO users (username,password,role)
        VALUES (?,?,?)
    ");

    $stmt->execute([
        'teacher',
        password_hash('teacher123', PASSWORD_DEFAULT),
        'Teacher'
    ]);
}

// =========================
// Create Student
// =========================

$check = $pdo->prepare("SELECT id FROM students WHERE roll_no = ?");
$check->execute([1]);

if (!$check->fetch()) {

    $stmt = $pdo->prepare("
        INSERT INTO students
        (roll_no, full_name, email, phone, gender, semester, password)
        VALUES (?,?,?,?,?,?,?)
    ");

    $stmt->execute([
        1,
        'Demo Student',
        'student@test.com',
        '9876543210',
        'Male',
        'Semester 1',
        password_hash('student123', PASSWORD_DEFAULT)
    ]);
}

echo "<h2>✅ Demo Accounts Created Successfully</h2>";

echo "<hr>";

echo "<b>Admin</b><br>";
echo "Username: admin<br>";
echo "Password: admin123<br><br>";

echo "<b>Teacher</b><br>";
echo "Username: teacher<br>";
echo "Password: teacher123<br><br>";

echo "<b>Student</b><br>";
echo "Username: 1<br>";
echo "Password: student123";