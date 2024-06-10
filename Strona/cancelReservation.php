<?php
session_start();

$servername = "localhost";
$username = "AdamJustynaRezerwacje";
$password = "Pwr1234BazyDanych";
$dbname = "rezerwacjaObiektow";

// Nawiązanie połączenia z bazą danych
$conn = new mysqli($servername, $username, $password, $dbname);

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
$reservationId = isset($_GET['reservationId']) ? intval($_GET['reservationId']) : 0;

$sql = "SELECT * FROM `Rezerwacja kortu tenisowego` WHERE `ID` = ? AND `Użytkownik zalogowanyID` = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $reservationId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Nie znaleziono rezerwacji lub nie masz uprawnień do jej anulowania.";
    $stmt->close();
    $conn->close();
    exit;
}

// Usunięcie rezerwacji
$sql = "DELETE FROM `Rezerwacja kortu tenisowego`  WHERE `ID` = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $reservationId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Rezerwacja została anulowana.";
} else {
    echo "Nie udało się anulować rezerwacji.";
}

$stmt->close();
$conn->close();
?>