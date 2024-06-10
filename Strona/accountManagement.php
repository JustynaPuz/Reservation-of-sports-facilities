<?php
session_start();

$servername = "localhost";
$dbUsername = "AdamJustynaRezerwacje"; // Użyj unikalnych nazw zmiennych dla połączenia z bazą
$dbPassword = "Pwr1234BazyDanych";
$dbname = "rezerwacjaObiektow";

// Nawiązanie połączenia z bazą danych
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

// Sprawdzenie połączenia
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$message = '';

// Przetwarzanie żądania POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($newPassword !== $confirmPassword) {
        $message = "Nowe hasła się nie zgadzają!";
    } else {
        $username = $_SESSION['username']; // Pobranie nazwy użytkownika z sesji
        $sql = "SELECT `Haslo` FROM `Użytkownik zalogowany` WHERE `Login` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && $oldPassword === $user['Haslo']) {

            $updateSql = "UPDATE `Użytkownik zalogowany` SET `Haslo` = ? WHERE `Login` = ?";
            $updateStmt = $conn->prepare($updateSql);
            if ($updateStmt === false) {
                die('MySQL prepare error: ' . $conn->error);
            }
            $updateStmt->bind_param("ss", $newPassword, $username);
            if ($updateStmt->execute()) {
                $message .= "Hasło zostało zmienione.";
            } else {
                $message .= "Błąd podczas zmiany hasła: " . $conn->error;
            }
            $updateStmt->close();
        } else {
            $message .= "Stare hasło jest nieprawidłowe!";
        }

        $stmt->close();
    }
}

$conn->close();
?>