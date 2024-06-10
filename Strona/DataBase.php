<?php
$servername = "localhost";
$username = "AdamJustynaRezerwacje";
$password = "Pwr1234BazyDanych";
$dbname = "rezerwacjaObiektow";

if (!function_exists('mysqli_init') && !extension_loaded('mysqli')) {
    echo 'Inizjalizacja bazy nieudana';
} else {
    echo '';
}

// Tworzenie połączenia
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

return $conn;