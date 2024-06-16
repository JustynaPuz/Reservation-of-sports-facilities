<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    if (!filter_var($_POST["login"], FILTER_VALIDATE_EMAIL)) {
        $errors['login'] = "Wprowadź poprawny adres email.";
    }

    if (!preg_match("/^[0-9]{9}$/", $_POST["phoneNumber"])) {
        $errors['phoneNumber'] = "Wprowadź poprawny numer telefonu.";
    }

    // Check if passwords match
    if ($_POST["password"] !== $_POST["password2"]) {
        $errors['password2'] = "Hasła nie są takie same.";
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: register.php");
        exit();
    }

    $conn = require __DIR__ . "/DataBase.php";

    // Odbiór danych z formularza
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['login'];
    $password = $_POST['password'];
    $phone_number = $_POST['phoneNumber'];

    $name = $conn->real_escape_string($name);
    $surname = $conn->real_escape_string($surname);
    $email = $conn->real_escape_string($email);
    $password = $conn->real_escape_string($password);
    $phone_number = $conn->real_escape_string($phone_number);

    $sql_check = "SELECT * FROM `Użytkownik zalogowany` WHERE `Numer telefonu` = '$phone_number'";
    $result = $conn->query($sql_check);

    if ($result->num_rows > 0) {
        $_SESSION['errors']['phoneNumber'] = "Numer telefonu już istnieje.";
        header("Location: register.php");
        exit();
    }

    $sql_check = "SELECT * FROM `Użytkownik zalogowany` WHERE `login` = '$email'";
    $result = $conn->query($sql_check);

    if ($result->num_rows > 0) {
        $_SESSION['errors']['login'] = "Email już istnieje.";
        header("Location: register.php");
        exit();
    }

    $sql = "INSERT INTO `Użytkownik zalogowany` (`Imie`, `Nazwisko`, `Login`, `Haslo`, `Numer telefonu`) VALUES ('$name', '$surname', '$email', '$password', '$phone_number')";

    if ($conn->query($sql) === TRUE) {
        header("Location: login.php");
        $conn->close();
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
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
            top: 0;
            padding: 20px;
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
        input[type="email"],
        input[type="password"] {
            width: 25%;
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
        function validateForm(event) {
            event.preventDefault();
            let isValid = true;

            // Clear previous error messages
            document.querySelectorAll('.error-message').forEach(el => el.remove());

            // Validate name
            const name = document.getElementById('name');
            if (name.value.trim() === '') {
                isValid = false;
                showError(name, 'Imię jest wymagane.');
            }

            // Validate surname
            const surname = document.getElementById('surname');
            if (surname.value.trim() === '') {
                isValid = false;
                showError(surname, 'Nazwisko jest wymagane.');
            }

            // Validate email
            const email = document.getElementById('login');
            if (!validateEmail(email.value)) {
                isValid = false;
                showError(email, 'Wprowadź poprawny adres email.');
            }

            // Validate password
            const password = document.getElementById('password');
            const password2 = document.getElementById('password2');
            if (password.value !== password2.value) {
                isValid = false;
                showError(password2, 'Hasła nie są takie same.');
            }

            // Validate phone number
            const phoneNumber = document.getElementById('phoneNumber');
            if (!/^[0-9]{9}$/.test(phoneNumber.value)) {
                isValid = false;
                showError(phoneNumber, 'Wprowadź poprawny numer telefonu.');
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

        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
    </script>
</head>

<body>
    <div class="header">
        <h1>Rezerwacja obiektów sportowych</h1>
    </div>
    <div class="sidebar">
        <a href="index.html">Strona główna</a>
        <a href="login.php">Zaloguj</a>
        <a href="register.php" class="active">Zarejestruj się</a>
    </div>
    <div class="main-content">
        <h2>Rejestracja</h2>
        <form action="register.php" method="post" onsubmit="validateForm(event)">
            <div class="form-group">
                <label for="name">Imię:</label>
                <input type="text" id="name" name="name" required>
                <?php
                if (isset($_SESSION['errors']['name'])) {
                    echo '<div class="error-message">' . $_SESSION['errors']['name'] . '</div>';
                    unset($_SESSION['errors']['name']);
                }
                ?>
            </div>
            <div class="form-group">
                <label for="surname">Nazwisko:</label>
                <input type="text" id="surname" name="surname" required>
                <?php
                if (isset($_SESSION['errors']['surname'])) {
                    echo '<div class="error-message">' . $_SESSION['errors']['surname'] . '</div>';
                    unset($_SESSION['errors']['surname']);
                }
                ?>
            </div>
            <div class="form-group">
                <label for="login">Email (login):</label>
                <input type="email" id="login" name="login" required>
                <?php
                if (isset($_SESSION['errors']['login'])) {
                    echo '<div class="error-message">' . $_SESSION['errors']['login'] . '</div>';
                    unset($_SESSION['errors']['login']);
                }
                ?>
            </div>
            <div class="form-group">
                <label for="password">Hasło:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="password2">Powtórz hasło:</label>
                <input type="password" id="password2" name="password2" required>
                <?php
                if (isset($_SESSION['errors']['password2'])) {
                    echo '<div class="error-message">' . $_SESSION['errors']['password2'] . '</div>';
                    unset($_SESSION['errors']['password2']);
                }
                ?>
            </div>
            <div class="form-group">
                <label for="phoneNumber">Numer telefonu:</label>
                <input type="text" id="phoneNumber" name="phoneNumber" required>
                <?php
                if (isset($_SESSION['errors']['phoneNumber'])) {
                    echo '<div class="error-message">' . $_SESSION['errors']['phoneNumber'] . '</div>';
                    unset($_SESSION['errors']['phoneNumber']);
                }
                ?>
            </div>
            <button type="submit">Zarejestruj się</button>
        </form>
    </div>
</body>

</html>
