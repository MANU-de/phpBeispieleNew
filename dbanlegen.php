<?php

// Verbindung zur Datenbank aufbauen.
// PHP Fehlermeldungen anzeigen
error_reporting(E_ALL);
ini_set('display_errors', true);

// Zugangsdaten zur Datenbank
$DB_HOST = "localhost"; // Host-Adresse
$DB_NAME = "phpworld"; // Datenbankname
$DB_BENUTZER = "root"; // Benutzername
$DB_PASSWORT = ""; // Passwort

/* Zeichenkodierung UTF-8 (utf8mb4) bei der Verbindung setzen,
 Infos: https://werner-zenk.de/tipps/schriftzeichen_richtig_darstellen.php
 Und eine PDOException bei einem Fehler auslösen. */
$OPTION = [
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
];

try {
    // Verbindung zur Datenbank aufbauen
    $db = new PDO("mysql:host=" . $DB_HOST . ";dbname=" . $DB_NAME,
        $DB_BENUTZER, $DB_PASSWORT, $OPTION);
}
catch (PDOException $e) {
    // Bei einer fehlerhaften Verbindung eine Nachricht ausgeben
    exit("Verbindung fehlgeschlagen! " . $e->getMessage());
}


try {
    // Anlegen der Datenbank-Tabelle.
    $db->exec("CREATE TABLE IF NOT EXISTS `nachrichten` (
            `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `titel` VARCHAR(80) NOT NULL,
            `autor` varchar(30) NOT NULL DEFAULT 'Werner',
            `nachricht` TEXT NOT NULL,
            `kategorie` VARCHAR(25) NOT NULL,
            `anzeige` TINYINT(1) NOT NULL,
            `datum` DATE NOT NULL
           ) ENGINE = MYISAM DEFAULT CHARSET=utf8");

    echo '<p>&#9655; Die Datenbank-Tabelle wurde angelegt.<br>
    <a href="eintragen.php">Erste Nachricht eintragen</a></p>';

}
catch (PDOException $e) {
    // Bei einem Fehler eine Nachricht ausgeben.
    exit('<p>&#9655; Fehler beim anlegen der Datenbank-Tabelle!</p>' .
        $e->getMessage());
}
?>


<html>
<head>
    <meta charset="UTF-8">
    <title>Nachricht eintragen</title>

    <style>
        body {
            font-family: Verdana, Arial, Sans-Serif;
            background-color: Whitesmoke;
        }

        a:link, a:visited {
            color: Royalblue;
            text-decoration: None;
        }
    </style>

</head>
<body>

<nav>
    <a href="dbauslesen.php">Nachrichten</a> |
    <u>Eintragen</u> |
    <a href="dbbearbeiten.php">Bearbeiten</a> |
    <a href="dbsuchen.php">Suchen</a>
</nav>

<form action="eintragen.php" method="post">

    <p>
        <label>Titel:
            <input type="text" name="titel" size="45" maxlength="80" required="required" autofocus="autofocus">
        </label>
    </p>

    <p>
        <label>Autor:
            <input type="text" name="autor" size="25" maxlength="30" required="required">
        </label>
    </p>

    <p>
        <label>Kategorie:
            <select name="kategorie" size="1" required="required">
                <option>Aktuell</option>
                <option>Hardware</option>
                <option>Software</option>
            </select>
        </label>
    </p>

    <p>
        <label>Nachricht:<br>
            <textarea rows="10" cols="40" name="nachricht" required="required"></textarea>
        </label>
    </p>

    <p>
        <label>
            <input type="checkbox" name="anzeige"> Nachricht anzeigen
        </label>
    </p>

    <p>
        <input type="submit" value="Absenden">
    </p>

</form>

<?php
// Wurde das Formular abgesendet?
if ("POST" == $_SERVER["REQUEST_METHOD"]) {

    /* Die Formulareingaben müssen hier überprüft werden,
     siehe: https://werner-zenk.de/tipps/php_mit_sicherheit.php */

    /* Verbindung zur Datenbank aufbauen.
     Die Verbindung sollte erst aufgebaut werden, wenn diese benötigt wird. */
    include "verbinden.php";

    /* Der Variable $anzeige einen Wert zuweisen, entweder 1 oder 0.
     Je nachdem ob die Checkbox gesetzt (ausgewählt) wurde. */
    $anzeige = isset($_POST["anzeige"]) ? 1 : 0;

    /* Nachricht eintragen
     prepare() (prepare = aufbereiten) bereitet die Anweisung für die Ausführung vor.
     Die Platzhalter werden hier anstatt den POST-Variablen eingesetzt. */
    $insert = $db->prepare("INSERT INTO `nachrichten`
                                 SET
                                  `titel`     = :titel,
                                  `autor`     = :autor,
                                  `nachricht` = :nachricht,
                                  `kategorie` = :kategorie,
                                  `anzeige` = :anzeige,
                                  `datum` = NOW()");

    /* Die Platzhalter werden mit $insert->bindValue() durch den
     Inhalt der POST-Variablen ersetzt und maskiert. */
    $insert->bindValue(':titel', $_POST["titel"]);
    $insert->bindValue(':autor', $_POST["autor"]);
    $insert->bindValue(':nachricht', $_POST["nachricht"]);
    $insert->bindValue(':kategorie', $_POST["kategorie"]);
    $insert->bindValue(':anzeige', $anzeige);

    /* $insert->execute() führt die vorbereitete Anweisung aus.
     Bei einem erfolgreichen Eintrag wird 'true' zurück gegeben. */
    if ($insert->execute()) {
        echo '<p>&#9655; Die Nachricht wurde eingetragen.</p>';

        /* Um die gerade eingetragene Nachricht bearbeiten zu können, benötigen
         wir die ID des zuletzt eingetragenen Datensatzes: lastInsertId() */
        $id = $db->lastInsertId();

        // Nun hängen wir an den Dateinamen (bearbeiten.php) die ID dran
        echo '<p><a href="bearbeiten.php?id=' . $id . '">Nachricht bearbeiten</a></p>';

    }
    else {
        // Andernfalls (bei 'false') wird eine SQL-Fehlermeldung ausgegeben.
        print_r($insert->errorInfo());
    }
}
?>

</body>
</html>




