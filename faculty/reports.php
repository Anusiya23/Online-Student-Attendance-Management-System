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

$showTable = false; // Flag to show table only after form submission

// Fetch dropdown options from the database
$conn = new mysqli('localhost', 'root', '', 'attendanceproject');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch departments
$departments = [];
$result = $conn->query("SELECT DISTINCT department FROM classes");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row['department'];
    }
}

// Fetch class names
$class_names = [];
$result = $conn->query("SELECT DISTINCT class_name FROM classes");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $class_names[] = $row['class_name'];
    }
}

// Fetch sections
$sections = [];
$result = $conn->query("SELECT DISTINCT section FROM classes");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sections[] = $row['section'];
    }
}

// Fetch semester numbers (assume fixed semesters 1-8)
$semesters = range(1, 8);

// Fetch batches
$batches = [];
$result = $conn->query("SELECT DISTINCT batch FROM dayattendance");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $batches[] = $row['batch'];
    }
}

if (isset($_POST['generate_report'])) {
    // Get form inputs
    $department = $_POST['department'];
    $class_name = $_POST['class_name'];
    $section = $_POST['section'];
    $semester_no = $_POST['semester_no'];
    $batch = $_POST['batch'];
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];

    // Query to get attendance records
    $query = "SELECT student_regno, student_name, status, date FROM dayattendance 
              WHERE department = '$department' 
              AND class_name = '$class_name' 
              AND section = '$section' 
              AND semester_no = '$semester_no' 
              AND batch = '$batch'
              AND date BETWEEN '$from_date' AND '$to_date'
              ORDER BY student_regno, date";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $showTable = true;

        // Start capturing table in a buffer
        ob_start();

        echo "<table class='attendance-table'>
                <tr>
                    <th>S.No</th>
                    <th>Student Reg. No</th>
                    <th>Student Name</th>";

        // Dynamically generate date columns
        $start = strtotime($from_date);
        $end = strtotime($to_date);
        for ($i = $start; $i <= $end; $i += 86400) {
            echo "<th>" . date('d/m/Y', $i) . "</th>";
        }
        echo "</tr>";

        $sno = 1;
        $attendanceData = [];

        // Fetching attendance data
        while ($row = $result->fetch_assoc()) {
            $attendanceData[$row['student_regno']]['name'] = $row['student_name'];
            $attendanceData[$row['student_regno']]['attendance'][strtotime($row['date'])] = $row['status'];
        }

        // Display data in the table
        foreach ($attendanceData as $regno => $data) {
            echo "<tr>";
            echo "<td>$sno</td>";
            echo "<td>$regno</td>";
            echo "<td>{$data['name']}</td>";

            // Fill in attendance for each date
            for ($i = $start; $i <= $end; $i += 86400) {
                $status = isset($data['attendance'][$i]) ? $data['attendance'][$i] : 'N/A';
                $color = ($status == 'Absent') ? 'style=\"background-color:#f5c6cb;\"' : '';
                echo "<td $color>" . (($status == 'Present') ? 'P' : 'A') . "</td>";
            }

            echo "</tr>";
            $sno++;
        }

        echo "</table>";

        // Capture the table HTML
        $attendanceTable = ob_get_clean();
    } else {
        $attendanceTable = "<p>No records found for the given criteria.</p>";
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report</title>
    <link rel="stylesheet" href="../css/forms.css">
     <link rel="stylesheet" href="../css/topbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    
</head>
<style>
    input[type="submit"] {
            background-color: #9b59b6;
            border: none;
            color: white;
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s ease;
            margin-left: 400px;
            margin-top: 5px;
            width:auto;
        }

        

        input[type="submit"]:hover {
            background-color: dodgerblue;
        }

</style>
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
        <li><a href="../dashboard/faculty_dashboard.php"class="sidebar-link"><i class="fas fa-tachometer-alt"></i><span> Dashboard</span></a></li>
        <li><a href="../faculty/manage_students.php"class="sidebar-link"><i class="fas fa-user-graduate"></i><span> Manage Students</span></a></li>
        <li><a href="../faculty/mark_attendance.php"class="sidebar-link"><i class="fas fa-check-circle"></i><span> Mark Attendance</span></a></li>
        <li><a href="../faculty/view_attendance.php"class="sidebar-link"><i class="fas fa-eye"></i><span> View Attendance</span></a></li>
        <li><a href="../faculty/reports.php"class="sidebar-link"><i class="fas fa-file-alt"></i><span> Generate Reports</span></a></li>
    
        </ul>
    </div>

    <div class="content">
        <h2>Attendance Report</h2>

        <!-- Form to generate report -->
        <form method="POST" action="reports.php" class="form-grid">
            <div>
                <label for="department">Department:</label>
                <select id="department" name="department" required>
                    <option value="">Select Department</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?php echo $dept; ?>" <?php echo isset($department) && $department == $dept ? 'selected' : ''; ?>>
                            <?php echo $dept; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="class_name">Class Name:</label>
                <select id="class_name" name="class_name" required>
                    <option value="">Select Class Name</option>
                    <?php foreach ($class_names as $class): ?>
                        <option value="<?php echo $class; ?>" <?php echo isset($class_name) && $class_name == $class ? 'selected' : ''; ?>>
                            <?php echo $class; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="section">Section:</label>
                <select id="section" name="section" required>
                <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>

                <label for="semester_no">Semester:</label>
                <select id="semester_no" name="semester_no" required>
                    <option value="">Select Semester</option>
                    <?php foreach ($semesters as $sem): ?>
                        <option value="<?php echo $sem; ?>" <?php echo isset($semester_no) && $semester_no == $sem ? 'selected' : ''; ?>>
                            <?php echo $sem; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
            <label for="batch">Batch:</label>
            <input type="text" name="batch" id="batch" required>

                <label for="from_date">From Date:</label>
                <input type="date" id="from_date" name="from_date" required value="<?php echo isset($from_date) ? $from_date : ''; ?>">

                <label for="to_date">To Date:</label>
                <input type="date" id="to_date" name="to_date"  value="<?php echo isset($to_date) ? $to_date : ''; ?>">
            </div>

            <input type="submit" name="generate_report" value="Student List">
        </form>

        <!-- Display the attendance table and the download button if form is submitted -->
        <?php if ($showTable): ?>
            <div class="attendance-table-container">
            
                <?php echo $attendanceTable; ?>
                <br>
                <form action="download_csv.php" method="POST">
                    <input type="hidden" name="department" value="<?php echo $department; ?>">
                    <input type="hidden" name="class_name" value="<?php echo $class_name; ?>">
                    <input type="hidden" name="section" value="<?php echo $section; ?>">
                    <input type="hidden" name="semester_no" value="<?php echo $semester_no; ?>">
                    <input type="hidden" name="batch" value="<?php echo $batch; ?>">
                    <input type="hidden" name="from_date" value="<?php echo $from_date; ?>">
                    <input type="hidden" name="to_date" value="<?php echo $to_date; ?>">
                    <input type="submit" value="Generate Report">
                </form>
            </div>
        <?php endif; ?>
    </div>
    <script src="../js/sidebar.js"></script>
    <script src="../js/topbar.js"></script>
</body>
</html>
