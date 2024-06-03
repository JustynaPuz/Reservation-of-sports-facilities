<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="HTML, CSS">
    <meta name="description" content="Strona do rezerwacji kortow tenisowych i biletow na lodowisko">
    <title>Rezerwacja obiektow sportowych</title>
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
            padding-left: 100px;
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
            margin-left: 300px;
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Rezerwacja obiektów sportowych</h1>
    </div>
    <div class="sidebar">
        <a href="#">Strona główna</a>
        <a href="korty.html" class="active">Korty tenisowe</a>
        <a href="lodowisko.html">Lodowisko</a>
    </div>
    <div class="main-content">
        <h2>Witaj!</h2>
        <p>Zaplanuj i zarezerwuj dostęp do różnych obiektów sportowych z łatwością.</p>
    </div>


</body>

</html>