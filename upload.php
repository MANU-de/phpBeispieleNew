<?php

echo "<pre>";
print_r($_FILES);
echo "</pre>";
if ($dateien = array($_FILES['uploaddatei']['name'] <> "")) {

    //Datei wurde durch HTML-Formular hochgeladen
    //und kann nun weiterverarbeitet werden
    move_uploaded_file(
        $_FILES['uploaddatei']['tmp_name'],
        'hochgeladenes/' . $_FILES['uploaddatei']['name']);

    echo "<p>Hochladen war erfolgreich: ";
    echo '<a href="hochgeladenes/' . $_FILES['uploaddatei']['name'] . '">';
    echo 'hochgeladenes/' . $_FILES['uploaddatei']['name'];
    echo '</a>';

   $dateien=  array($_FILES['uploaddatei']['tmp_name'] => $_FILES['uploaddatei']['name']);
    mail("manuelaschrittwieser@yahoo.com", "Email mit Anhang", "Im Anhang ist eine Datei",
    "Absender", "absender@domain.de");
}
?>
<form name="uploadformular" enctype="multipart/form-data"
      action="upload.php" method="post">

    <br><br>

    Datei: <input type="file" name="uploaddatei" size="" maxlength="">

    <br><br>
    <input type="email" name="email" value="email">
    <input type="submit" name="submit" value="Datei hochladen">
</form>


