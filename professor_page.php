<?php
session_start();

// Include database configuration
require_once 'config.php'; // Ensure this path is correct

// Check if the user is logged in and is a professor
if (!isset($_SESSION['id']) || $_SESSION['user_type'] !== 'Professor') {
    // If not logged in or not a professor, redirect to login page
    header("Location: login_form.php");
    exit;
}

// Define the user's id
$professor_id = $_SESSION['id'];

// Prepare a SQL statement to fetch professor-specific data from the user_form table
$sql = "SELECT first_name, last_name, email FROM user_form WHERE id = ? AND user_type = 'Professor'";

if ($stmt = $conn->prepare($sql)) {
    // Bind parameters
    $stmt->bind_param("i", $professor_id);

    // Execute the statement
    if ($stmt->execute()) {
        // Fetch the result
        $result = $stmt->get_result();

        // Check if a professor record was found
        if ($result->num_rows == 1) {
            // Fetch the professor data
            $professor = $result->fetch_assoc();
        } else {
            // No professor found with this ID
            echo "No professor found.";
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

// Fetch professor's courses
$professor_email = $_SESSION['email'];
$sql_courses = "SELECT course_name FROM courses WHERE professor_email = ?"; 

if ($stmt_courses = $conn->prepare($sql_courses)) {
    // Bind parameters for the courses statement
    $stmt_courses->bind_param("s", $professor_email);
    
    // Execute the courses statement
    if ($stmt_courses->execute()) {
        $result_courses = $stmt_courses->get_result();
    } else {
        // Error executing the courses statement
        echo "Error executing course query: " . htmlspecialchars($conn->error);
        exit; // Stop script execution
    }
} else {
    // If the query preparation failed, output the error
    echo "Error preparing course statement: " . htmlspecialchars($conn->error);
    exit; // Stop script execution
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Professor Dashboard - Pateros Technological College</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Welcome, Professor <?php echo htmlspecialchars($professor['first_name'] . ' ' . $professor['last_name']); ?>!</h1>
        <p>This is your professor dashboard.</p>

        <h2>Your Courses</h2>
        <ul>
            <?php
            if ($result_courses->num_rows > 0) {
                // Output data of each row
                while ($row = $result_courses->fetch_assoc()) {
                    echo "<li>" . htmlspecialchars($row['course_name']) . "</li>";
                }
            } else {
                echo "<li>No courses found.</li>";
            }
            ?>
        </ul>

        <h2>Student Management</h2>
        <ul>
            <li><a href="student_grades.php">Manage Student Grades</a></li>
            <li><a href="attendance.php">Attendance</a></li>
        </ul>

        <a href="logout.php">Log Out</a>
    </div>
</body>
</html>

<?php
// Close the statements and connection
if (isset($stmt_courses)) {
    $stmt_courses->close();
}
$conn->close();
?>
