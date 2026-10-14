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

$error_message = '';
$success_message = '';


// ======================================================
// GET STUDENT ID
// ======================================================

$student_id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (
    $student_id === false ||
    $student_id === null ||
    $student_id <= 0
) {

    http_response_code(400);

    $error_message = "Invalid student ID.";

}


// ======================================================
// FETCH STUDENT
// ======================================================

$student = null;

if ($error_message === '') {

    try {

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
             WHERE id = ?"
        );

        $stmt->execute([$student_id]);

        $student = $stmt->fetch();

        if (!$student) {

            http_response_code(404);

            $error_message =
                "The requested student record was not found.";

        }

    } catch (PDOException $e) {

        http_response_code(500);

        $error_message =
            "Unable to load student information.";

    }
}


// ======================================================
// UPDATE STUDENT
// ======================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['update_student']) &&
    $student !== null
) {

    // --------------------------------------------------
    // CSRF CHECK
    // --------------------------------------------------

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals(
            $_SESSION['csrf_token'],
            $_POST['csrf_token']
        )
    ) {

        $error_message =
            "Security verification failed. Please try again.";

    } else {

        // --------------------------------------------------
        // GET INPUT
        // --------------------------------------------------

        $roll_no = trim($_POST['roll_no'] ?? '');
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $gender = trim($_POST['gender'] ?? '');
        $semester = trim($_POST['semester'] ?? '');
        $new_password = $_POST['new_password'] ?? '';


        // --------------------------------------------------
        // EMPTY CHECK
        // --------------------------------------------------

        if (
            $roll_no === '' ||
            $full_name === '' ||
            $email === '' ||
            $phone === '' ||
            $gender === '' ||
            $semester === ''
        ) {

            $error_message =
                "Please fill in all required fields.";

        }


        // --------------------------------------------------
        // ROLL NUMBER
        // --------------------------------------------------

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


        // --------------------------------------------------
        // NAME
        // --------------------------------------------------

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


        // --------------------------------------------------
        // EMAIL
        // --------------------------------------------------

        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $error_message =
                "Please enter a valid email address containing @.";

        }

        elseif (strlen($email) > 150) {

            $error_message =
                "Email address is too long.";

        }


        // --------------------------------------------------
        // PHONE
        // --------------------------------------------------

        elseif (!preg_match('/^[0-9]{10}$/', $phone)) {

            $error_message =
                "Phone number must contain exactly 10 digits.";

        }


        // --------------------------------------------------
        // GENDER
        // --------------------------------------------------

        elseif (
            !in_array(
                $gender,
                ['Male', 'Female', 'Other'],
                true
            )
        ) {

            $error_message =
                "Please select a valid gender.";

        }


        // --------------------------------------------------
        // SEMESTER
        // --------------------------------------------------

        elseif (
            !in_array(
                $semester,
                [
                    'Semester 1',
                    'Semester 2',
                    'Semester 3',
                    'Semester 4',
                    'Semester 5',
                    'Semester 6'
                ],
                true
            )
        ) {

            $error_message =
                "Please select a valid semester.";

        }


        // --------------------------------------------------
        // NEW PASSWORD
        // --------------------------------------------------

        elseif (
            $new_password !== '' &&
            strlen($new_password) < 6
        ) {

            $error_message =
                "New password must contain at least 6 characters.";

        }

        elseif (
            $new_password !== '' &&
            strlen($new_password) > 100
        ) {

            $error_message =
                "New password is too long.";

        }


        // --------------------------------------------------
        // DATABASE CHECKS
        // --------------------------------------------------

        else {

            try {

                // Duplicate roll number
                $check = $pdo->prepare(
                    "SELECT id
                     FROM students
                     WHERE roll_no = ?
                     AND id != ?"
                );

                $check->execute([
                    $roll_no,
                    $student_id
                ]);

                if ($check->fetch()) {

                    $error_message =
                        "This roll number is already used by another student.";

                } else {

                    // Duplicate email
                    $check = $pdo->prepare(
                        "SELECT id
                         FROM students
                         WHERE email = ?
                         AND id != ?"
                    );

                    $check->execute([
                        $email,
                        $student_id
                    ]);

                    if ($check->fetch()) {

                        $error_message =
                            "This email address is already registered.";

                    } else {

                        // Duplicate phone
                        $check = $pdo->prepare(
                            "SELECT id
                             FROM students
                             WHERE phone = ?
                             AND id != ?"
                        );

                        $check->execute([
                            $phone,
                            $student_id
                        ]);

                        if ($check->fetch()) {

                            $error_message =
                                "This phone number is already registered.";

                        } else {

                            // --------------------------------------------------
                            // UPDATE WITH OR WITHOUT PASSWORD
                            // --------------------------------------------------

                            if ($new_password !== '') {

                                $hashed_password = password_hash(
                                    $new_password,
                                    PASSWORD_DEFAULT
                                );

                                $stmt = $pdo->prepare(
                                    "UPDATE students
                                     SET
                                        roll_no = ?,
                                        full_name = ?,
                                        email = ?,
                                        phone = ?,
                                        gender = ?,
                                        semester = ?,
                                        password = ?
                                     WHERE id = ?"
                                );

                                $stmt->execute([
                                    $roll_no,
                                    $full_name,
                                    $email,
                                    $phone,
                                    $gender,
                                    $semester,
                                    $hashed_password,
                                    $student_id
                                ]);

                            } else {

                                $stmt = $pdo->prepare(
                                    "UPDATE students
                                     SET
                                        roll_no = ?,
                                        full_name = ?,
                                        email = ?,
                                        phone = ?,
                                        gender = ?,
                                        semester = ?
                                     WHERE id = ?"
                                );

                                $stmt->execute([
                                    $roll_no,
                                    $full_name,
                                    $email,
                                    $phone,
                                    $gender,
                                    $semester,
                                    $student_id
                                ]);
                            }


                            // --------------------------------------------------
                            // SUCCESS
                            // --------------------------------------------------

                            header(
                                "Location: students.php?updated=1"
                            );

                            exit;
                        }
                    }
                }

            } catch (PDOException $e) {

                $error_message =
                    "Unable to update student information. Please try again.";
            }
        }


        // --------------------------------------------------
        // KEEP ENTERED VALUES AFTER ERROR
        // --------------------------------------------------

        $student['roll_no'] = $roll_no;
        $student['full_name'] = $full_name;
        $student['email'] = $email;
        $student['phone'] = $phone;
        $student['gender'] = $gender;
        $student['semester'] = $semester;
    }
}


