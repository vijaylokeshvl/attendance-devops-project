// ===================================
// Attendance Management System
// Main JavaScript File
// ===================================

console.log("Attendance Management System Loaded Successfully");

document.addEventListener("DOMContentLoaded", function () {

    const searchInput = document.getElementById("searchStudent");
    const tableRows = document.querySelectorAll(".attendance-table tbody tr");

    const allPresentBtn = document.getElementById("allPresent");
    const allAbsentBtn = document.getElementById("allAbsent");
    const clearBtn = document.getElementById("clearAttendance");

    const saveBtn = document.getElementById("saveAttendance");

    const presentCount = document.getElementById("presentCount");
    const absentCount = document.getElementById("absentCount");

    //---------------------------------------------------
    // Search Student
    //---------------------------------------------------

    if (searchInput) {

        searchInput.addEventListener("keyup", function () {

            let value = this.value.toLowerCase();

            tableRows.forEach(function (row) {

                let text = row.innerText.toLowerCase();

                row.style.display = text.includes(value) ? "" : "none";

            });

        });

    }

    //---------------------------------------------------
    // Radio Buttons
    //---------------------------------------------------

    const radios = document.querySelectorAll("input[type='radio']");

    radios.forEach(function (radio) {

        radio.addEventListener("change", function () {

            updateCounter();

            toggleSaveButton();

            highlightRows();

        });

    });

    //---------------------------------------------------
    // Mark All Present
    //---------------------------------------------------

    if (allPresentBtn) {

        allPresentBtn.addEventListener("click", function () {

            tableRows.forEach(function (row) {

                row.querySelector("input[value='Present']").checked = true;

            });

            updateCounter();

            toggleSaveButton();

            highlightRows();

        });

    }

    //---------------------------------------------------
    // Mark All Absent
    //---------------------------------------------------

    if (allAbsentBtn) {

        allAbsentBtn.addEventListener("click", function () {

            tableRows.forEach(function (row) {

                row.querySelector("input[value='Absent']").checked = true;

            });

            updateCounter();

            toggleSaveButton();

            highlightRows();

        });

    }

    //---------------------------------------------------
    // Clear Attendance
    //---------------------------------------------------

    if (clearBtn) {

        clearBtn.addEventListener("click", function () {

            radios.forEach(function (radio) {

                radio.checked = false;

            });

            updateCounter();

            toggleSaveButton();

            highlightRows();

        });

    }

    //---------------------------------------------------
    // Update Counter
    //---------------------------------------------------

    function updateCounter() {

        let p = 0;
        let a = 0;

        tableRows.forEach(function (row) {

            if (row.querySelector("input[value='Present']").checked) {

                p++;

            }

            if (row.querySelector("input[value='Absent']").checked) {

                a++;

            }

        });

        presentCount.textContent = p;

        absentCount.textContent = a;

    }

    //---------------------------------------------------
    // Enable Save Button
    //---------------------------------------------------

    function toggleSaveButton() {

        let complete = true;

        tableRows.forEach(function (row) {

            let checked = row.querySelector("input[type='radio']:checked");

            if (!checked) {

                complete = false;

            }

        });

        saveBtn.disabled = !complete;

    }

    //---------------------------------------------------
    // Highlight Rows
    //---------------------------------------------------

    function highlightRows() {

        tableRows.forEach(function (row) {

            row.classList.remove("present-row");

            row.classList.remove("absent-row");

            if (row.querySelector("input[value='Present']").checked) {

                row.classList.add("present-row");

            }

            if (row.querySelector("input[value='Absent']").checked) {

                row.classList.add("absent-row");

            }

        });

    }

});
// ===================================
// Login Page
// ===================================

// Show / Hide Password

const togglePassword = document.getElementById("togglePassword");
const passwordInput = document.getElementById("password");

if (togglePassword && passwordInput) {

    togglePassword.addEventListener("click", function () {

        if (passwordInput.type === "password") {

            passwordInput.type = "text";

            this.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';

        } else {

            passwordInput.type = "password";

            this.innerHTML = '<i class="fa-solid fa-eye"></i>';

        }

    });

}

// Login Button Loading

const loginForm = document.getElementById("loginForm");
const loginButton = document.getElementById("loginButton");

if (loginForm && loginButton) {

    loginForm.addEventListener("submit", function () {

        loginButton.disabled = true;

        loginButton.innerHTML =
            '<i class="fa-solid fa-spinner fa-spin"></i> Signing In...';

    });

}

// Input Animation

document.querySelectorAll(".input-box input, .input-box select").forEach(function(input){

    input.addEventListener("focus", function(){

        this.parentElement.style.transform = "scale(1.02)";

    });

    input.addEventListener("blur", function(){

        this.parentElement.style.transform = "scale(1)";

    });

});
// ===================================
// Premium Attendance UI Enhancements
// ===================================

document.addEventListener("DOMContentLoaded", function () {

    const attendanceRows = document.querySelectorAll(".attendance-table tbody tr");
    const totalStudents = attendanceRows.length;

    const presentCountEl = document.getElementById("presentCount");
    const absentCountEl = document.getElementById("absentCount");
    const percentEl = document.getElementById("attendancePercent");
    const progressBar = document.getElementById("attendanceProgress");
    const saveButton = document.getElementById("saveAttendance");
    const attendanceForm = document.getElementById("attendanceForm");

    // --------------------------------
    // Update Attendance Percentage
    // --------------------------------

    function updateAttendanceUI() {

        if (!presentCountEl || !absentCountEl) return;

        const present = parseInt(presentCountEl.textContent) || 0;
        const absent = parseInt(absentCountEl.textContent) || 0;

        const percentage =
            totalStudents > 0
                ? Math.round((present / totalStudents) * 100)
                : 0;

        if (percentEl) {

            percentEl.textContent = percentage + "%";

        }

        if (progressBar) {

            progressBar.style.width = percentage + "%";

        }

    }

    // --------------------------------
    // Observe counter changes
    // --------------------------------

    if (presentCountEl && absentCountEl) {

        const observer = new MutationObserver(function () {

            updateAttendanceUI();

        });

        observer.observe(presentCountEl, {
            childList: true
        });

        observer.observe(absentCountEl, {
            childList: true
        });

    }

    // --------------------------------
    // Save Button Animation
    // --------------------------------

    if (attendanceForm && saveButton) {

        attendanceForm.addEventListener("submit", function () {

            saveButton.disabled = true;

            saveButton.innerHTML =
                '<i class="fa-solid fa-spinner fa-spin"></i> Saving Attendance...';

        });

    }

    // --------------------------------
    // Card Hover Effect
    // --------------------------------

    document.querySelectorAll(".attendance-stat-card").forEach(function(card){

        card.addEventListener("mouseenter", function(){

            this.style.transform = "translateY(-6px)";

        });

        card.addEventListener("mouseleave", function(){

            this.style.transform = "translateY(0px)";

        });

    });

    // --------------------------------
    // Search Highlight
    // --------------------------------

    const searchInput = document.getElementById("searchStudent");

    if (searchInput) {

        searchInput.addEventListener("focus", function(){

            this.parentElement.style.boxShadow =
                "0 0 0 4px rgba(13,110,253,.15)";

        });

        searchInput.addEventListener("blur", function(){

            this.parentElement.style.boxShadow = "none";

        });

    }

    // --------------------------------
    // Initial Load
    // --------------------------------

    updateAttendanceUI();

});