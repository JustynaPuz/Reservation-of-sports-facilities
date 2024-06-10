<?php
session_start();

$servername = "localhost";
$dbUsername = "AdamJustynaRezerwacje";
$dbPassword = "Pwr1234BazyDanych";
$dbname = "rezerwacjaObiektow";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['dataInput']) && isset($_POST['dataType'])) {
        $dataInput = $_POST['dataInput'];
        $dataType = $_POST['dataType'];
        $username = $_SESSION['username'] ?? 'default_username';  // Assumes 'username' in session, otherwise uses a default value

        if ($dataType == 'Email') {
            $columnName = 'Login';
        } elseif ($dataType == 'Numer telefonu') {
            $columnName = 'Numer telefonu';
        } else {
            $message .= 'Nieprawidłowy typ danych.';
            echo $message;
            exit; // Stops execution if the data type is invalid
        }

        if (!empty($columnName)) {
            $sql = "UPDATE `Użytkownik zalogowany` SET `$columnName` = ? WHERE `Login` = ?";
            $updateStmtData = $conn->prepare($sql);
            if ($updateStmtData === false) {
                echo "Błąd przygotowania zapytania: " . $conn->error;
                exit;
            }
            $updateStmtData->bind_param("ss", $dataInput, $username);
            if ($updateStmtData->execute()) {
                echo "$dataType został zmieniony.";
            } else {
                echo "Błąd podczas zmiany $dataType: " . $conn->error;
            }
            $updateStmtData->close();
        }
    } else {
        echo 'Nie dostarczono wszystkich wymaganych danych.';
    }
}

$conn->close();
?>