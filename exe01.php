<?php

$notas = array();
$notas[0] = 4.0;
$notas[1] = 5.5;
$notas[2] = 6.5;
$notas[3] = 6.0;

echo $notas[0] . "<br>";
echo $notas[1] . "<br>";
echo $notas[2] . "<br>";
echo $notas[3] . "<br>";

$total = $notas [0] + $notas[1] + $notas[2] + $notas[3] ;
echo "Total de notas " . $total."<br>";

$media = $total/4;
echo "Media das notas: " .$media."<br>";

?>