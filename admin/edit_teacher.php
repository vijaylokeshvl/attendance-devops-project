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

$error_message = '';


// ======================================================
// GET TEACHER ID
// ======================================================

$teacher_id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (
    $teacher_id === false ||
    $teacher_id === null ||
    $teacher_id <= 0
) {

    http_response_code(400);

    $error_message =
        "Invalid teacher ID.";

}


// ======================================================
// FETCH TEACHER
// ======================================================

$teacher = null;

if ($error_message === '') {

    try {

        $stmt = $pdo->prepare(
            "SELECT
                id,
                full_name,
                email,
                phone,
                username
             FROM teachers
             WHERE id = ?"
        );

        $stmt->execute([$teacher_id]);

        $teacher = $stmt->fetch();

        if (!$teacher) {

            http_response_code(404);

            $error_message =
                "The requested teacher record was not found.";
        }

    } catch (PDOException $e) {

        http_response_code(500);

        $error_message =
            "Unable to load teacher information.";
    }
}


// ======================================================
// UPDATE TEACHER
// ======================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['update_teacher']) &&
    $teacher !== null
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

        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $new_password = $_POST['new_password'] ?? '';


        // --------------------------------------------------
        // EMPTY
        // --------------------------------------------------

        if (
            $full_name === '' ||
            $email === '' ||
            $phone === '' ||
            $username === ''
        ) {

            $error_message =
                "Please fill in all required fields.";

        }


        // --------------------------------------------------
        // NAME
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
        // USERNAME
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
        // PASSWORD
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
        // DATABASE
        // --------------------------------------------------

        else {

            try {

                // Duplicate email
                $check = $pdo->prepare(
                    "SELECT id
                     FROM teachers
                     WHERE email = ?
                     AND id != ?"
                );

                $check->execute([
                    $email,
                    $teacher_id
                ]);

                if ($check->fetch()) {

                    $error_message =
                        "This email address is already used by another teacher.";

                } else {

                    // Duplicate phone
                    $check = $pdo->prepare(
                        "SELECT id
                         FROM teachers
                         WHERE phone = ?
                         AND id != ?"
                    );

                    $check->execute([
                        $phone,
                        $teacher_id
                    ]);

                    if ($check->fetch()) {

                        $error_message =
                            "This phone number is already used by another teacher.";

                    } else {

                        // Duplicate username
                        $check = $pdo->prepare(
                            "SELECT id
                             FROM teachers
                             WHERE username = ?
                             AND id != ?"
                        );

                        $check->execute([
                            $username,
                            $teacher_id
                        ]);

                        if ($check->fetch()) {

                            $error_message =
                                "This username is already used by another teacher.";

                        } else {

                            // ------------------------------------------
                            // PASSWORD CHANGED
                            // ------------------------------------------

                            if ($new_password !== '') {

                                $hashed_password = password_hash(
                                    $new_password,
                                    PASSWORD_DEFAULT
                                );

                                $stmt = $pdo->prepare(
                                    "UPDATE teachers
                                     SET
                                        full_name = ?,
                                        email = ?,
                                        phone = ?,
                                        username = ?,
                                        password = ?
                                     WHERE id = ?"
                                );

                                $stmt->execute([
                                    $full_name,
                                    $email,
                                    $phone,
                                    $username,
                                    $hashed_password,
                                    $teacher_id
                                ]);

                            } else {

                                // ------------------------------------------
                                // PASSWORD UNCHANGED
                                // ------------------------------------------

                                $stmt = $pdo->prepare(
                                    "UPDATE teachers
                                     SET
                                        full_name = ?,
                                        email = ?,
                                        phone = ?,
                                        username = ?
                                     WHERE id = ?"
                                );

                                $stmt->execute([
                                    $full_name,
                                    $email,
                                    $phone,
                                    $username,
                                    $teacher_id
                                ]);
                            }


                            // Redirect after successful update

                            header(
                                "Location: teachers.php?updated=1"
                            );

                            exit;
                        }
                    }
                }

            } catch (PDOException $e) {

                $error_message =
                    "Unable to update teacher information. Please try again.";
            }
        }


        // Keep values after error

        $teacher['full_name'] = $full_name;
        $teacher['email'] = $email;
        $teacher['phone'] = $phone;
        $teacher['username'] = $username;
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
                    Edit Teacher
                </h1>

                <p>
                    Update teacher account information.
                </p>

            </div>

            <a
                href="teachers.php"
                class="secondary-btn"
            >
                ← Back to Teachers
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


        <?php if ($teacher !== null): ?>

            <section class="student-form-card">

                <div class="section-title">

                    <h2>
                        Teacher Information
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
                                    $teacher['full_name'],
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
                                    $teacher['email'],
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
                                    $teacher['phone'],
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
                                value="<?php
                                echo htmlspecialchars(
                                    $teacher['username'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>"
                                maxlength="50"
                                required
                            >

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
                                Leave this empty if you do not want
                                to change the password.
                            </small>

                        </div>

                    </div>


                    <div class="form-actions">

                        <a
                            href="teachers.php"
                            class="secondary-btn"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            name="update_teacher"
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