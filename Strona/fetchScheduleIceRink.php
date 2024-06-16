<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$conn = require __DIR__ . "/DataBase.php";

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

// Get the start and end dates from the GET parameters
$startDate = $_GET['start'];
$endDate = $_GET['end'];

$sql = "SELECT `Termin` AS date, `Zajętość` AS availability FROM `Termin lodowiska` WHERE `Termin` BETWEEN ? AND ?";
$stmt = $conn->prepare($sql);


if ($stmt === false) {
    echo json_encode(['error' => 'SQL prepare statement failed: ' . $conn->error]);
    exit();
}

$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    echo json_encode(['error' => 'SQL execution failed: ' . $stmt->error]);
    exit();
}

$schedule = [];
while ($row = $result->fetch_assoc()) {
    $schedule[$row['date']] = $row['availability'];
}

$stmt->close();
$conn->close();

echo json_encode($schedule);
?>