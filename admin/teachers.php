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
// DELETE TEACHER
// ======================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['delete_teacher'])
) {

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

        $teacher_id = filter_input(
            INPUT_POST,
            'teacher_id',
            FILTER_VALIDATE_INT
        );

        if (
            $teacher_id === false ||
            $teacher_id === null ||
            $teacher_id <= 0
        ) {

            $error_message =
                "Invalid teacher selected.";

        } else {

            try {

                // Check teacher exists
                $check = $pdo->prepare(
                    "SELECT id FROM teachers WHERE id = ?"
                );

                $check->execute([$teacher_id]);

                if (!$check->fetch()) {

                    $error_message =
                        "Teacher record was not found.";

                } else {

                    $stmt = $pdo->prepare(
                        "DELETE FROM teachers WHERE id = ?"
                    );

                    $stmt->execute([$teacher_id]);

                    $success_message =
                        "Teacher deleted successfully.";
                }

            } catch (PDOException $e) {

                $error_message =
                    "Unable to delete teacher. Please try again.";
            }
        }
    }
}


// ======================================================
// ADD TEACHER
// ======================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['add_teacher'])
) {

    // --------------------------------------------------
    // CSRF
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
        // INPUT
        // --------------------------------------------------

        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';


        // --------------------------------------------------
        // EMPTY CHECK
        // --------------------------------------------------

        if (
            $full_name === '' ||
            $email === '' ||
            $phone === '' ||
            $username === '' ||
            $password === ''
        ) {

            $error_message =
                "Please fill in all required fields.";

        }


        // --------------------------------------------------
        // NAME VALIDATION
        // --------------------------------------------------

        elseif (strlen($full_name) < 2) {

            $error_message =
                "Teacher name must contain at least 2 characters.";

        }

        elseif (strlen($full_name) > 100) {

            $error_message =
                "Teacher name cannot exceed 100 characters.";

        }

        elseif (!preg_match("/^[a-zA-Z .'-]+$/", $full_name)) {

            $error_message =
                "Teacher name contains invalid characters.";

        }


        // --------------------------------------------------
        // EMAIL VALIDATION
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
        // PHONE VALIDATION
        // --------------------------------------------------

        elseif (!preg_match('/^[0-9]{10}$/', $phone)) {

            $error_message =
                "Phone number must contain exactly 10 digits.";

        }


        // --------------------------------------------------
        // USERNAME VALIDATION
        // --------------------------------------------------

        elseif (strlen($username) < 4) {

            $error_message =
                "Username must contain at least 4 characters.";

        }

        elseif (strlen($username) > 50) {

            $error_message =
                "Username cannot exceed 50 characters.";

        }

        elseif (!preg_match('/^[a-zA-Z0-9_.]+$/', $username)) {

            $error_message =
                "Username can contain only letters, numbers, underscore and dot.";

        }


        // --------------------------------------------------
        // PASSWORD VALIDATION
        // --------------------------------------------------

        elseif (strlen($password) < 6) {

            $error_message =
                "Password must contain at least 6 characters.";

        }

        elseif (strlen($password) > 100) {

            $error_message =
                "Password is too long.";

        }


        // --------------------------------------------------
        // DATABASE
        // --------------------------------------------------

        else {

            try {

                // Duplicate email
                $check = $pdo->prepare(
                    "SELECT id
                     FROM teachers
                     WHERE email = ?"
                );

                $check->execute([$email]);

                if ($check->fetch()) {

                    $error_message =
                        "This email address is already registered.";

                } else {

                    // Duplicate phone
                    $check = $pdo->prepare(
                        "SELECT id
                         FROM teachers
                         WHERE phone = ?"
                    );

                    $check->execute([$phone]);

                    if ($check->fetch()) {

                        $error_message =
                            "This phone number is already registered.";

                    } else {

                        // Duplicate username
                        $check = $pdo->prepare(
                            "SELECT id
                             FROM teachers
                             WHERE username = ?"
                        );

                        $check->execute([$username]);

                        if ($check->fetch()) {

                            $error_message =
                                "This username is already taken.";

                        } else {

                            $hashed_password = password_hash(
                                $password,
                                PASSWORD_DEFAULT
                            );

                            $stmt = $pdo->prepare(
                                "INSERT INTO teachers
                                (
                                    full_name,
                                    email,
                                    phone,
                                    username,
                                    password
                                )
                                VALUES (?, ?, ?, ?, ?)"
                            );

                            $stmt->execute([
                                $full_name,
                                $email,
                                $phone,
                                $username,
                                $hashed_password
                            ]);

                            $success_message =
                                "Teacher added successfully.";
                        }
                    }
                }

            } catch (PDOException $e) {

                $error_message =
                    "Unable to save teacher information. Please try again.";
            }
        }
    }
}


