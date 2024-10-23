<?php
session_start(); // Start the session

// Include database configuration
require_once 'config.php'; // Ensure this path is correct

// Define variables and initialize with empty values
$email = $password = "";
$email_err = $password_err = "";

// Process the form when submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Check for errors before proceeding with login
    if (empty($email_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT id, password, user_type FROM user_form WHERE email = ?";

        // Prepare the statement
        if ($stmt = $conn->prepare($sql)) {
            // Bind the email parameter
            $stmt->bind_param("s", $email);

            // Execute the query
            $stmt->execute();

            // Store the result
            $stmt->store_result();

            // Check if email exists, if yes then verify the password
            if ($stmt->num_rows == 1) {
                // Bind result variables
                $stmt->bind_result($id, $hashedPassword, $userType);

                if ($stmt->fetch()) {
                    // Verify the password
                    if (password_verify($password, $hashedPassword)) {
                        // Start a new session and store user information
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id; // Store user ID in session
                        $_SESSION["email"] = $email; // Store user email in session
                        $_SESSION["user_type"] = $userType; // Store user type in session

                        // Redirect to respective user page based on user type
                        if ($userType === 'Student') {
                            header("Location: student_page.php");
                        } elseif ($userType === 'Professor') {
                            header("Location: professor_page.php");
                        }
                        exit; // Terminate script after redirection
                    } else {
                        // Invalid password
                        $password_err = "Invalid email or password.";
                    }
                }
            } else {
                // Email doesn't exist
                $password_err = "Invalid email or password.";
            }

            // Close the statement
            $stmt->close();
        } else {
            // Error preparing statement
            echo "Error preparing statement: " . $conn->error;
        }
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Pateros Technological College</title>
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

      <!-- Login Form -->
      <div id="loginFormSection" class="form-section active">
        <h2>Log In</h2>
        <form id="loginForm" class="form" method="POST" action="">
          <div class="form__field">
            <i class="fas fa-envelope form-icon"></i>
            <input type="email" name="email" placeholder="info@paterostechnologicalcollege.edu.ph" required>
            <span class="error-message"><?php echo $email_err; ?></span> <!-- Error message for email -->
          </div>
          <div class="form__field">
            <i class="fas fa-lock form-icon"></i>
            <input type="password" name="password" placeholder="Enter your password" required>
            <span class="error-message"><?php echo $password_err; ?></span> <!-- Error message for password -->
          </div>
          <div class="form__field">
            <input type="submit" value="Log In">
          </div>
        </form>
        <p>
          <span class="text-link">Don't have an account?</span>
          <a href="register_form.php">Sign Up</a>
        </p>
      </div>
    </div>
  </div>
</body>
</html>
