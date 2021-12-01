<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Nachrichten bearbeiten</title>

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
    <a href="dbanlegen.php">Eintragen</a> |
    <u>Bearbeiten</u> |
    <a href="dbsuchen.php">Suchen</a>
</nav>

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




/* Die Nachricht aus der Datenbank zum bearbeiten in ein Formular laden.
 Wurde eine ID über $_GET gesendet? */
if (isset($_GET["id"])) {

    /* Nachricht mit der ID auslesen
      prepare() bereitet die Anweisung für das auslesen vor. */
    $select = $db->prepare("SELECT `id`, `titel`, `autor`, `nachricht`, `kategorie`, `anzeige`, `datum`
                         FROM `nachrichten`
                         WHERE `id` = :id");

    // Der Platzhalter wird mit $select->bindParam() durch den Inhalt der GET-Variablen maskiert.
    $select->bindParam(':id', $_GET["id"], PDO::PARAM_INT);
    $select->execute(); // Führt die Anweisung aus.

    // $select->fetch() holt die betreffende Zeile aus dem Ergebnis.
    $nachricht = $select->fetch();

    // Mit $select->rowCount() überprüfen ob ein Datensatz zurückgegeben wurde.
    if ($select->rowCount() == 1) {

        // Die Auswahlliste für die Kategorie erstellen
        $AuswahllisteKategorie = '<select name="kategorie" size="1">';
        $kategorien = ["Aktuell", "Hardware", "Software"];
        foreach ($kategorien as $kategorie) {
            $AuswahllisteKategorie .= '<option' .
                ($kategorie == $nachricht["kategorie"] ? ' selected="selected"' : '') .
                '>' . $kategorie . '</option>';
        }
        $AuswahllisteKategorie .= '</select>';

        // Die Checkbox auswählen
        $anzeigeCK = ($nachricht["anzeige"] == "1") ? ' checked="checked"' : '';

        // Formular zum bearbeiten der Nachricht ausgeben
        echo '<form action="dbbearbeiten.php" method="post">
   <p>
    <label>Titel: 
     <input type="text" name="titel" value="' . $nachricht["titel"] . '" size="45" maxlength="80" required="required">
    </label>
   </p>

   <p>
    <label>Autor: 
     <input type="text" name="autor" value="' . $nachricht["autor"] . '" size="25" maxlength="30" required="required">
    </label>
   </p>

   <p>
    <label>Kategorie: 
     ' . $AuswahllisteKategorie . '
    </label>
   </p>

   <p>
    <label>Nachricht: <br>
     <textarea rows="10" cols="40" name="nachricht" required="required">' . $nachricht["nachricht"] . '</textarea>
    </label>
   </p>

   <p>
    <label>
     <input type="checkbox" name="anzeige"' . $anzeigeCK . '> Nachricht anzeigen
    </label>
   </p>

   <p>
    <label><input type="radio" name="option" value="edit" checked="checked"> Ändern</label>
    <label><input type="radio" name="option" value="delete" required="required"> Löschen</label>
    <input type="hidden" name="id" value="' . $nachricht["id"] . '">
   </p>

   <p>
    <input type="submit" name="execute" value="Absenden">
   </p>
  </form>';
    }
    else {
        echo '<p>Dieser Datensatz ist nicht vorhanden!</p>';
    }
}

// Nachricht ändern oder löschen
if (isset($_POST["execute"])) {

    // Nachricht ändern
    if ($_POST["option"] == 'edit') {

        /* Die Formulareingaben müssen hier überprüft werden,
         siehe: https://werner-zenk.de/tipps/php_mit_sicherheit.php */

        /*  Der Variable: $anzeige einen Wert zuweisen, entweder 1 oder 0.
          Je nachdem ob die Checkbox gesetzt (ausgewählt) wurde. */
        $anzeige = isset($_POST["anzeige"]) ? 1 : 0;

        // prepare() (prepare = aufbereiten) bereitet die Anweisung für die Ausführung vor.
        $update = $db->prepare("UPDATE `nachrichten`
                          SET
                            `titel`     = :titel,
                            `autor`     = :autor,
                            `nachricht` = :nachricht,
                            `kategorie` = :kategorie,
                            `anzeige` = :anzeige
                          WHERE `id` = :id");

        /* Die Platzhalter werden über ein assoziatives Array mit dem Inhalt der POST-Variablen übergeben.
         $update->execute() führt die Anweisung dann aus. */
        if ($update->execute( [':titel' => $_POST["titel"],
            ':autor' => $_POST["autor"],
            ':nachricht' => $_POST["nachricht"],
            ':kategorie' => $_POST["kategorie"],
            ':anzeige' => $anzeige,
            ':id' => $_POST["id"] ])) {
            echo '<p>&#9655; Die Nachricht wurde überschrieben.</p>';
        }
        else {
            // SQL-Fehlermeldung anzeigen.
            print_r($update->errorInfo());
        }
    }

    // Nachricht löschen
    if ($_POST["option"] == 'delete') {

        // prepare() bereitet die Anweisung für die Ausführung vor.
        $delete = $db->prepare("DELETE FROM `nachrichten`
                          WHERE `id` = :id");

        /* Der Platzhalter wird über ein assoziatives Array mit dem Inhalt der POST-Variable übergeben.
         $delete->execute() führt die Anweisung dann aus. */
        if ($delete->execute( [':id' => $_POST["id"] ])) {
            echo '<p>&#9655; Die Nachricht wurde gelöscht.</p>';
        }
    }
}

/* Nachrichten auslesen
 $select->query() führt die SQL-Anweisung aus,
 die eine Ergebnismenge als PDOStatement Objekt zurück gibt. */
$select = $db->query("SELECT `id`, `titel`, `autor`, `nachricht`, `kategorie`, `anzeige`, `datum`
                      FROM `nachrichten`
                      ORDER BY `datum` DESC");

// $select->fetchAll(PDO::FETCH_OBJ) gibt ein Objekt mit allen Datensätzen zurück.
$nachrichten = $select->fetchAll(PDO::FETCH_OBJ);

// Anzahl der Nachrichten mit count($nachrichten) ausgeben.
echo '<h4>' . count($nachrichten) .
    (count($nachrichten) == 1 ? ' Nachricht' : ' Nachrichten') . '</h4>';

// Ausgabe über eine Foreach-Schleife
foreach ($nachrichten as $nachricht) {
    sscanf($nachricht->datum, "%4s-%2s-%2s", $jahr, $monat, $tag);
    echo '<p><small>' . $tag . '.' . $monat . '.' . $jahr .
        '</small> - <b>' . $nachricht->titel . '</b><br>' .
        ' Autor: <em>' . $nachricht->autor . '</em><br>' .
        ' Kategorie: ' . $nachricht->kategorie . '<br>' .
        nl2br($nachricht->nachricht) . '<br>' .
        'Anzeige: ' . $nachricht->anzeige . ' - ' .
        '<a href="?id=' . $nachricht->id . '"><small>Nachricht bearbeiten</small></a></p>';
}
?>

</body>
</html>