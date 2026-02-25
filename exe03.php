<?php

$notas = array();
$notas[0] = 4.0;
$notas[1] = 5.5;
$notas[2] = 6.5;
$notas[3] = 6.0;
$notas[4] = 6.0;


// Mostrar todas as notas
foreach ($notas as $i => $nota) {
    echo "Nota[$i] = $nota<br>";
}

// Maior nota
$maior = max($notas);
echo "<strong>Maior nota: $maior</strong><br>";

// (Opcional) Posições onde a maior nota aparece
$posicoes = array_keys($notas, $maior, true);
echo "Posições: " . implode(', ', $posicoes) . "<br>";
?>
