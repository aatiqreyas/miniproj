<?php
//signup.php
include 'connect.php';
include 'header.php';
echo '<h3>Sign up</h3>';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    // Display the signup form
    echo '<form method="post" action="">
    Username: <input type="text" name="user_name" required /> 
    Password: <input type="password" name="user_pass" required> 
    Password again: <input type="password" name="user_pass_check" required> 
    E-mail: <input type="email" name="user_email" required> 
    <input type="submit" value="Sign In" /> 
    </form>';
} else {
    // Form has been posted, process the data
    $errors = array();

    // Validate username
    if (isset($_POST['user_name'])) {
        if (!ctype_alnum($_POST['user_name'])) {
            $errors[] = 'The username can only contain letters and digits.';
        }
        if (strlen($_POST['user_name']) > 30) {
            $errors[] = 'The username cannot be longer than 30 characters.';
        }
    } else {
        $errors[] = 'The username field must not be empty.';
    }

    // Validate passwords
    if (isset($_POST['user_pass'])) {
        if ($_POST['user_pass'] != $_POST['user_pass_check']) {
            $errors[] = 'The two passwords did not match.';
        }
    } else {
        $errors[] = 'The password field cannot be empty.';
    }

    // Validate email
    if (!isset($_POST['user_email']) || !filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'The email address is not valid.';
    }

    // Display errors if any
    if (!empty($errors)) {
        echo 'Uh-oh.. a couple of fields are not filled in correctly..';
        echo '<ul>';
        foreach ($errors as $key => $value) {
            echo '<li>' . $value . '</li>';
        }
        echo '</ul>';
    } else {
        // Prepare the SQL statement with placeholders
        $sql = "INSERT INTO users(user_name, user_pass, user_email, user_date, user_level) VALUES (?, ?, ?, NOW(), 0)";

        // Create a prepared statement
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            // Hash the password securely
            $hashed_password = password_hash($_POST['user_pass'], PASSWORD_DEFAULT);

            // Bind parameters to the prepared statement
            mysqli_stmt_bind_param($stmt, 'sss', $_POST['user_name'], $hashed_password, $_POST['user_email']);

            // Execute the prepared statement
            $result = mysqli_stmt_execute($stmt);
            if (!$result) {
                echo 'Something went wrong while registering. Please try again later.';
            } else {
                echo 'Successfully registered. You can now <a href="signin.php">sign in</a> and start posting! :-)';
            }
            mysqli_stmt_close($stmt);
        } else {
            echo 'Could not prepare statement: ' . mysqli_error($conn);
        }
    }
}

include 'footer.php';
?>
