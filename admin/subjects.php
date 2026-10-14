<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';

requireRole('Admin');

$stmt = $pdo->prepare("
    SELECT *
    FROM subjects
    ORDER BY subject_name ASC
");

$stmt->execute();

$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalSubjects = count($subjects);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Subjects Management</title>

<link rel="stylesheet" href="../css/style.css">

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

📚 Subjects Management

</h1>

<p>

View all available subjects in the Attendance Management System.

</p>

</div>

<a href="dashboard.php" class="dashboard-btn">

← Dashboard

</a>

</div>

<!-- ==========================
     SUBJECT COUNT
========================== -->

<div class="stats-grid">

<div class="stat-card">

<h3>Total Subjects</h3>

<h1>

<?= $totalSubjects ?>

</h1>

</div>

</div>

<!-- ==========================
     SEARCH BOX
========================== -->

<div class="student-list-card">

<div class="section-title">

<h2>

Subject List

</h2>

<p>

Search subjects instantly.

</p>

</div>

<div class="search-form">

<input
type="text"
id="searchSubject"
placeholder="🔍 Search by Subject Code or Subject Name..."
>

</div>

<div class="table-wrapper">

<table
class="students-table"
id="subjectsTable"
>

<thead>

<tr>

<th>ID</th>

<th>Subject Code</th>

<th>Subject Name</th>

<th>Created At</th>

</tr>

</thead>

<tbody>
    <?php if (!empty($subjects)): ?>

    <?php foreach ($subjects as $subject): ?>

        <tr>

            <td><?= htmlspecialchars($subject['id']) ?></td>

            <td><?= htmlspecialchars($subject['subject_code']) ?></td>

            <td><?= htmlspecialchars($subject['subject_name']) ?></td>

            <td>
                <?= date("d M Y", strtotime($subject['created_at'])) ?>
            </td>

        </tr>

    <?php endforeach; ?>

<?php else: ?>

<tr>

    <td colspan="4" class="no-data">

        📚 No Subjects Found

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

const searchInput = document.getElementById("searchSubject");

const table = document.getElementById("subjectsTable");

const rows = table.getElementsByTagName("tr");

searchInput.addEventListener("keyup", function () {

    const value = this.value.toLowerCase();

    for (let i = 1; i < rows.length; i++) {

        const code = rows[i].cells[1]?.textContent.toLowerCase() || "";

        const name = rows[i].cells[2]?.textContent.toLowerCase() || "";

        if (code.includes(value) || name.includes(value)) {

            rows[i].style.display = "";

        } else {

            rows[i].style.display = "none";

        }

    }

});

</script>

</body>

</html>