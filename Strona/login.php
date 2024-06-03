<?php
$servername = "localhost";
$username = "AdamJustynaRezerwacje";
$password = "Pwr1234BazyDanych";
$dbname = "rezerwacjaObiektow";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$username = $_POST['username'];
$password = $_POST['password'];

// Zabezpieczenie przed SQL Injection
$username = $conn->real_escape_string($username);
$password = $conn->real_escape_string($password);


$sql = "SELECT `Haslo` FROM `Użytkownik zalogowany` WHERE `Login` = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Sprawdzenie hasła
    $row = $result->fetch_assoc();
    if ($password === $row['Haslo']) {
        header("Location: Profile.html");
        $conn->close();
        exit; 

      
    } else {
        echo "Nieprawidłowe hasło";
    }
} else {
    echo "Nieprawidłowy login";
}

$conn->close();
?>
