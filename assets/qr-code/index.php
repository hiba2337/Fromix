
<?php

require_once "phpqrcode/qrlib.php";
$path ='images/';
$qrcode = $path . time() . ".png";


QRcode::png("Tech Area", $qrcode);


echo "<img src='" . $qrcode . "'>"; 

?>
