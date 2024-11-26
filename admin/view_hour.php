
<?php
session_start();
include('../config/db.php');
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

$showTable = false;
            if (isset($_POST['view_attendance'])) {
                // Capture input values
                $class_name = $_POST['class_name'];
                $section = $_POST['section'];
                $department = $_POST['department'];
                $date = $_POST['date'];
                $day_order = $_POST['day_order'];
                $semester_no = $_POST['semester_no'];

                // Fetch attendance data based on input values
                $sql = "SELECT s.student_regno, s.student_name, a.status, a.subject_name, a.subject_papercode, a.date
                        FROM attendance a
                        JOIN students s ON a.student_regno = s.student_regno
                        WHERE a.class_name = '$class_name'
                        AND a.section = '$section'
                        AND a.department = '$department'
                        AND a.date = '$date'
                        AND a.day_order = '$day_order'
                        AND a.semester_no = '$semester_no'";
                
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $showTable = true;
                    ob_start();
                    echo "<table>
                            <tr>
                                <th>S.No</th>
                                <th>Student Reg. No</th>
                                <th>Student Name</th>
                                <th>Status</th>
                                <th>Subject Name</th>
                                <th>Paper Code</th>
                                <th>Date</th>
                            </tr>";
                    
                    $sno = 1;
                    while ($row = $result->fetch_assoc()) {
                          $row_class = ($row['status'] === 'Absent') ? 'absent-row' : '';
                    echo "<tr class='{$row_class}'>
                        
                                <td>{$sno}</td>
                                <td>{$row['student_regno']}</td>
                                <td>{$row['student_name']}</td>
                                <td>{$row['status']}</td>
                                <td>{$row['subject_name']}</td>
                                <td>{$row['subject_papercode']}</td>
                                <td>{$row['date']}</td>
                            </tr>";
                        $sno++;
                    }
                    echo "</table>";
                    $attendanceTable = ob_get_clean();
                } else {
                    $attendanceTable = "<p>No attendance records found.</p>";
                }
            }
            ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Attendance</title>
    <link rel="stylesheet" href="../css/forms.css">
    <link rel="stylesheet" href="../css/topbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
     input[type="text"], select {
            width: 80%;
            padding: 10px;
            margin: 8px 0;
            display: block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
    }
    button {
            width: 30%;
            padding: 10px;
            background-color: #9b59b6;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        button:hover {
            background-color: dodgerblue;
        }

    </style>
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

    <!-- Sidebar -->
    <div class="side-content">
    <div class="sidebar">
        
        <ul>
        <li><a href="../dashboard/admin_dashboard.php"class="sidebar-link"><i class="fas fa-tachometer-alt"></i><span> Dashboard</span></a></li>
                <li><a href="../admin/manage_classes.php"class="sidebar-link"><i class="fas fa-chalkboard"></i><span> Manage Classes</span></a></li>
                <li><a href="../admin/manage_subjects.php"class="sidebar-link"><i class="fas fa-book"></i><span> Manage Subjects</span></a></li>
                <li><a href="../admin/manage_faculty.php"class="sidebar-link"><i class="fas fa-user-tie"></i><span> Manage Faculty</span></a></li>
                <li><a href="../admin/view_attendance.php"class="sidebar-link"><i class="fas fa-eye"></i><span> View Attendance</span></a></li>
                
        </ul>
    </div>

    <!-- Main content -->
    <div class="content">
        <h2>View Hour Attendance</h2>
        <form method="POST" action="">
        <div class="container">
            
                <div class="form-grid">
                    <div>
                        <label for="class_name">Class Name</label>
                        <select id="class_name" name="class_name" required>
                            <option value="">Select Class</option>
                            <?php
                            $sql = "SELECT DISTINCT class_name FROM classes";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['class_name'] . "'>" . $row['class_name'] . "</option>";
                            }
                            ?>
                        </select>

                        <label for="section">Section</label>
                        <select id="section" name="section" required>
                            <option value="">Select Section</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    
                        <label for="department">Department</label>
                        <select id="department" name="department" required>
                            <option value="">Select Department</option>
                            <?php
                            $sql = "SELECT DISTINCT department FROM classes";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['department'] . "'>" . $row['department'] . "</option>";
                            }
                            ?>
                        </select>
                        </div>
                    <div>

                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" required>

                        <label for="day_order">Day Order</label>
                        <select id="day_order" name="day_order" required>
                            <option value="">Select Day Order</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>

                        <label for="semester_no">Semester No</label>
                        <input type="number" id="semester_no" name="semester_no" required>
                    </div>
                </div>
                <button type="submit" name="view_attendance">View Attendance</button>
            </form>
            <?php if ($showTable): ?>
            <div class="attendance-table-container">
                <?php echo $attendanceTable; ?>
            </div>
        <?php endif; ?>
    </div>
    <script src="../js/sidebar.js"></script>
    <script src="../js/topbar.js"></script>
</body>
</html>
