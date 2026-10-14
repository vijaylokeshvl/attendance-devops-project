<?php

require_once '../includes/auth.php';
requireRole('Admin');

require_once '../includes/db.php';


// ======================================================
// CSRF TOKEN
// ======================================================

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION['csrf_token'];


// ======================================================
// VARIABLES
// ======================================================

$success_message = '';
$error_message = '';


// ======================================================
// HELPER FUNCTION
// ======================================================

function cleanInput($value)
{
    return trim($value);
}


// ======================================================
// DELETE STUDENT
// ======================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['delete_student'])) {

    // CSRF check
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {

        $error_message = "Security verification failed. Please try again.";

    } else {

        $student_id = filter_input(
            INPUT_POST,
            'student_id',
            FILTER_VALIDATE_INT
        );

        if ($student_id === false || $student_id === null || $student_id <= 0) {

            $error_message = "Invalid student selected.";

        } else {

            try {

                // Check student exists
                $check = $pdo->prepare(
                    "SELECT id FROM students WHERE id = ?"
                );

                $check->execute([$student_id]);

                if (!$check->fetch()) {

                    $error_message = "Student record was not found.";

                } else {

                    $stmt = $pdo->prepare(
                        "DELETE FROM students WHERE id = ?"
                    );

                    $stmt->execute([$student_id]);

                    $success_message = "Student deleted successfully.";
                }

            } catch (PDOException $e) {

                $error_message =
                    "Unable to delete the student. Please try again.";
            }
        }
    }
}


// ======================================================
// ADD STUDENT
// ======================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['add_student'])) {

    // CSRF check
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {

        $error_message = "Security verification failed. Please try again.";

    } else {

        // Get input
        $roll_no = cleanInput($_POST['roll_no'] ?? '');
        $full_name = cleanInput($_POST['full_name'] ?? '');
        $email = cleanInput($_POST['email'] ?? '');
        $phone = cleanInput($_POST['phone'] ?? '');
        $gender = cleanInput($_POST['gender'] ?? '');
        $semester = cleanInput($_POST['semester'] ?? '');
        $password = $_POST['password'] ?? '';


        // ==================================================
        // EMPTY CHECK
        // ==================================================

        if (
            $roll_no === '' ||
            $full_name === '' ||
            $email === '' ||
            $phone === '' ||
            $gender === '' ||
            $semester === '' ||
            $password === ''
        ) {

            $error_message =
                "Please fill in all required fields.";

        }


        // ==================================================
        // ROLL NUMBER VALIDATION
        // ==================================================

        elseif (!ctype_digit($roll_no)) {

            $error_message =
                "Roll number must contain numbers only.";

        }

        elseif ((int)$roll_no <= 0) {

            $error_message =
                "Roll number must be greater than 0.";

        }

        elseif (strlen($roll_no) > 10) {

            $error_message =
                "Roll number cannot be more than 10 digits.";

        }


        // ==================================================
        // NAME VALIDATION
        // ==================================================

        elseif (strlen($full_name) < 2) {

            $error_message =
                "Student name must contain at least 2 characters.";

        }

        elseif (strlen($full_name) > 100) {

            $error_message =
                "Student name cannot exceed 100 characters.";

        }

        elseif (!preg_match("/^[a-zA-Z .'-]+$/", $full_name)) {

            $error_message =
                "Student name contains invalid characters.";

        }


        // ==================================================
        // EMAIL VALIDATION
        // ==================================================

        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $error_message =
                "Please enter a valid email address containing @.";

        }

        elseif (strlen($email) > 150) {

            $error_message =
                "Email address is too long.";

        }


        // ==================================================
        // PHONE VALIDATION
        // ==================================================

        elseif (!preg_match('/^[0-9]{10}$/', $phone)) {

            $error_message =
                "Phone number must contain exactly 10 digits.";

        }


        // ==================================================
        // GENDER VALIDATION
        // ==================================================

        elseif (!in_array($gender, ['Male', 'Female', 'Other'], true)) {

            $error_message =
                "Please select a valid gender.";

        }


        // ==================================================
        // SEMESTER VALIDATION
        // ==================================================

        elseif (strlen($semester) > 30) {

            $error_message =
                "Semester value is too long.";

        }


        // ==================================================
        // PASSWORD VALIDATION
        // ==================================================

        elseif (strlen($password) < 6) {

            $error_message =
                "Password must contain at least 6 characters.";

        }

        elseif (strlen($password) > 100) {

            $error_message =
                "Password is too long.";

        }


        // ==================================================
        // DATABASE
        // ==================================================

        else {

            try {

                // Check duplicate roll number
                $check = $pdo->prepare(
                    "SELECT id FROM students WHERE roll_no = ?"
                );

                $check->execute([$roll_no]);

                if ($check->fetch()) {

                    $error_message =
                        "This roll number already exists.";

                } else {

                    // Check duplicate email
                    $check = $pdo->prepare(
                        "SELECT id FROM students WHERE email = ?"
                    );

                    $check->execute([$email]);

                    if ($check->fetch()) {

                        $error_message =
                            "This email address is already registered.";

                    } else {

                        // Check duplicate phone
                        $check = $pdo->prepare(
                            "SELECT id FROM students WHERE phone = ?"
                        );

                        $check->execute([$phone]);

                        if ($check->fetch()) {

                            $error_message =
                                "This phone number is already registered.";

                        } else {

                            // Hash password
                            $hashed_password = password_hash(
                                $password,
                                PASSWORD_DEFAULT
                            );

                            // Insert
                            $stmt = $pdo->prepare(
                                "INSERT INTO students
                                (
                                    roll_no,
                                    full_name,
                                    email,
                                    phone,
                                    gender,
                                    semester,
                                    password
                                )
                                VALUES (?, ?, ?, ?, ?, ?, ?)"
                            );

                            $stmt->execute([
                                $roll_no,
                                $full_name,
                                $email,
                                $phone,
                                $gender,
                                $semester,
                                $hashed_password
                            ]);

                            $success_message =
                                "Student added successfully.";
                        }
                    }
                }

            } catch (PDOException $e) {

                $error_message =
                    "Unable to save student information. Please try again.";
            }
        }
    }
}


