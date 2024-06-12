<?php
// Start sesjon
session_start();

// Feilmeldinger - aktiver kun for utvikling, deaktiver for produksjon
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database tilkoblingsdetaljer
$servername = "localhost";
$dbusername = "alfred";
$dbpassword = "Gulingen03!";
$dbname = "login";

// Håndter innsending av skjema
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Koble til databasen ved hjelp av PDO
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Tilkobling mislyktes: " . $e->getMessage());
    }

    // Hent og rens data fra skjemaet
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    // Sjekk om brukernavnet finnes
    $query = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $query->bindParam(':username', $username, PDO::PARAM_STR);
    $query->execute();

    // Hvis brukernavnet finnes
    if ($query->rowCount() == 1) {
        // Hent brukerdata
        $row = $query->fetch(PDO::FETCH_ASSOC);
        // Verifiser passord
        if (password_verify($password, $row['password'])) {
            // Passord korrekt, start sesjon
            $_SESSION['username'] = $username; 
            header("Location: index.html");
            exit();
        } else {
            $error = "Feil passord.";
        } 
    } else {
        $error = "Brukernavn ikke funnet.";
    }

    // Lukk databasetilkobling
    $conn = null;
}
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

        /* Navigasjonsdiv-elementet */
.nav_flex {
    background-color: darkkhaki; /* Endrer bakgrunnsfarge til mørk khaki */
    display: flex; /* Bruker flexbox for layout */
    justify-content: space-between; /* Sprer elementene jevnt langs aksen */
    align-items: center; /* Sentrerer elementene vertikalt */
    border-radius: 10px; /* Gir boksen avrundede hjørner */
}

.produkt {
    display: flex;
    justify-content: start;
}

/* 'Norwegian Fragrances' tekst */
#tittel {
    display: flex;
    padding: 20px;
    font-size: 40px;
}

/* 'Produkt' tekst i navigasjonslinjen */
#prod {
    font-size: 27px;
    justify-content: center;
    padding: 20px;
}

.handel {
    display: flex;
    flex-direction: row;
}
#login {
    margin: 10px;
}
#user {
    width: 40px;
    height: auto;
    padding-top: 10px;
}

/* Handlevogn */
#vogn {
    justify-content: flex-end;
    height: 40px;
    width: 40px;
    padding: 20px;
    margin-right: 20px;
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
