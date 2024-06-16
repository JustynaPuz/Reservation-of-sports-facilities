<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = require __DIR__ . "/DataBase.php";

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Zabezpieczenie przed SQL Injection
    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);

    $sql = "SELECT `Haslo` FROM `Użytkownik zalogowany` WHERE `Login` = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Sprawdzenie hasła
        $row = $result->fetch_assoc();
        if ($password === $row['Haslo']) {
            $_SESSION['username'] = $username;
            header("Location: indexAfterLogin.html");
            $conn->close();
            exit();
        } else {
            $_SESSION['error']['password'] = "Nieprawidłowe hasło";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error']['username'] = "Nieprawidłowy login";
        header("Location: login.php");
        exit();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="HTML, CSS">
    <meta name="description" content="Strona do rezerwacji kortów tenisowych i biletów na lodowisko">
    <title>Rezerwacja obiektów sportowych</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }

        .header {
            background-color: #007BFF;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            padding-left: 100px;
        }

        .sidebar {
            background-color: #333;
            color: white;
            width: 200px;
            height: 100vh;
            position: fixed;
            padding: 20px;
            top: 0;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 10px;
            text-decoration: none;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #555;
        }

        .main-content {
            margin-left: 220px;
            padding: 40px;
            width: calc(100% - 220px);
        }

        .form-group {
            margin-bottom: 15px;
            position: relative;
        }

        input[type="text"],
        input[type="password"] {
            width: 50%;
            padding: 8px;
            margin-top: 5px;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
        }

        button:hover {
            background-color: #0056b3;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        .error-message {
            color: red;
            font-size: 12px;
            position: absolute;
            top: 100%;
            left: 0;
        }
    </style>
    <script>
        function validateLoginForm(event) {
            event.preventDefault();
            let isValid = true;

            // Clear previous error messages
            document.querySelectorAll('.error-message').forEach(el => el.remove());

            // Validate username
            const username = document.getElementById('username');
            if (username.value.trim() === '') {
                isValid = false;
                showError(username, 'Login jest wymagany.');
            }

            // Validate password
            const password = document.getElementById('password');
            if (password.value.trim() === '') {
                isValid = false;
                showError(password, 'Hasło jest wymagane.');
            }

            if (isValid) {
                event.target.submit();
            }
        }

        function showError(input, message) {
            const error = document.createElement('div');
            error.className = 'error-message';
            error.textContent = message;
            input.parentElement.appendChild(error);
        }
    </script>
</head>

<body>
    <div class="header">
        <h1>Rezerwacja obiektów sportowych</h1>
    </div>
    <div class="sidebar">
        <a href="index.html">Strona główna</a>
        <a href="login.php" class="active">Zaloguj</a>
        <a href="register.php">Zarejestruj się</a>
    </div>

    <div class="main-content">
        <h2>Zaloguj się</h2>
        <form action="login.php" method="post" onsubmit="validateLoginForm(event)">
            <div class="form-group">
                <label for="username">Login:</label>
                <input type="text" id="username" name="username" required>
                <?php
                if (isset($_SESSION['error']['username'])) {
                    echo '<div class="error-message">' . $_SESSION['error']['username'] . '</div>';
                    unset($_SESSION['error']['username']);
                }
                ?>
            </div>
            <div class="form-group">
                <label for="password">Hasło:</label>
                <input type="password" id="password" name="password" required>
                <?php
                if (isset($_SESSION['error']['password'])) {
                    echo '<div class="error-message">' . $_SESSION['error']['password'] . '</div>';
                    unset($_SESSION['error']['password']);
                }
                ?>
            </div>
            <button type="submit">Zaloguj</button>
            <?php
            if (isset($_SESSION['error']['general'])) {
                echo '<div class="error-message" style="margin-top: 10px;">' . $_SESSION['error']['general'] . '</div>';
                unset($_SESSION['error']['general']);
            }
            ?>
        </form>
    </div>
</body>

</html>
