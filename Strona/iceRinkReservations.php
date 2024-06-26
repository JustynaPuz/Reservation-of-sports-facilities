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

// Pobranie danych z formularza
$dateTime = $_POST['date'] . ' ' . $_POST['time'];

// Obliczenie czasu rozpoczęcia
$startTime = date('Y-m-d H:i:s', strtotime($dateTime));

// Zapytanie SQL sprawdzające dostępność
$sql = "SELECT `Zajętość` FROM `Termin lodowiska` WHERE `Termin` = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('MySQL prepare error: ' . $conn->error);
}
$stmt->bind_param("s", $startTime);
$stmt->execute();
$result = $stmt->get_result();
$availability = $result->fetch_assoc()['Zajętość'];

// Sprawdzenie, czy jest dostępne miejsce
if ($availability <= 0) {
    header("Location: iceRinkReservations.html?date=" . $_POST['date'] . "&time=" . $_POST['time'] . "&message=Niestety, nie ma wolnych miejsc na ten termin.");
    exit();
} else {
    // Kod do dodania rezerwacji do bazy danych
    $insertSql = "INSERT INTO `Rezerwacja biletu na lodowisko` (`Status`, `Użytkownik zalogowanyID`, `Termin lodowiska`) VALUES ('Aktywna', ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("is", $userId, $startTime);
    if ($insertStmt->execute()) {
        // Zmień zajętość na odpowiednią ilość
        $updateSql = "UPDATE `Termin lodowiska` SET `Zajętość` = `Zajętość` - 1 WHERE `Termin` = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("s", $startTime);
        if ($updateStmt->execute()) {
            header("Location: iceRinkReservations.html?date=" . $_POST['date'] . "&time=" . $_POST['time'] . "&message=Rezerwacja została pomyślnie dokonana.");
            exit();
        } else {
            header("Location: iceRinkReservations.html?date=" . $_POST['date'] . "&time=" . $_POST['time'] . "&message=Błąd podczas aktualizacji terminów.");
            exit();
        }
        $updateStmt->close();
    } else {
        header("Location: iceRinkReservations.html?date=" . $_POST['date'] . "&time=" . $_POST['time'] . "&message=Błąd podczas dodawania rezerwacji.");
        exit();
    }
    $insertStmt->close();
}

$stmt->close();
$conn->close();
?>
