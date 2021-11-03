<?php
setlocale(LC_TIME, "de_DE,utf8");
$kal_datum = time();
$kal_tage_gesamt = date("t", $kal_datum);
$kal_start_timestamp = mktime(0,0,0,date("n", $kal_datum),1,
date("Y",$kal_datum));
$kal_start_tag = date("N", $kal_start_timestamp);
$kal_ende_tag = date("N", mktime(0,0,0, date("n", $kal_datum), $kal_tage_gesamt,
date("Y", $kal_datum)));
?>
<img src="image/bulletin-board-g8e95ef533_1920.jpg">
<link rel="stylesheet" href="style.css">
<h1>Kalender</h1>

<div class="cs-grid-row">
    <div class="cs-grid-col-d-4">
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr,
    sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat,
    sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.</p>
</div>
    <div class="cs-grid-col-d-4">
    <p>Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.
    Lorem ipsum dolor sit amet, consetetur sadipscing elitr,
    sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.
        At vero eos et accusam et justo duo dolores et ea rebum.</p>
    </div>
     <div class="cs-grid-col-d-4">
   <p> Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>
     </div>
</div>
<hr>
<table class = "kalender">
<caption>
    <?php echo utf8_decode(strftime("%B %Y", $kal_datum)); ?>
</caption>
<thead>
    <tr>
        <th>Mo</th>
        <th>Di</th>
        <th>Mi</th>
        <th>Do</th>
        <th>Fr</th>
        <th>Sa</th>
        <th>So</th>
    </tr>
</thead>
<tbody>
    <?php
    for($i = 1; $i <= $kal_tage_gesamt + ($kal_start_tag - 1) + (7 - $kal_ende_tag); $i++)
    {
        $kal_anzeige_akt_tag = $i - $kal_start_tag;
        $kal_anzeige_heute_timestamp = strtotime($kal_anzeige_akt_tag."day", $kal_start_timestamp);
        $kal_anzeige_heute_tag = date("j", $kal_anzeige_heute_timestamp);

        if(date("N", $kal_anzeige_heute_timestamp) == 1)
          echo "    <tr>\n";
         if(date("dmY", $kal_datum) == date("dmY", $kal_anzeige_heute_timestamp))
          echo "        <td class =\"kal_aktueller_tag\">",
          $kal_anzeige_heute_tag,"</td>\n";
        elseif($kal_anzeige_akt_tag >= 0 AND $kal_anzeige_akt_tag < $kal_tage_gesamt)
          echo "      <td class =\" kal_standart_tag\">", $kal_anzeige_heute_tag, "</td>\n";
          else
            echo "    <td class=\"kal_vormonat_tag\">", $kal_anzeige_heute_tag, "</td>\n";

            if(date("N", $kal_anzeige_heute_timestamp) == 7)
               echo "     </tr>\n";
    }
    ?>
</tbody>
    <div class="tfoot">
    <?php
    $Object = new Datetime();
    $DateAndTime = $Object->format("d-m-Y, h:i:s a");
    echo "Das aktuelle Datum und die Zeit beträgt: $DateAndTime";
    ?>
    </div>
</table>
