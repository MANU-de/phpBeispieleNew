<?php
// Wurde das Formular abgesendet?
if ("POST" == $_SERVER["REQUEST_METHOD"]) {

    /* Die Formulareingaben müssen hier überprüft werden,
     siehe: https://werner-zenk.de/tipps/php_mit_sicherheit.php */

    /* Verbindung zur Datenbank aufbauen.
     Die Verbindung sollte erst aufgebaut werden, wenn diese benötigt wird. */
    // PHP Fehlermeldungen anzeigen
    error_reporting(E_ALL);
    ini_set('display_errors', true);

// Zugangsdaten zur Datenbank
    $DB_HOST = "localhost"; // Host-Adresse
    $DB_NAME = "phpworld"; // Datenbankname
    $DB_BENUTZER = "root"; // Benutzername
    $DB_PASSWORT = ""; // Passwort

    /* Zeichenkodierung UTF-8 (utf8mb4) bei der Verbindung setzen,
     
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
        echo '<p><a href="dbbearbeiten.php?id=' . $id . '">Nachricht bearbeiten</a></p>';

    }
    else {
        // Andernfalls (bei 'false') wird eine SQL-Fehlermeldung ausgegeben.
        print_r($insert->errorInfo());
    }
}
?>

</body>
</html>
