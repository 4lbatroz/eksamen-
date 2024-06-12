<?php
// Start sesjon
session_start();

// Database tilkoblingsdetaljer
$servername = "localhost";
$dbusername = "alfred";
$dbpassword = "Gulingen03!";
$dbname = "login";

// Håndterer innsending av skjema
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Koble til databasen
    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
    if ($conn->connect_error) {
        die("Tilkobling mislyktes: " . $conn->connect_error);
    }

    // Hent og rens data fra skjemaet
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Sjekk om brukernavnet finnes
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        // Bruker funnet, verifiser passord
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Passord korrekt, start sesjon
            $_SESSION['username'] = $username;
            
    }

    // Lukk databasetilkobling
    $conn->close();
}}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        <h2>Login</h2>
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
                <button type="submit">Login</button>
            </div>
        </form>
    </div>
</body>
</html>
