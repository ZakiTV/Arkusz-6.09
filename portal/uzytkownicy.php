<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal społecznościowy</title>
    <link rel="stylesheet" href="styl5.css">
</head>

<body>
    <div id="banery">
        <header id="banner1">
            <h2>Nasze osiedle</h2>
        </header>
        <header id="banner2">
            <!-- Skrypt 1 -->
            <?php
            $conn = new mysqli("localhost", "root", "", "portal");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            ;
            $uzytkownicy = "SELECT COUNT(ID) FROM `dane`";
            $wynik = $conn->query($uzytkownicy);
            if ($wynik->num_rows > 0) {
                while ($row = $wynik->fetch_assoc()) {
                    echo "<h5>Liczba użytkowników portalu: " . $row["COUNT(ID)"] . "</h5>";
                }
            } else {
                echo "<h2>Brak użytkowników</h2>";
            }
            ?>
        </header>
    </div>
    <div id="left">
        <h3>Logowanie</h3>
        <form method="post" action="">
            <label for="login">Login:</label>
            <input type="text" id="login" name="login" required>
            <br><br>
            <label for="haslo">Hasło:</label>
            <input type="password" id="haslo" name="haslo" required>
            <br><br>
            <input type="submit" value="Zaloguj">
        </form>
    </div>
    <div id="right">
        <h3>Wizytówka</h3>
        <div>
            <!-- Skrypt 2 -->
            <?php
        if (isset($_POST['login']) && isset($_POST['haslo'])) {
            $login = $_POST['login'];
            $password = $_POST['haslo'];

            // Prepared statement for the first query
            $stmt = $conn->prepare("SELECT haslo FROM `uzytkownicy` WHERE `login` = ?");
            $stmt->bind_param("s", $login);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                echo "<h2>Nie ma takiego użytkownika</h2>";
            } else {
                $row = $result->fetch_assoc();
                $hashedPassword = sha1($password);

                if ($hashedPassword === $row['haslo']) {
                    // Prepared statement for the second query
                    $stmt2 = $conn->prepare("SELECT login, rok_urodz, przyjaciol, hobby, zdjecie 
                        FROM `uzytkownicy` 
                        LEFT JOIN dane ON uzytkownicy.id = dane.id
                        WHERE login = ?");
                    $stmt2->bind_param("s", $login);
                    $stmt2->execute();
                    $result2 = $stmt2->get_result();

                    if ($result2->num_rows > 0) {
                        while ($row2 = $result2->fetch_assoc()) {
                            $rokurodzenia = $row2['rok_urodz'];
                            $rok = date('Y');
                            $wiek = $rok - $rokurodzenia;

                            echo "<img src='" . $row2['zdjecie'] . "' alt='osoba'><br>";
                            echo "<h4>" . $row2['login'] . "($wiek)</h4>";
                            echo "<p>Hobby: " . $row2['hobby'] . "</p>";
                            echo "<p>Przyjaciele: " . $row2['przyjaciol'] . "</p>";
                        }
                    }
                } else {
                    echo "<h2>Hasło niepoprawne</h2>";
                }
            }
        } else {
            echo "<h2>Proszę wypełnić formularz logowania</h2>";
        }
        
        ?>
        </div>
    </div>
    <footer id="foot">Strone wykonal: 00000000000</footer>
</body>

</html>