<?php
session_start();

$conn = require __DIR__ . "/DataBase.php";

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
$court = $_POST['court'];
$date = $_POST['date'];
$time = $_POST['time'];
$duration = intval($_POST['duration']); // w minutach

// Obliczenie czasu zakończenia
$startTime = strtotime("$date $time");
$endTime = $startTime + ($duration * 60);

// Sprawdzenie dostępności kortu na cały okres rezerwacji
$available = true;
for ($t = $startTime; $t < $endTime; $t += 3600) {
    $timestamp = date('Y-m-d H:i:s', $t);
    
    // Sprawdzenie czy termin istnieje
    $checkExistence = $conn->prepare('SELECT * FROM `Termin Kortu` WHERE `Termin` = ? AND `Numer kortu` = ?');
    $checkExistence->bind_param('si', $timestamp, $court);
    $checkExistence->execute();
    $resultExistence = $checkExistence->get_result();

    $sql_first_last = "SELECT MIN(`Termin`) AS first_term, MAX(`Termin`) AS last_term FROM `Termin Kortu`";

    $result_first_last = $conn->query($sql_first_last);
    
    if ($result_first_last === false) {
        die('Błąd wykonania zapytania: ' . $conn->error);
    }
     
    
    if ($resultExistence->num_rows === 0) {
        $available = false;
        $message = "Nie można zarezerwować terminu, termin nie istnieje lub wykracza po za możliwe ramy czasowe";
        break;
    
    }
    
    // Sprawdzenie dostępności terminu
    $checkAvailability = $conn->prepare('SELECT * FROM `Termin Kortu` WHERE `Termin` = ? AND `Numer kortu` = ? AND `Zajętość` = 1');
    $checkAvailability->bind_param('si', $timestamp, $court);
    $checkAvailability->execute();
    $resultAvailability = $checkAvailability->get_result();
    
    if ($resultAvailability->num_rows > 0) {
        $available = false;
        $message = "Niestety, wybrany slot jest już zajęty: $timestamp";
        break;
    }
}

if (!$available) {
    header("Location: tennisCourtsReservations.html?court=$court&date=$date&time=$time&message=$message");
    exit();
}

// Rezerwacja kortu na cały okres
for ($t = $startTime; $t < $endTime; $t += 3600) {
    $timestamp = date('Y-m-d H:i:s', $t);
    
    $reserveCourt = $conn->prepare('INSERT INTO `Rezerwacja kortu tenisowego` (`Termin Kortu`, Status, `Użytkownik zalogowanyID`, `KortyNumer kortu`, `czas trwania`) VALUES (?, ?, ?, ?, ?)');
    $status = 'Aktywna';
    $durationPart = min($duration, 60); // rezerwujemy na maks 60 minut jednorazowo
    $reserveCourt->bind_param('ssiii', $timestamp, $status, $userId, $court, $durationPart);
    $reserveCourt->execute();

    // Aktualizacja tabeli Termin Kortu
    $updateTerm = $conn->prepare('UPDATE `Termin Kortu` SET `Zajętość` = 1 WHERE `Termin` = ? AND `Numer kortu` = ?');
    $updateTerm->bind_param('si', $timestamp, $court);
    $updateTerm->execute();

    $duration -= 60; // zmniejsz czas trwania o jedną godzinę
}

header("Location: tennisCourtsReservations.html?court=$court&date=$date&time=$time&message=Rezerwacja została pomyślnie dokonana.");
exit();

$stmt->close();
$conn->close();
?>
