<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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

// Przygotowanie danych
$currentdate = $_GET['date']; // Pobierz datę z parametru GET, domyślnie pusta

$sql = "SELECT `Termin` AS date, `Numer kortu` AS courtNumber FROM `Termin Kortu` WHERE `Zajętość` = 1 AND DATE(`Termin`) = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(['error' => 'SQL prepare statement failed: ' . $conn->error]);
    exit();
}
$stmt->bind_param("s", $currentdate);
$stmt->execute();
$result = $stmt->get_result();
if ($result === false) {
    echo json_encode(['error' => 'SQL execution failed: ' . $stmt->error]);
    exit();
}

$schedule = [];

// Inicjalizacja harmonogramu z domyślnie wszystkimi kortami jako wolne
for ($i = 8; $i <= 22; $i += 0.5) {
    for ($j = 1; $j <= 4; $j++) {
        $key = 'court' . $j . '-' . sprintf('%02d:%02d', floor($i), ($i * 60) % 60);
        $schedule[$key] = '0';
    }
}

// Oznaczenie zajętych kortów
while ($row = $result->fetch_assoc()) {
    $formattedDate = date('H:i', strtotime($row['date'])); // Przekształcenie daty
    $key = 'court' . $row['courtNumber'] . '-' . $formattedDate;
    $schedule[$key] = 'Zajety';
}

// Odpowiedź JSON
echo json_encode($schedule);