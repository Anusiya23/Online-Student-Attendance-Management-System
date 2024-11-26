<?php
session_start();
include('../config/db.php');

// Check if the user is faculty
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
// When the form is submitted, display the student list
if (isset($_POST['fetch_students'])) {
    // Ensure the faculty_name is available
    if (isset($_POST['faculty_name'])) {
        $faculty_name = $_POST['faculty_name'];
    } else {
        $faculty_name = ''; // Default to empty if not set
    }

    $department = $_POST['department'];
    $class_name = $_POST['class_name'];
    $section = $_POST['section'];
    $date = $_POST['date'];
    $batch = $_POST['batch'];
    $semester_no = $_POST['semester_no'];

    // Fetch students based on class and section
    $query = $conn->prepare("SELECT student_regno, student_name FROM students WHERE class_name = ? AND section = ?");
    $query->bind_param("ss", $class_name, $section);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $showTable = true;

        // Start capturing table in a buffer
        ob_start();
        echo "<form method='POST' action='submit_day_attendance.php'>";
        echo "<table class='attendance-table'>
                <tr>
                    <th>S. No.</th>
                    <th>Register Number</th>
                    <th>Student Name</th>
                    <th>Status (Present/Absent)</th>
                </tr>";

        $serial_number = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$serial_number}</td>
                    <td>{$row['student_regno']}</td>
                    <td>{$row['student_name']}</td>
                    <td>
                        <input type='radio' name='status_{$row['student_regno']}' value='Present' required> Present
                        <input type='radio' name='status_{$row['student_regno']}' value='Absent' required> Absent
                    </td>
                </tr>";
            $serial_number++;
        }
        echo "</table>";
        // Use hidden inputs to pass faculty_name and other details to the submit page
        echo "<input type='hidden' name='faculty_name' value='$faculty_name'>";
        echo "<input type='hidden' name='department' value='$department'>";
        echo "<input type='hidden' name='class_name' value='$class_name'>";
        echo "<input type='hidden' name='section' value='$section'>";
        echo "<input type='hidden' name='date' value='$date'>";
        echo "<input type='hidden' name='batch' value='$batch'>";
        echo "<input type='hidden' name='semester_no' value='$semester_no'>";

        echo "<input type='submit' name='mark_day_attendance' value='Submit Attendance'>";

        echo "</form>";
        $attendanceTable = ob_get_clean();
    } else {
        $attendanceTable = "No students found for the selected class and section.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Day Attendance</title>
    <link rel="stylesheet" href="../css/topbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        /* Add your CSS styling for the page */
        /* Reuse your existing CSS */
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

.sidebar ul {
    list-style: none;
    padding: 0;
}

.sidebar ul li {
    padding: 15px 0;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.sidebar ul li a {
    color: white;
    text-decoration: none;
    display: flex;
    align-items: center;
    transition: color 0.3s ease;
}
  .sidebar ul li a i {
            margin-right: 10px;
        }

        .sidebar ul li:hover {
            background-color: #8e44ad;
        }

        /* Active class styling */
        .sidebar ul li.active {
            background-color: #6c3483;
            font-weight: bold;
        }

        .sidebar ul li.active a {
            color: #eef2f6;
        }
        .content {
            padding: 50px;
            flex-grow: 1;
            font-weight: bold;
            margin-left: 250px; /* Ensure content is shifted to the right of the sidebar */
            transition: margin-left 0.3s;
            font-size:20px;
        }

        .hidden-sidebar {
            width: 60px; /* Set width for hidden sidebar (to display only icons) */
            overflow: hidden;
        }

        .hidden-sidebar .sidebar-link span {
            display: none; /* Hide text when sidebar is collapsed */
        }

        .hidden-sidebar + .content {
            margin-left: 60px; /* Adjust content width when sidebar is hidden */
        }
h2 {
    color: #333;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

select, input[type="text"], input[type="date"], input[type="number"] {
    width: 80%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

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
}

input[type="submit"]:hover {
    background-color: dodgerblue;
}

/* CSS for attendance table */
.attendance-table {
    margin-top:30px;
    margin-bottom:30px;
    margin-left: 50px;
    border-collapse: collapse;
    width: 90%;
    align-items:center;
    position:relative;
}

.attendance-table th, .attendance-table td {
    border: 2px solid #333;
    padding: 7px 5px;
    text-align: center;
}

.attendance-table th {
    background-color: #9b59b6;
    color:white;
    padding: 10px ;
}
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
}
.message-container {
    margin-right : 10px; /* Space between the form and the message */
    text-align:end;
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
        <li><a href="../dashboard/faculty_dashboard.php"class="sidebar-link"><i class="fas fa-tachometer-alt"></i><span> Dashboard</span></a></li>
    <li><a href="../faculty/manage_students.php"class="sidebar-link"><i class="fas fa-user-graduate"></i><span> Manage Students</span></a></li>
    <li><a href="../faculty/mark_attendance.php"class="sidebar-link"><i class="fas fa-check-circle"></i><span> Mark Attendance</span></a></li>
    <li><a href="../faculty/view_attendance.php"class="sidebar-link"><i class="fas fa-eye"></i><span> View Attendance</span></a></li>
    <li><a href="../faculty/reports.php" class="sidebar-link"><i class="fas fa-file-alt"></i><span> Generate Reports</span></a></li>
    
    </div>
<div class="content">
    <h2>Day Attendance</h2>
    <form method="POST" action="">
        <div class="form-grid">
            <div>
                <label for="faculty_name">Incharge Name:</label>
                <input type="text" name="faculty_name" id="faculty_name" value="<?php echo isset($_POST['faculty_name']) ? $_POST['faculty_name'] : ''; ?>" required>

                <label for="department">Department:</label>
                <select name="department" id="department" required>
                    <option value="">Select Department</option>
                    <?php
                    // Populate class names from database
                    $class_query = $conn->query("SELECT DISTINCT department FROM classes");
                    while ($class_row = $class_query->fetch_assoc()) {
                        $selected = (isset($_POST['department']) && $_POST['department'] === $class_row['department']) ? 'selected' : '';
                        echo "<option value='{$class_row['department']}' $selected>{$class_row['department']}</option>";
                    }
                    ?>
                </select>

                <label for="class_name">Class Name:</label>
                <select name="class_name" id="class_name" required>
                    <option value="">Select Class</option>
                    <?php
                    // Example query to get class names
                    $class_query = $conn->query("SELECT DISTINCT class_name FROM classes");
                    while ($class_row = $class_query->fetch_assoc()) {
                        $selected = (isset($_POST['class_name']) && $_POST['class_name'] === $class_row['class_name']) ? 'selected' : '';
                        echo "<option value='{$class_row['class_name']}' $selected>{$class_row['class_name']}</option>";
                    }
                    ?>
                </select>

                <label for="section">Section:</label>
                <select name="section" id="section" required>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>

            <div>
                <label for="date">Date:</label>
                <input type="date" name="date" id="date" required>

                <label for="batch">Batch:</label>
                <input type="text" name="batch" id="batch" required>

                <label for="semester_no">Semester Number:</label>
                <input type="number" name="semester_no" id="semester_no" required>
            </div>
        </div>

        <input type="submit" name="fetch_students" value="Student List">
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
