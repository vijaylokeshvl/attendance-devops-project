<?php
session_start();

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>


<main class="login-page">

<section class="login-section">

<div class="login-wrapper">

    <!-- ==========================
            LEFT PANEL
    =========================== -->

    <div class="login-info">

        <div class="login-info-content">

            <span class="login-badge">

                <i class="fa-solid fa-shield-halved"></i>

                Secure Login

            </span>

            <h1>

                Attendance
                <br>
                Management
                <br>
                System

            </h1>

            <p>

                Welcome to the Attendance Management System.
                Manage attendance securely for Administrators,
                Teachers and Students through one centralized
                platform.

            </p>

            <div class="login-features">

                <div class="feature-item">

                    <i class="fa-solid fa-user-shield"></i>

                    Secure Authentication

                </div>

                <div class="feature-item">

                    <i class="fa-solid fa-chart-line"></i>

                    Attendance Reports

                </div>

                <div class="feature-item">

                    <i class="fa-solid fa-database"></i>

                    Centralized Database

                </div>

                <div class="feature-item">

                    <i class="fa-solid fa-clock"></i>

                    Real-Time Attendance

                </div>

            </div>

        </div>

    </div>

    <!-- ==========================
            LOGIN CARD
    =========================== -->

    <div class="login-card-modern">

        <div class="login-header">

            <div class="login-icon">

                <i class="fa-solid fa-user-lock"></i>

            </div>

            <h2>

                Welcome Back

            </h2>

            <p>

                Sign in to continue

            </p>

        </div>

        <?php

        if(isset($_SESSION['error']))
        {
            ?>

            <div class="login-error">

                <i class="fa-solid fa-circle-exclamation"></i>

                <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>

            </div>

            <?php
        }

        ?>

        <form
            action="login_process.php"
            method="POST"
            id="loginForm"
        >

            <div class="modern-input">

                <label>

                    Username

                </label>

                <div class="input-box">

                    <i class="fa-solid fa-user"></i>

                    <input
                        type="text"
                        name="username"
                        id="username"
                        maxlength="50"
                        placeholder="Enter your username"
                        required
                    >

                </div>

            </div>

            <div class="modern-input">

                <label>

                    Password

                </label>

                <div class="input-box password-box">

                    <i class="fa-solid fa-lock"></i>

                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Enter your password"
                        required
                    >

                    <span
                        id="togglePassword"
                        class="toggle-password"
                    >

                        <i class="fa-solid fa-eye"></i>

                    </span>

                </div>

            </div>

            <div class="modern-input">

                <label>

                    Login As

                </label>

                <div class="input-box">

                    <i class="fa-solid fa-users"></i>

                    <select
                        name="role"
                        id="role"
                        required
                    >

                        <option value="">

                            Select Role

                        </option>

                        <option value="Admin">

                            Administrator

                        </option>

                        <option value="Teacher">

                            Teacher

                        </option>

                        <option value="Student">

                            Student

                        </option>

                    </select>

                </div>

            </div>

            <button
                type="submit"
                class="login-button"
                id="loginButton"
            >

                <i class="fa-solid fa-right-to-bracket"></i>

                Login

            </button>

        </form>

    </div>

</div>

</section>

</main>

<?php
require_once 'includes/footer.php';
?>