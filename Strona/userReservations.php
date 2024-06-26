<?php
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

// Pobieranie typu rezerwacji z formularza
$reservationType = isset($_POST['reservationType']) ? $_POST['reservationType'] : null;

// Definiowanie zapytań dla różnych typów rezerwacji
if ($reservationType == 'tennis') {
    $query = "SELECT * FROM `Rezerwacja kortu tenisowego` WHERE `Użytkownik zalogowanyID` = ? AND `Status` = 'Aktywna'";
} elseif ($reservationType == 'iceRink') {
    $query = "SELECT * FROM `Rezerwacja biletu na lodowisko` WHERE `Użytkownik zalogowanyID` = ? AND `Status` = 'Aktywna'";
} else {
    echo "Proszę wybrać typ rezerwacji.";
    exit();
}

// Przygotowanie zapytania SQL
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$reservations = $result->fetch_all(MYSQLI_ASSOC);

$_SESSION['reservations'] = $reservations;

$stmt->close();
$conn->close();
header('Location: userReservationsScreen.php');
?>
