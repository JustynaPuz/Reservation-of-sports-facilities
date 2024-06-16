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

        table {
            width: 80%;
            border-collapse: collapse;
            text-align: center;
        }

        th,
        td {
            padding: 7px;
            border: 1px solid #ccc;
        }

        thead {
            background-color: #f4f4f4;
        }

        tbody tr:nth-child(even) {
            background-color: #eee;
        }

        .reserved {
            background-color: #007BFF;
            color: white;
        }

        td:hover:not(.reserved) {
            background-color: #cccccc;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Rezerwacja obiektów sportowych</h1>
    </div>
    <div class="sidebar">
        <a href="indexAfterLogin.html">Strona główna</a>
        <a href="korty.php" class="active">Korty tenisowe</a>
        <a href="lodowisko.php">Lodowisko</a>
        <a href="userReservationsScreen.php">Rezerwacje</a>
        <a href="accountManagement.html">Profil</a>
        <a href="logout.php">Wyloguj</a>
    </div>
    <div class="main-content">
        <h2>Harmonogram kortów</h2>
        <form id="dateForm">
            <label for="scheduleDate">Wybierz datę:</label>
            <input type="date" id="scheduleDate" name="scheduleDate" required>
            <button type="submit">Pokaż harmonogram</button>
        </form>

        <table id="scheduleTable" border="1">
            <thead>
                <tr>
                    <th>Godzina</th>
                    <th>Kort 1</th>
                    <th>Kort 2</th>
                    <th>Kort 3</th>
                    <th>Kort 4</th>
                </tr>
            </thead>
            <tbody>
                <!-- Harmonogram zostanie wygenerowany tutaj -->
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const scheduleDateInput = document.getElementById('scheduleDate');
            scheduleDateInput.addEventListener('change', () => loadSchedule(scheduleDateInput.value));
            const today = new Date().toISOString().slice(0, 10);
            scheduleDateInput.value = today;

            function loadSchedule(selectedDate) {
                if (!selectedDate) {
                    alert("Proszę wybrać datę.");
                    return;
                }
                console.log('Data wybrana:', selectedDate);

                fetch(`fetchTennisSchedule.php?date=${selectedDate}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Parsed data:', data);
                        const tableBody = document.querySelector('.main-content table tbody');
                        tableBody.innerHTML = ''; // Clear existing rows

                        for (let i = 8; i <= 22; i++) { // Godzinne interwały od 8:00 do 22:00
                            const row = tableBody.insertRow();
                            const timeSlotCell = row.insertCell(0);
                            let hour = Math.floor(i);
                            let formattedTime = `${hour.toString().padStart(2, '0')}:00`;
                            timeSlotCell.textContent = formattedTime;

                            for (let j = 1; j <= 4; j++) { // Zakładając 4 korty
                                const courtCell = row.insertCell(j);
                                const key = `court${j}-${formattedTime}`;
                                console.log("key", key);
                                console.log("Data key", data[key]);
                                if (data[key] === 'Zajety') {
                                    courtCell.textContent = "Zarezerwowany";
                                    courtCell.classList.add('reserved');
                                    courtCell.addEventListener('click', () => cancelReservation(formattedTime, j, selectedDate));
                                } else {
                                    courtCell.textContent = "Wolny";
                                    courtCell.addEventListener('click', () => makeReservation(formattedTime, j, selectedDate));
                                }
                            }
                        }
                    })
                    .catch(error => console.error('Error fetching schedule:', error));
            }

            function makeReservation(time, court, date) {
                const queryParams = `court=${court}&time=${encodeURIComponent(`${time}:00`)}&date=${encodeURIComponent(date)}`;
                window.location.href = `tennisCourtsReservations.html?${queryParams}`;
            }

            function cancelReservation(time, court, date) {
                const queryParams = `court=${court}&time=${encodeURIComponent(`${time}:00`)}&date=${encodeURIComponent(date)}`;
                fetch(`cancelTennisReservation.php?${queryParams}`)
                    .then(response => response.text())
                    .then(result => {
                        console.log('Cancel result:', result);
                        loadSchedule(date); // Reload the schedule after cancelling
                    })
                    .catch(error => console.error('Error cancelling reservation:', error));
            }

            // Wywołanie funkcji loadSchedule z domyślną datą
            loadSchedule(today);

            // Dodanie nasłuchiwania na zmianę daty w input
            scheduleDateInput.addEventListener('change', function () {
                loadSchedule(this.value);
            });

            // Dodanie nasłuchiwania na submit formularza
            document.getElementById('dateForm').addEventListener('submit', function (event) {
                event.preventDefault();
                loadSchedule(scheduleDateInput.value);
            });
        });
    </script>
</body>

</html>

