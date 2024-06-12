<?php
// Start session
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection details
$servername = "localhost";
$dbusername = "alfred";
$dbpassword = "Gulingen03";
$dbname = "login";

// Function to check if the username already exists
function usernameExists($conn, $username) {
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($query);
    if (!$result) {
        throw new Exception("Database Query Failed: " . $conn->error);
    }
    return $result->num_rows > 0;
}

// Function to validate password
function validatePassword($password) {
    return strlen($password) <= 8 && preg_match('/[A-Z]/', $password);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connect to the database
    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Ensure the users table has the necessary columns
    $check_column_query1 = "SHOW COLUMNS FROM users LIKE 'username'";
    $check_column_query2 = "SHOW COLUMNS FROM users LIKE 'password'";

    $result1 = $conn->query($check_column_query1);
    if ($result1->num_rows == 0) {
        $alter_query1 = "ALTER TABLE users ADD COLUMN username VARCHAR(255) NOT NULL";
        $conn->query($alter_query1);
    }

    $result2 = $conn->query($check_column_query2);
    if ($result2->num_rows == 0) {
        $alter_query2 = "ALTER TABLE users ADD COLUMN password VARCHAR(255) NOT NULL";
        $conn->query($alter_query2);
    }

    // Get and sanitize form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Validate username and password
    try {
        if (usernameExists($conn, $username)) {
            $error = "Username already exists";
        } elseif (!validatePassword($password)) {
            $error = "Password must be at most 8 characters long and contain at least one uppercase letter";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user data into the database
            $insert_query = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";
            if ($conn->query($insert_query) === TRUE) {
                // Redirect to index.html after successful registration
                header("Location: index.html");
                exit();
            } else {
                $error = "Error: " . $insert_query . "<br>" . $conn->error;
            }
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #e0f7fa;
            font-family: Arial, sans-serif;
        }
        .container {
            text-align: center;
            background-color: #ffffff;
            width: 300px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #00796b;
        }
        input[type="text"],
        input[type="password"],
        button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #00796b;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #004d40;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <?php if (isset($error)) : ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
            <div>
                <input type="text" name="username" placeholder="Brukernavn" required>
            </div>
            <div>
                <input type="password" name="password" placeholder="Passord" required>
            </div>
            <div>
                <button type="submit">Register</button>
            </div>
        </form>
    </div>
</body>
</html>
