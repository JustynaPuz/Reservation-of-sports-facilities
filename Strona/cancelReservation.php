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
    echo json_encode(['success' => false, 'message' => 'Nie znaleziono użytkownika.']);
    $stmt->close();
    $conn->close();
    exit();
}

$userId = $user['ID'];
$reservationId = isset($_GET['reservationId']) ? intval($_GET['reservationId']) : 0;
$termin = isset($_GET['termin']) ? $_GET['termin'] : '';
$numerKortu = isset($_GET['numerKortu']) ? intval($_GET['numerKortu']) : 0;

if ($numerKortu > 0) {
    // Sprawdzenie i usunięcie rezerwacji kortu tenisowego
    $sql = "SELECT * FROM `Rezerwacja kortu tenisowego` WHERE `ID` = ? AND `Użytkownik zalogowanyID` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $reservationId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'Nie znaleziono rezerwacji kortu tenisowego lub nie masz uprawnień do jej anulowania.']);
        $stmt->close();
        $conn->close();
        exit();
    }

    // Usunięcie rezerwacji kortu tenisowego
    $sql = "DELETE FROM `Rezerwacja kortu tenisowego` WHERE `ID` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reservationId);
    $stmt->execute();

    // Zmień zajętość na 0 dla odpowiednich terminów i kortów
    $updateSql = "UPDATE `Termin Kortu` SET `Zajętość` = 0 WHERE `Termin` = ? AND `Numer kortu` = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("si", $termin, $numerKortu);
    if ($updateStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Rezerwacja kortu tenisowego została anulowana.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Błąd podczas aktualizacji terminów: ' . $conn->error]);
    }

    $updateStmt->close();
} else {
    // Sprawdzenie i usunięcie rezerwacji lodowiska
    $sql = "SELECT * FROM `Rezerwacja biletu na lodowisko` WHERE `ID` = ? AND `Użytkownik zalogowanyID` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $reservationId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'Nie znaleziono rezerwacji lodowiska lub nie masz uprawnień do jej anulowania.']);
        $stmt->close();
        $conn->close();
        exit();
    }

    // Usunięcie rezerwacji lodowiska
    $sql = "DELETE FROM `Rezerwacja biletu na lodowisko` WHERE `ID` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reservationId);
    $stmt->execute();

    // Zwiększenie zajętości lodowiska o 1
    $updateSql = "UPDATE `Termin lodowiska` SET `Zajętość` = `Zajętość` + 1 WHERE `Termin` = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("s", $termin);
    if ($updateStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Rezerwacja lodowiska została anulowana.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Błąd podczas aktualizacji terminów: ' . $conn->error]);
    }

    $updateStmt->close();
}

$stmt->close();
$conn->close();
?>
