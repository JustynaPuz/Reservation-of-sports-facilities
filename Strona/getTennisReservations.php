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


// Pobierz datę z zapytania GET
$date = $_GET['date'];

$sql = "SELECT `Termin`, `Numer kortu` FROM `Termin Kortu` WHERE `Zajętość` = 1 AND DATE(`Termin`) = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$reservations = [];
while ($row = $result->fetch_assoc()) {
    $reservations[] = $row;
}

echo json_encode($reservations);

$conn->close();
?>