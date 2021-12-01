<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Nachrichten</title>

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
    <u>Nachrichten</u> |
    <a href="dbanlegen.php">Eintragen</a> |
    <a href="dbbearbeiten.php">Bearbeiten</a> |
    <a href="dbsuchen.php">Suchen</a>
</nav>

<?php
// Verbindung zur Datenbank aufbauen
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






// Anzahl der Datensätze (Nachrichten) pro Seite
$DatensaetzeSeite = 2;

// Anzahl der Datensätze ermitteln
$AnzahlDatensaetze = $db->query("SELECT COUNT(*) FROM `nachrichten` WHERE `anzeige` = '1'")->fetchColumn(0);

// Sind Datensätze vorhanden?
if ($AnzahlDatensaetze > 0) {

    // Die Anzahl der Seiten ermitteln
    $AnzahlSeiten = ceil($AnzahlDatensaetze / $DatensaetzeSeite);

    // Die aktuelle Seite ermitteln
    $AktuelleSeite = isset($_GET["seite"]) ? $_GET["seite"] : 1;

    // Den Wert überprüfen und ggf. ändern
    $AktuelleSeite = ctype_digit($AktuelleSeite) ? $AktuelleSeite : 1;
    $AktuelleSeite = $AktuelleSeite < 1 || $AktuelleSeite > $AnzahlSeiten ? 1 : $AktuelleSeite;

    // Den Versatz ermitteln
    $Versatz = $AktuelleSeite * $DatensaetzeSeite - $DatensaetzeSeite;

    /* Alle Datensätze auslesen die in der DB-Spalte `anzeige` den Wert 1 haben.
     Mit LIMIT die Ausgabe der Datensätze begrenzen (Versatz und Datensätze pro Seite). */
    $select = $db->prepare("SELECT `titel`, `autor`, `nachricht`, `kategorie`, `datum`
                         FROM `nachrichten`
                         WHERE `anzeige` = '1'
                         ORDER BY `datum` DESC
                         LIMIT :versatz, :DatensaetzeSeite");
    $select->bindParam(':versatz', $Versatz, PDO::PARAM_INT);
    $select->bindParam(':DatensaetzeSeite', $DatensaetzeSeite, PDO::PARAM_INT);
    $select->execute();
    $nachrichten = $select->fetchAll();

    // Ausgabe über eine Foreach-Schleife
    foreach ($nachrichten as $nachricht) {
        // Mit sscanf() wird das Format des Datums in die Variablen $jahr, $monat und $tag extrahiert.
        sscanf($nachricht['datum'], "%4s-%2s-%2s", $jahr, $monat, $tag);

        echo '<p><small>' . $tag . '.' . $monat . '.' . $jahr .
            '</small> - <b>' . $nachricht['titel'] . '</b><br>' .
            ' Kategorie: ' . $nachricht['kategorie'] . '<br>' .
            ' Autor: <em>' . $nachricht['autor'] . '</em><br>' .
            nl2br($nachricht['nachricht']) . '</p>';
    }

    // Formular.- und Blätterfunktion (Wer sich da auskennt bekommt einen Preis verliehen ;)
    echo '<form action="dbauslesen.php" method="GET" autocomplete="off">' .
        (($AktuelleSeite - 1) > 0 ?
            '<a href="?seite=' . ($AktuelleSeite - 1) . '">&#9664;</a>' :
            ' &#9664;') .
        ' <label>Seite <input type="text" value="' . $AktuelleSeite . '" name="seite" size="3"' .
        ' title="Seitenzahl eingeben und die Eingabetaste drücken."> von ' . $AnzahlSeiten . '</label>' .
        (($AktuelleSeite + 1) <= $AnzahlSeiten ?
            ' <a href="?seite=' . ($AktuelleSeite + 1) . '">&#9654;</a>' :
            ' &#9654;') .
        '</form>';
}
else {
    echo '<p>Keine Nachrichten vorhanden!</p>';
}
?>

</body>
</html>