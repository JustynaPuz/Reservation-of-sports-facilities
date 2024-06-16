<?php
session_start();

$conn = require __DIR__ . "/DataBase.php";

// Pozostała część kodu...

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