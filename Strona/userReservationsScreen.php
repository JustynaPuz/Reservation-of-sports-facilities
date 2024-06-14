<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="HTML, CSS">
    <meta name="description" content="Strona do rezerwacji kortów tenisowych i biletów na lodowisko">
    <title>Rezerwacja obiektów sportowych</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }

        .header {
            background-color: #007BFF;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
        }

        .sidebar {
            background-color: #333;
            color: white;
            width: 200px;
            height: 100vh;
            position: fixed;
            padding: 20px;
            top: 0;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 10px;
            text-decoration: none;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #555;
        }

        .main-content {
            margin-left: 220px;
            padding: 40px;
            width: calc(100% - 220px);
        }

        .form-group {
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="password"] {
            width: 50%;
            padding: 8px;
            margin-top: 5px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            width: 200px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <?php session_start(); ?> <!-- Początek sesji -->
    <div class="header">
        <h1>Rezerwacja obiektów sportowych</h1>
    </div>
    <div class="sidebar">
        <a href="indexAfterLogin.html">Strona główna</a>
        <a href="korty.php">Korty tenisowe</a>
        <a href="lodowisko.php">Lodowisko</a>
        <a href="userReservations.html" class="active">Rezerwacje</a>
        <a href="accountManagement.html">Profil</a>
        <a href="logout.php">Wyloguj</a>
    </div>
    <div class="main-content">
        <h2>Zarządzaj swoimi rezerwacjami</h2>
        <form action="userReservations.php" method="post">
            <div class="form-group">
                <label for="reservationType">Wybierz typ rezerwacji:</label>
                <select name="reservationType" id="reservationType">
                    <option value="tennis">Korty tenisowe</option>
                    <option value="iceRink">Lodowisko</option>
                </select>
                <button type="submit">Pokaż rezerwacje</button>
            </div>
        </form>

        <?php
        if (!empty($_SESSION['reservations'])) {
            echo "<h3>Aktywne rezerwacje:</h3><ul>";
            foreach ($_SESSION['reservations'] as $reservation) {
                if (isset($reservation['Termin Kortu'], $reservation['Status'], $reservation['ID'], $reservation['KortyNumer Kortu'])) {
                    echo "<li>";
                    echo "Rezerwacja na: " . htmlspecialchars($reservation['Termin Kortu']);
                    echo ", Numer Kortu: " . htmlspecialchars($reservation['KortyNumer Kortu']);
                    echo ", Status: " . htmlspecialchars($reservation['Status']);
                    echo " - <a href='cancelReservation.php?reservationId=" . urlencode($reservation['ID']) . "&termin=" . urlencode($reservation['Termin Kortu']) . "&numerKortu=" . urlencode($reservation['Numer Kortu']) . "'>Anuluj</a>";
                    echo "</li>";
                } else {
                    echo "<li>Niekompletna informacja o rezerwacji.</li>";
                }
            }
            echo "</ul>";
        } else {
            echo "<p>Brak aktywnych rezerwacji.</p>";
        }
        ?>

    </div>
</body>

</html>