// ======================================================
// SEARCH
// ======================================================

$search = trim($_GET['search'] ?? '');

try {

    if ($search !== '') {

        $stmt = $pdo->prepare(
            "SELECT
                id,
                full_name,
                email,
                phone,
                username
             FROM teachers
             WHERE
                full_name LIKE ?
                OR email LIKE ?
                OR phone LIKE ?
                OR username LIKE ?
             ORDER BY full_name ASC"
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
                full_name,
                email,
                phone,
                username
             FROM teachers
             ORDER BY full_name ASC"
        );
    }

    $teachers = $stmt->fetchAll();

} catch (PDOException $e) {

    $teachers = [];

    $error_message =
        "Unable to load teacher records.";
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
                    Teacher Management
                </h1>

                <p>
                    Add, search and manage teacher accounts.
                </p>

            </div>

            <a
                href="dashboard.php"
                class="secondary-btn"
            >
                ← Dashboard
            </a>

        </section>


        <!-- SUCCESS -->

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


        <!-- ADD TEACHER -->

        <section class="student-form-card">

            <div class="section-title">

                <h2>
                    Add New Teacher
                </h2>

                <p>
                    Create a teacher account.
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
                            placeholder="Enter teacher's full name"
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
                            placeholder="teacher@example.com"
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
                            placeholder="10 digit phone number"
                            inputmode="numeric"
                            maxlength="10"
                            pattern="[0-9]{10}"
                            required
                        >

                    </div>


                    <!-- USERNAME -->

                    <div class="form-group">

                        <label for="username">
                            Username
                            <span>*</span>
                        </label>

                        <input
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Teacher username"
                            maxlength="50"
                            required
                        >

                    </div>


                    <!-- PASSWORD -->

                    <div class="form-group">

                        <label for="password">
                            Password
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
                    name="add_teacher"
                    class="primary-btn"
                >
                    + Add Teacher
                </button>

            </form>

        </section>


        <!-- TEACHER LIST -->

        <section class="student-list-card">

            <div class="section-title">

                <h2>
                    Teacher Records
                </h2>

                <p>
                    Search and manage existing teachers.
                </p>

            </div>


            <!-- SEARCH -->

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
                    placeholder="Search by name, email, phone or username..."
                >

                <button
                    type="submit"
                    class="primary-btn"
                >
                    Search
                </button>

                <?php if ($search !== ''): ?>

                    <a
                        href="teachers.php"
                        class="secondary-btn"
                    >
                        Clear
                    </a>

                <?php endif; ?>

            </form>


            <!-- TABLE -->

            <div class="table-wrapper">

                <table class="students-table">

                    <thead>

                        <tr>

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
                                Username
                            </th>

                            <th>
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php if (empty($teachers)): ?>

                            <tr>

                                <td
                                    colspan="5"
                                    class="no-data"
                                >
                                    No teacher records found.
                                </td>

                            </tr>

                        <?php else: ?>

                            <?php foreach ($teachers as $teacher): ?>

                                <tr>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $teacher['full_name'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $teacher['email'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $teacher['phone'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $teacher['username'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </td>

                                    <td>

                                        <div class="action-buttons">


                                            <!-- EDIT -->

                                            <a
                                                href="edit_teacher.php?id=<?php
                                                echo (int)$teacher['id'];
                                                ?>"
                                                class="edit-btn"
                                            >
                                                Edit
                                            </a>


                                            <!-- DELETE -->

                                            <form
                                                method="POST"
                                                action=""
                                                onsubmit="return confirm('Are you sure you want to delete this teacher? This action cannot be undone.');"
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
                                                    name="teacher_id"
                                                    value="<?php
                                                    echo (int)$teacher['id'];
                                                    ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    name="delete_teacher"
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