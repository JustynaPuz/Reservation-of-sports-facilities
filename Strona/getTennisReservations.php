<?php
session_start();

$conn = require __DIR__ . "/DataBase.php";

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