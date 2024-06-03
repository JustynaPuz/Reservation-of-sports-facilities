<?php



if ( ! filter_var($_POST["login"], FILTER_VALIDATE_EMAIL)) {
    die("Valid email is required");
}

if (!preg_match("/^[0-9]{9}$/", $_POST["phoneNumber"])) {
    die("Valid phone number is required");
}


$conn = require __DIR__ . "/DataBase.php";


// Odbiór danych z formularza
$name = $_POST['name'];
$surname = $_POST['surname'];
$email = $_POST['login'];
$password = $_POST['password'];
$phone_number = $_POST['phoneNumber'];

$name = $conn->real_escape_string($name);
$surname = $conn->real_escape_string($surname);
$email = $conn->real_escape_string($email);
$password = $conn->real_escape_string($password);
$phone_number = $conn->real_escape_string($phone_number);

$sql_check = "SELECT * FROM `Użytkownik zalogowany` WHERE `Numer telefonu` = '$phone_number'";
$result = $conn->query($sql_check);

if ($result->num_rows > 0) {
    die("Phone number already exists");
}


$sql_check = "SELECT * FROM `Użytkownik zalogowany` WHERE `login` = '$email'";
$result = $conn->query($sql_check);

if ($result->num_rows > 0) {
    die("login already exists");
}



$sql = "INSERT INTO `Użytkownik zalogowany` (`Imie`, `Nazwisko`, `Login`, `Haslo`, `Numer telefonu`) VALUES ('$name', '$surname', '$email', '$password', '$phone_number')";


if ($conn->query($sql) === TRUE) {
    
    header("Location: register-success.html");
    $conn->close();
    exit; 


} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

?>
