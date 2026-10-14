<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| Check if user is logged in
|--------------------------------------------------------------------------
*/

function requireLogin()
{
    if (!isset($_SESSION['role'])) {
        header("Location: /Attendance-Management-System/login.php");
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| Check user role
|--------------------------------------------------------------------------
*/

function requireRole($allowedRole)
{
    requireLogin();

    if ($_SESSION['role'] !== $allowedRole) {

        http_response_code(403);

        echo "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Access Denied</title>
            <link rel='stylesheet' href='/Attendance-Management-System/css/style.css'>
        </head>

        <body>

            <div class='container'>

                <div class='card'>

                    <h2>Access Denied</h2>

                    <p>You do not have permission to access this page.</p>

                    <br>

                    <a href='/Attendance-Management-System/login.php'>
                        Return to Login
                    </a>

                </div>

            </div>

        </body>
        </html>
        ";

        exit;
    }
}