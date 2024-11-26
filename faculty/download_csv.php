<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get POST data
    $department = $_POST['department'];
    $class_name = $_POST['class_name'];
    $section = $_POST['section'];
    $semester = $_POST['semester_no'];
    $batch = $_POST['batch'];
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'attendanceproject');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to get attendance records
    $query = "SELECT student_regno, student_name, status, date FROM dayattendance 
              WHERE department = '$department' 
              AND class_name = '$class_name' 
              AND section = '$section' 
              AND semester_no = '$semester' 
              AND batch = '$batch'
              AND date BETWEEN '$from_date' AND '$to_date'
              ORDER BY student_regno, date";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="attendance_report.csv"');

        // Open output stream
        $output = fopen('php://output', 'w');
         

    
        // Set CSV headers
        fputcsv($output, ["Department:", $department]);
    fputcsv($output, ["Class:", $class_name]);
    fputcsv($output, ["Section:", $section]);
    fputcsv($output, ["Semester:", $semester]);
    fputcsv($output, ["Batch:", $batch]);
    fputcsv($output, ["From Date:", $from_date]);
    fputcsv($output, ["To Date:", $to_date]);


        // Add date columns dynamically
        $start = strtotime($from_date);
        $end = strtotime($to_date);
        $date_columns = [];
        for ($i = $start; $i <= $end; $i += 86400) {
            $date_columns[] = date('d/m/Y', $i);
        }
        fputcsv($output, array_merge(['S.No', 'Student Reg. No', 'Student Name'], $date_columns));

        $sno = 1;
        $attendanceData = [];

        // Fetch and store attendance data
        while ($row = $result->fetch_assoc()) {
            $attendanceData[$row['student_regno']]['name'] = $row['student_name'];
            $attendanceData[$row['student_regno']]['attendance'][strtotime($row['date'])] = $row['status'];
        }

        // Write data to CSV
        foreach ($attendanceData as $regno => $data) {
            $row = [$sno++, $regno, $data['name']];
            for ($i = $start; $i <= $end; $i += 86400) {
                $status = isset($data['attendance'][$i]) ? $data['attendance'][$i] : 'N/A';
                $row[] = ($status == 'Present') ? 'P' : 'A';
            }
            fputcsv($output, $row);
        }

        // Close the output stream
        fclose($output);
        exit;
    } else {
        echo "No records found.";
    }

    // Close connection
    $conn->close();
}
?>
