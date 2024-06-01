<?php
$servername = "localhost";  // lub nazwa kontenera Docker, jeśli jest inna
$username = "AdamJustynaRezerwacje";
$password = "Pwr1234BazyDanych";
$dbname = "rezerwacjaObiektow";

// Tworzenie połączenia
$conn = new mysqli($servername, $username, $password, $dbname);

// Sprawdzenie połączenia
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Odbiór danych z formularza
$name = $_POST['name'];
$surname = $_POST['surname'];
$email = $_POST['login'];
$password = $_POST['password'];
$phone_number = $_POST['phoneNumber'];

// Zabezpieczenie przed SQL Injection
$name = $conn->real_escape_string($name);
$surname = $conn->real_escape_string($surname);
$email = $conn->real_escape_string($email);
$password = $conn->real_escape_string($password);
$phone_number = $conn->real_escape_string($phone_number);

// Zapytanie SQL
$sql = "INSERT INTO Użytkownik zalogowany (Imie, Nazwisko, Login, Haslo, Numer telefonu) VALUES ('$name', '$surname', '$email', '$password', '$phone_number')";

// Wykonanie zapytania
if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