// ======================================================
// PAGE
// ======================================================

require_once '../includes/header.php';
require_once '../includes/navbar.php';

?>

<main class="students-page">

    <div class="students-container">

        <!-- PAGE HEADER -->

        <section class="page-header">

            <div>

                <span class="page-label">
                    ADMIN PANEL
                </span>

                <h1>
                    Edit Student
                </h1>

                <p>
                    Update the student's information.
                </p>

            </div>

            <a
                href="students.php"
                class="secondary-btn"
            >
                ← Back to Students
            </a>

        </section>


        <!-- ERROR -->

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


        <?php if ($student !== null): ?>

            <!-- EDIT FORM -->

            <section class="student-form-card">

                <div class="section-title">

                    <h2>
                        Student Information
                    </h2>

                    <p>
                        Update the details below.
                    </p>

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


                        <!-- ROLL NUMBER -->

                        <div class="form-group">

                            <label for="roll_no">
                                Roll Number
                                <span>*</span>
                            </label>

                            <input
                                type="text"
                                id="roll_no"
                                name="roll_no"
                                value="<?php
                                echo htmlspecialchars(
                                    $student['roll_no'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>"
                                inputmode="numeric"
                                maxlength="10"
                                required
                            >

                        </div>


                        <!-- NAME -->

                        <div class="form-group">

                            <label for="full_name">
                                Full Name
                                <span>*</span>
                            </label>

                            <input
                                type="text"
                                id="full_name"
                                name="full_name"
                                value="<?php
                                echo htmlspecialchars(
                                    $student['full_name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>"
                                maxlength="100"
                                required
                            >

                        </div>


                        <!-- EMAIL -->

                        <div class="form-group">

                            <label for="email">
                                Email
                                <span>*</span>
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="<?php
                                echo htmlspecialchars(
                                    $student['email'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>"
                                maxlength="150"
                                required
                            >

                        </div>


                        <!-- PHONE -->

                        <div class="form-group">

                            <label for="phone">
                                Phone Number
                                <span>*</span>
                            </label>

                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                value="<?php
                                echo htmlspecialchars(
                                    $student['phone'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>"
                                inputmode="numeric"
                                maxlength="10"
                                pattern="[0-9]{10}"
                                required
                            >

                        </div>


                        <!-- GENDER -->

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

                                <option
                                    value="Male"
                                    <?php
                                    echo $student['gender'] === 'Male'
                                        ? 'selected'
                                        : '';
                                    ?>
                                >
                                    Male
                                </option>

                                <option
                                    value="Female"
                                    <?php
                                    echo $student['gender'] === 'Female'
                                        ? 'selected'
                                        : '';
                                    ?>
                                >
                                    Female
                                </option>

                                <option
                                    value="Other"
                                    <?php
                                    echo $student['gender'] === 'Other'
                                        ? 'selected'
                                        : '';
                                    ?>
                                >
                                    Other
                                </option>

                            </select>

                        </div>


                        <!-- SEMESTER -->

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

                                <?php

                                $semesters = [
                                    'Semester 1',
                                    'Semester 2',
                                    'Semester 3',
                                    'Semester 4',
                                    'Semester 5',
                                    'Semester 6'
                                ];

                                foreach ($semesters as $sem):

                                ?>

                                    <option
                                        value="<?php
                                        echo htmlspecialchars(
                                            $sem,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>"
                                        <?php
                                        echo $student['semester'] === $sem
                                            ? 'selected'
                                            : '';
                                        ?>
                                    >
                                        <?php
                                        echo htmlspecialchars(
                                            $sem,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- PASSWORD -->

                        <div class="form-group">

                            <label for="new_password">
                                New Password
                            </label>

                            <input
                                type="password"
                                id="new_password"
                                name="new_password"
                                placeholder="Leave blank to keep current password"
                                minlength="6"
                                maxlength="100"
                            >

                            <small>
                                Leave this empty if you don't want
                                to change the password.
                            </small>

                        </div>


                    </div>


                    <div class="form-actions">

                        <a
                            href="students.php"
                            class="secondary-btn"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            name="update_student"
                            class="primary-btn"
                        >
                            Save Changes
                        </button>

                    </div>

                </form>

            </section>

        <?php endif; ?>

    </div>

</main>


<?php

require_once '../includes/footer.php';

?>