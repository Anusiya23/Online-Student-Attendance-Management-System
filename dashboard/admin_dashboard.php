<?php
session_start();
include('../config/db.php');

// Check if admin is logged in

if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] !== 'admin' && $_SESSION['user_type'] !== 'faculty' && $_SESSION['user_type'] !== 'student')) {
    header('Location: ../login.php');
    exit();
}

// Fetch the logged-in user's username from the session
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Use the session-stored username
} else {
    $username = 'Guest'; // Fallback in case the username isn't set
}
 

// Fetch summary statistics for dashboard
$student_count = $conn->query("SELECT COUNT(*) AS count FROM students")->fetch_assoc()['count'];
$faculty_count = $conn->query("SELECT COUNT(*) AS count FROM faculty")->fetch_assoc()['count'];
$class_count = $conn->query("SELECT COUNT(*) AS count FROM classes")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/topbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    
</head>
<body>

    <div class="topbar">
        <div class="topbar-left">
            <span class="menu-toggle"><i class="fas fa-bars"></i></span>
            <h1>OSAMS</h1>
        </div>
        <div class="topbar-right">
            <i class="fas fa-user-circle"></i>
            <span>Welcome, <?php echo $username; ?></span>
            <div class="welcome-dropdown">
                <a href="../login/login.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>

    <div class="side-content">
        <div class="sidebar">
            <ul>
                <li><a href="../dashboard/admin_dashboard.php" class="sidebar-link"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
                <li><a href="../admin/manage_classes.php" class="sidebar-link"><i class="fas fa-chalkboard"></i> <span>Manage Classes</span></a></li>
                <li><a href="../admin/manage_subjects.php" class="sidebar-link"><i class="fas fa-book"></i> <span>Manage Subjects</span></a></li>
                <li><a href="../admin/manage_faculty.php" class="sidebar-link"><i class="fas fa-user-tie"></i> <span>Manage Faculty</span></a></li>
                <li><a href="../admin/view_attendance.php" class="sidebar-link"><i class="fas fa-eye"></i> <span>View Attendance</span></a></li>
                
            </ul>
        </div>
        <div class="content">
            <h2>Administrator Dashboard</h2>
            <div class="dashboard-container">
                <div class="dashboard-card">
                    <i class="fas fa-user-graduate"></i>
                    <h3>Students</h3>
                    <p><?php echo $student_count; ?></p>
                </div>
                <div class="dashboard-card">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <h3>Faculty</h3>
                    <p><?php echo $faculty_count; ?></p>
                </div>
                <div class="dashboard-card">
                    <i class="fas fa-school"></i>
                    <h3>Classes</h3>
                    <p><?php echo $class_count; ?></p>
                </div>
            </div>
        </div>
    </div>
    <script src="../js/sidebar.js"></script>
    <script src="../js/topbar.js"></script>
</body>
</html>
