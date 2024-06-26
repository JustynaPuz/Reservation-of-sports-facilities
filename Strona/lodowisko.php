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
            width: 90%;
            border-collapse: collapse;
            text-align: center;
        }

        th,
        td {
            padding: 6px;
            border: 1px solid #ccc;
        }

        thead {
            background-color: #f4f4f4;
        }

        tbody tr:nth-child(even) {
            background-color: #eee;
        }

        tbody td {
            cursor: pointer;
        }

        tbody td:hover {
            background-color: #e0e0e0;
        }

        tbody td.niedostepne {
            background-color: #cccccc;
            color: #666666;
            cursor: not-allowed;
            pointer-events: none;
        }

        tbody td.przerwa {
            background-color: #ffd700;
            color: #333;
            cursor: not-allowed;
            pointer-events: none;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Rezerwacja obiektów sportowych</h1>
    </div>
    <div class="sidebar">
        <a href="indexAfterLogin.php">Strona główna</a>
        <a href="korty.php">Korty tenisowe</a>
        <a href="lodowisko.php" class="active">Lodowisko</a>
        <a href="userReservationsScreen.php">Rezerwacje</a>
        <a href="accountManagement.html">Profil</a>
        <a href="logout.php">Wyloguj</a>
    </div>
    <div class="main-content">
        <h2>Harmonogram lodowiska</h2>
        <form id="weekForm">
            <label for="weekSelector">Wybierz tydzień:</label>
            <input type="week" id="weekSelector" name="week" required>
        </form>
        <table id="scheduleTable" border="1">
            <thead>
                <tr>
                    <th>Godzina</th>
                    <th>Poniedziałek</th>
                    <th>Wtorek</th>
                    <th>Środa</th>
                    <th>Czwartek</th>
                    <th>Piątek</th>
                    <th>Sobota</th>
                    <th>Niedziela</th>
                </tr>
            </thead>
            <tbody>
                <!-- Harmonogram zostanie wygenerowany tutaj -->
            </tbody>
        </table>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const weekSelector = document.getElementById('weekSelector');

            function setCurrentWeek() {
                const currentDate = new Date();
                console.log('setWeekcurrentDate:', currentDate);
                const currentYear = currentDate.getFullYear();
                console.log('setWeekcurrentYear:', currentYear);
                const start = new Date(currentYear, 0, 1); // Start of the year

                // Normalizacja dat do północy
                const normalizedToday = new Date(currentDate.setHours(0, 0, 0, 0));
                const normalizedStartOfYear = new Date(start.setHours(0, 0, 0, 0));

                console.log('setWeekcurrentStart:', start);
                const days = Math.ceil((normalizedToday - normalizedStartOfYear) / (24 * 60 * 60 * 1000)) + 1;
                console.log('setWeekcurrentDays:', days);

                const weekNumber = Math.ceil((days) / 7);
                console.log('getday:', currentDate.getDay());
                console.log('setWeekcurrentWeekNumber:', weekNumber);
                weekSelector.value = `${currentYear}-W${weekNumber.toString().padStart(2, '0')}`;
                weekSelector.min = `${currentYear}-W${weekNumber.toString().padStart(2, '0')}`;
                weekSelector.max = `${currentYear}-W${(weekNumber + 1).toString().padStart(2, '0')}`;
                console.log('setWeekcurrentWeekSelector:', weekSelector.value);
                generateSchedule(); // Initial call to populate the table
            }

            setCurrentWeek(); // Set the current week on load
            weekSelector.addEventListener('change', generateSchedule);
        });

        function getMondayOfSelectedWeek(weekValue) {
            const [year, week] = weekValue.split('-W');
            const firstDayOfYear = new Date(year, 0, 1); // Pierwszy dzień roku
            const days = (parseInt(week, 10) - 1) * 7;
            firstDayOfYear.setDate(firstDayOfYear.getDate() + days);

            const dayOfWeek = firstDayOfYear.getDay(); // Dzień tygodnia dla 1 stycznia
            const offset = (dayOfWeek === 0 ? 6 : dayOfWeek - 1); // Ajust for Monday start
            firstDayOfYear.setDate(firstDayOfYear.getDate() - offset);

            return firstDayOfYear;
        }


        function generateSchedule() {
            const tableBody = document.querySelector('#scheduleTable tbody');
            tableBody.innerHTML = ''; // Czyszczenie istniejących wierszy tabeli
            const selectedWeek = document.getElementById('weekSelector').value;
            const startDate = getMondayOfSelectedWeek(selectedWeek);
            const endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + 8);

            console.log('Start:', startDate);
            console.log('End:', endDate);

            const startDateStr = startDate.toISOString().split('T')[0];
            const endDateStr = endDate.toISOString().split('T')[0];
            console.log('StartStr:', startDateStr);
            console.log('EndStr:', endDateStr);

            fetch(`fetchScheduleIceRink.php?start=${startDateStr}&end=${endDateStr}`)
                .then(response => response.text())
                .then(text => {
                    console.log('Raw response:', text); // Log raw response for debugging

                    try {
                        const data = JSON.parse(text);
                        console.log('Parsed data:', data);
                        for (let i = 0; i < 15; i++) {
                            const hour = 8 + i; // Godziny otwarcia od 8:00 do 22:00
                            const timeSlot = `${hour}:00`;
                            const row = tableBody.insertRow();
                            const timeCell = row.insertCell(0);
                            timeCell.textContent = timeSlot;

                            for (let j = 1; j <= 7; j++) {
                                const dayCell = row.insertCell(j);
                                const dayOffset = j; // Corrected day offset
                                const reservationDate = new Date(startDate);
                                reservationDate.setDate(startDate.getDate() + dayOffset);
                                //console.log('Reserv:', reservationDate);
                                const dateString = reservationDate.toISOString().split('T')[0];
                                const dateTimeString = `${dateString} ${String(hour).padStart(2, '0')}:00:00`;
                                console.log('DateTimeString:', dateTimeString);

                                const availability = data[dateTimeString] !== undefined ? data[dateTimeString] : 0;
                                console.log(`Date: ${dateString}, Availability: ${availability}`);

                                if ((j <= 5 && (hour >= 8 && hour <= 11)) || hour === 15 || hour === 19) {
                                    if (hour === 15 || hour === 19) {
                                        dayCell.textContent = 'Przerwa';
                                        dayCell.className = 'przerwa';
                                    } else {
                                        dayCell.textContent = 'Niedostępne';
                                        dayCell.className = 'niedostepne';
                                    }
                                } else {
                                    dayCell.textContent = `${availability} miejsc`;
                                    dayCell.className = 'wolny';
                                    addClickHandler(dayCell, hour, reservationDate);
                                }
                            }
                        }
                    } catch (error) {
                        console.error('Error parsing JSON:', error, text); // Log error and raw response
                    }
                })
                .catch(error => console.error('Error fetching data:', error));
        }




        function addClickHandler(cell, time, reservationDate) {
            cell.addEventListener('click', function () {
                const queryParams = `time=${encodeURIComponent(`${time}:00`)}&date=${encodeURIComponent(reservationDate.toISOString().split('T')[0])}`;
                window.location.href = `iceRinkReservations.html?${queryParams}`;
            });
        }


    </script>
</body>

</html>