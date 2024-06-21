<!--Connecting -->
<?php
define ('DATABASE_NAME', 'TLHAKHA1_cs2480_final');
include 'DataBase.php';
$thisDataBaseReader = new DataBase('tlhakha1_reader', DATABASE_NAME);
$thisDataBaseWriter = new DataBase('tlhakha1_writer', DATABASE_NAME);
?>
<!--Connection Complete -->