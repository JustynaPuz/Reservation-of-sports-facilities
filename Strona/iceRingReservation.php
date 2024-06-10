<?php
session_start();

$servername = "localhost";
$dbUsername = "AdamJustynaRezerwacje";
$dbPassword = "Pwr1234BazyDanych";
$dbname = "rezerwacjaObiektow";

// Nawiązanie połączenia z bazą danych
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

// Sprawdzenie połączenia
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Pobranie nazwy użytkownika z sesji
$username = $_SESSION['username'];

// Zapytanie SQL do wyciągnięcia ID użytkownika
$sql = "SELECT ID FROM `Użytkownik zalogowany` WHERE Login = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Nie znaleziono użytkownika.");
}

$userId = $user['ID'];


// Pobranie danych z formularza
$KortyNumerKortu = $_POST['court'];
$TerminKortu = $_POST['date'] . ' ' . $_POST['time'];
$duration = intval($_POST['duration']); // w minutach

// Obliczenie czasu zakończenia
$startTime = date('Y-m-d H:i:s', strtotime($TerminKortu));
$actualEndTime = date('Y-m-d H:i:s', strtotime($startTime) + 60 * 60 - 30 * 60);

// Zapytanie SQL sprawdzające dostępność
$sql = "SELECT COUNT(*) FROM `Termin Kortu` WHERE `Zajętość` = 1 AND `Termin` BETWEEN ? AND ? AND `Numer kortu` = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('MySQL prepare error: ' . $conn->error);
}
$stmt->bind_param("ssi", $startTime, $actualEndTime, $KortyNumerKortu);
$stmt->execute();
$result = $stmt->get_result();
$count = $result->fetch_array()[0];


if ($count > 0) {
    echo "Niestety, wybrany slot jest już zajęty.";
} else {
    echo "Slot jest wolny - można dokonać rezerwacji.";

    // Kod do dodania rezerwacji do bazy danych
    $insertSql = "INSERT INTO `Rezerwacja kortu tenisowego` (`Termin Kortu`, Status, `Użytkownik zalogowanyID`, `KortyNumer kortu`) VALUES (?, 'Aktywna', ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("sii", $startTime, $userId, $KortyNumerKortu);
    if ($insertStmt->execute()) {
        echo "Rezerwacja została dodana.";
        // Zmień zajętość na 1 dla odpowiednich 
        $updateSql = "UPDATE `Termin Kortu` SET `Zajętość` = 1 WHERE `Zajętość` = 0 AND `Termin` BETWEEN ? AND ? AND `Numer kortu` = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ssi", $startTime, $actualEndTime, $KortyNumerKortu);
        if ($updateStmt->execute()) {
            echo "Rezerwacja została pomyślnie dokonana.";
        } else {
            echo "Błąd podczas aktualizacji terminów: " . $conn->error;
        }
        $updateStmt->close();
    } else {
        echo "Błąd podczas dodawania rezerwacji: " . $conn->error;
    }
    $insertStmt->close();


}

$stmt->close();
$conn->close();
?>