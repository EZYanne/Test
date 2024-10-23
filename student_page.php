<?php
session_start();

// Include database configuration
require_once 'config.php'; // Ensure this path is correct

// Check if the user is logged in and is a student
if (!isset($_SESSION['id']) || $_SESSION['user_type'] !== 'Student') {
    // If not logged in or not a student, redirect to login page
    header("Location: login_form.php");
    exit;
}

// Define the user's id
$student_id = $_SESSION['id'];

// Prepare a SQL statement to fetch student-specific data from the user_form table
$sql = "SELECT first_name, last_name, email FROM user_form WHERE id = ? AND user_type = 'Student'";

if ($stmt = $conn->prepare($sql)) {
    // Bind parameters
    $stmt->bind_param("i", $student_id);

    // Execute the statement
    if ($stmt->execute()) {
        // Fetch the result
        $result = $stmt->get_result();

        // Check if a student record was found
        if ($result->num_rows == 1) {
            // Fetch the student data
            $student = $result->fetch_assoc();
        } else {
            // No student found with this ID
            echo "No student found.";
            exit; // Stop script execution
        }
    } else {
        // Error executing the statement
        echo "Error executing query: " . htmlspecialchars($conn->error);
        exit; // Stop script execution
    }

    // Close the statement
    $stmt->close();
} else {
    // If the query preparation failed, output the error
    echo "Error preparing statement: " . htmlspecialchars($conn->error);
    exit; // Stop script execution
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard - Pateros Technological College</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Welcome, Student <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>!</h1>
        <p>This is your student dashboard.</p>

        <!-- Add additional content here -->
        <h2>Your Courses</h2>
        <!-- Example course listing (adjust according to your logic) -->
         <h2>Resources</h2>
         <ul>
            <li><a href="course_materials.php">Course Materials</a></li>
            <li><a href="assignments.php">Assignments</a></li>
            <li><a href="grades.php">Grades</a></li>
        </ul>

        <a href="logout.php">Log Out</a>
    </div>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>
