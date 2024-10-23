<?php
// Start session
session_start();

// Include database connection file
@include 'config.php';

// Only process if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize form data
    $firstName = mysqli_real_escape_string($conn, trim($_POST['firstName']));
    $lastName = mysqli_real_escape_string($conn, trim($_POST['lastName']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = mysqli_real_escape_string($conn, trim($_POST['password']));
    $confirmPassword = mysqli_real_escape_string($conn, trim($_POST['confirmPassword']));
    $userType = mysqli_real_escape_string($conn, trim($_POST['userType']));

    // Validate form data
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($confirmPassword)) {
        echo "All fields are required.";
        exit();
    }

    // Validate if passwords match
    if ($password !== $confirmPassword) {
        echo "Passwords do not match. Please try again.";
        exit();
    }

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Prepare SQL query to check if the email is already registered
    $checkEmailQuery = "SELECT * FROM user_form WHERE email = ?";
    if ($stmt = $conn->prepare($checkEmailQuery)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if email already exists
        if ($result->num_rows > 0) {
            echo "An account with this email already exists. Please log in or use a different email.";
            exit();
        } else {
            // Prepare SQL query to insert the new user
            $insertQuery = "INSERT INTO user_form (first_name, last_name, email, password, user_type) VALUES (?, ?, ?, ?, ?)";
            if ($stmt = $conn->prepare($insertQuery)) {
                $stmt->bind_param("sssss", $firstName, $lastName, $email, $hashedPassword, $userType);

                if ($stmt->execute()) {
                    // Set session variable for user type
                    $_SESSION['user_name'] = $firstName; // or use another identifier
                    $_SESSION['user_type'] = $userType;

                    // Redirect to respective user or professor page
                    if ($userType === 'Student') {
                        header("Location: student_page.php");
                    } elseif ($userType === 'Professor') {
                        header("Location: professor_page.php");
                    }
                    exit();
                } else {
                    echo "Error: " . $stmt->error;
                }
            } else {
                echo "Error preparing insert statement: " . $conn->error;
            }
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error preparing check statement: " . $conn->error;
    }

    // Close the connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up - Pateros Technological College</title>
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Favicon -->
  <link href="img/logo-ptc.ico" rel="icon">

  <!-- Link for Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <!-- Link the compiled CSS file -->
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="align">
  <div class="grid align__item">
    <div class="register">
      <img src="img/logo-ptc.png" alt="Pateros Technological College Logo" class="site__logo logo-hover" style="width: 200px; height: 190px; margin-bottom: 2rem;">

      <!-- Sign-Up Form -->
      <div id="signUpFormSection" class="form-section active">
        <h2>Sign Up</h2>
        <form id="signUpForm" class="form" method="POST" action="">
          <div class="form__field">
            <i class="fas fa-user form-icon"></i>
            <input type="text" name="firstName" placeholder="First Name" required>
          </div>
          <div class="form__field">
            <i class="fas fa-user form-icon"></i>
            <input type="text" name="lastName" placeholder="Last Name" required>
          </div>
          <div class="form__field">
            <i class="fas fa-envelope form-icon"></i>
            <input type="email" name="email" placeholder="info@paterostechnologicalcollege.edu.ph" required>
          </div>
          <div class="form__field">
            <i class="fas fa-lock form-icon"></i>
            <input type="password" name="password" placeholder="Enter your password" required>
          </div>
          <div class="form__field">
            <i class="fas fa-lock form-icon"></i>
            <input type="password" name="confirmPassword" placeholder="Confirm your password" required>
          </div>
          <div class="form__field">
            <label for="userType">Select User Type:</label>
            <select name="userType" id="userType" required>
              <option value="Student">Student</option>
              <option value="Professor">Professor</option>
            </select>
          </div>
          <div class="form__field">
            <input type="submit" value="Sign Up">
          </div>
        </form>
        <p>
          <span class="text-link">Already have an account?</span>
          <a href="login_form.php">Log In</a>
        </p>
      </div>
    </div>
  </div>
</body>
</html>