// ======================================================
// SEARCH
// ======================================================

$search = cleanInput($_GET['search'] ?? '');

try {

    if ($search !== '') {

        $stmt = $pdo->prepare(
            "SELECT
                id,
                roll_no,
                full_name,
                email,
                phone,
                gender,
                semester
             FROM students
             WHERE
                roll_no LIKE ?
                OR full_name LIKE ?
                OR email LIKE ?
                OR phone LIKE ?
             ORDER BY roll_no ASC"
        );

        $search_value = '%' . $search . '%';

        $stmt->execute([
            $search_value,
            $search_value,
            $search_value,
            $search_value
        ]);

    } else {

        $stmt = $pdo->query(
            "SELECT
                id,
                roll_no,
                full_name,
                email,
                phone,
                gender,
                semester
             FROM students
             ORDER BY roll_no ASC"
        );
    }

    $students = $stmt->fetchAll();

} catch (PDOException $e) {

    $students = [];

    $error_message =
        "Unable to load student records.";
}


// ======================================================
// HTML
// ======================================================

require_once '../includes/header.php';
require_once '../includes/navbar.php';

?>

<main class="students-page">

    <div class="students-container">


        <!-- ==========================================
             PAGE HEADER
        =========================================== -->

        <section class="page-header">

            <div>

                <span class="page-label">
                    ADMIN PANEL
                </span>

                <h1>
                    Student Management
                </h1>

                <p>
                    Add, search and manage student records.
                </p>

            </div>

            <a
                href="dashboard.php"
                class="secondary-btn"
            >
                ← Dashboard
            </a>

        </section>


        <!-- ==========================================
             SUCCESS MESSAGE
        =========================================== -->

        <?php if ($success_message !== ''): ?>

            <div class="alert alert-success">

                <strong>Success:</strong>

                <?php
                echo htmlspecialchars(
                    $success_message,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>

        <?php endif; ?>


        <!-- ==========================================
             ERROR MESSAGE
        =========================================== -->

        <?php if ($error_message !== ''): ?>

            <div class="alert alert-error">

                <strong>Error:</strong>

                <?php
                echo htmlspecialchars(
                    $error_message,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>

        <?php endif; ?>


        <!-- ==========================================
             ADD STUDENT FORM
        =========================================== -->

        <section class="student-form-card">

            <div class="section-title">

                <div>

                    <h2>
                        Add New Student
                    </h2>

                    <p>
                        Enter the student's details below.
                    </p>

                </div>

            </div>


            <form
                method="POST"
                action=""
                autocomplete="off"
            >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?php
                    echo htmlspecialchars(
                        $csrf_token,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>"
                >


                <div class="form-grid">


                    <!-- Roll Number -->

                    <div class="form-group">

                        <label for="roll_no">
                            Roll Number
                            <span>*</span>
                        </label>

                        <input
                            type="text"
                            id="roll_no"
                            name="roll_no"
                            placeholder="Example: 101"
                            inputmode="numeric"
                            maxlength="10"
                            required
                        >

                    </div>


                    <!-- Full Name -->

                    <div class="form-group">

                        <label for="full_name">
                            Full Name
                            <span>*</span>
                        </label>

                        <input
                            type="text"
                            id="full_name"
                            name="full_name"
                            placeholder="Enter student's full name"
                            maxlength="100"
                            required
                        >

                    </div>


                    <!-- Email -->

                    <div class="form-group">

                        <label for="email">
                            Email
                            <span>*</span>
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="example@email.com"
                            maxlength="150"
                            required
                        >

                    </div>


                    <!-- Phone -->

                    <div class="form-group">

                        <label for="phone">
                            Phone Number
                            <span>*</span>
                        </label>

                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            placeholder="10 digit phone number"
                            inputmode="numeric"
                            maxlength="10"
                            pattern="[0-9]{10}"
                            required
                        >

                    </div>


                    <!-- Gender -->

                    <div class="form-group">

                        <label for="gender">
                            Gender
                            <span>*</span>
                        </label>

                        <select
                            id="gender"
                            name="gender"
                            required
                        >

                            <option value="">
                                Select Gender
                            </option>

                            <option value="Male">
                                Male
                            </option>

                            <option value="Female">
                                Female
                            </option>

                            <option value="Other">
                                Other
                            </option>

                        </select>

                    </div>


                    <!-- Semester -->

                    <div class="form-group">

                        <label for="semester">
                            Semester
                            <span>*</span>
                        </label>

                        <select
                            id="semester"
                            name="semester"
                            required
                        >

                            <option value="">
                                Select Semester
                            </option>

                            <option value="Semester 1">
                                Semester 1
                            </option>

                            <option value="Semester 2">
                                Semester 2
                            </option>

                            <option value="Semester 3">
                                Semester 3
                            </option>

                            <option value="Semester 4">
                                Semester 4
                            </option>

                            <option value="Semester 5">
                                Semester 5
                            </option>

                            <option value="Semester 6">
                                Semester 6
                            </option>

                        </select>

                    </div>


                    <!-- Password -->

                    <div class="form-group">

                        <label for="password">
                            Student Password
                            <span>*</span>
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Minimum 6 characters"
                            minlength="6"
                            maxlength="100"
                            required
                        >

                    </div>


                </div>


                <button
                    type="submit"
                    name="add_student"
                    class="primary-btn"
                >
                    + Add Student
                </button>

            </form>

        </section>


        <!-- ==========================================
             SEARCH
        =========================================== -->

        <section class="student-list-card">

            <div class="section-title">

                <div>

                    <h2>
                        Student Records
                    </h2>

                    <p>
                        Search and manage existing students.
                    </p>

                </div>

            </div>


            <form
                method="GET"
                action=""
                class="search-form"
            >

                <input
                    type="text"
                    name="search"
                    value="<?php
                    echo htmlspecialchars(
                        $search,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>"
                    placeholder="Search by roll number, name, email or phone..."
                >

                <button
                    type="submit"
                    class="primary-btn"
                >
                    Search
                </button>

                <?php if ($search !== ''): ?>

                    <a
                        href="students.php"
                        class="secondary-btn"
                    >
                        Clear
                    </a>

                <?php endif; ?>

            </form>


            <!-- ======================================
                 STUDENT TABLE
            ======================================= -->

            <div class="table-wrapper">

                <table class="students-table">

                    <thead>

                        <tr>

                            <th>
                                Roll No.
                            </th>

                            <th>
                                Name
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Phone
                            </th>

                            <th>
                                Gender
                            </th>

                            <th>
                                Semester
                            </th>

                            <th>
                                Actions
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if (empty($students)): ?>

                            <tr>

                                <td
                                    colspan="7"
                                    class="no-data"
                                >
                                    No student records found.
                                </td>

                            </tr>

                        <?php else: ?>

                            <?php foreach ($students as $student): ?>

                                <tr>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $student['roll_no'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $student['full_name'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $student['email'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $student['phone'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $student['gender'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $student['semester'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </td>

                                    <td>

                                        <div class="action-buttons">

                                            <a
                                                href="edit_student.php?id=<?php
                                                echo (int)$student['id'];
                                                ?>"
                                                class="edit-btn"
                                            >
                                                Edit
                                            </a>


                                            <form
                                                method="POST"
                                                action=""
                                                onsubmit="return confirm('Are you sure you want to delete this student? This action cannot be undone.');"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="csrf_token"
                                                    value="<?php
                                                    echo htmlspecialchars(
                                                        $csrf_token,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    );
                                                    ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="student_id"
                                                    value="<?php
                                                    echo (int)$student['id'];
                                                    ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    name="delete_student"
                                                    class="delete-btn"
                                                >
                                                    Delete
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>

    </div>

</main>


<?php

require_once '../includes/footer.php';

?>