#!/bin/env php
#ex: php -d memory_limit=1000M split_unimarc.php gros_fichier_unimarc
<?php
$filename = $argv[1];


$contents = file_get_contents($filename);
$unimarcs = preg_split('/'.chr(30).chr(29).'/', $contents);

$i=0;
foreach($unimarcs as $unimarc) {
  $i++;
	file_put_contents("unimarc.$i", $unimarc);
}
?>

