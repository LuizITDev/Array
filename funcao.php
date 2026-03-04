<?php



$numeros = array(1, 2, 3, 4);

$array2 = array(7,8,9);



array_push($numeros , 5,6);
array_pop($numeros);
array_unshift($numeros,0,-1);

$numeros=array_merge($numeros,$array2);
$numeros= array_slice($numeros,3,3);

sort($numeros);
rsort ($numeros);



for ($i=0;$i < count($numeros);$i++){
    echo $numeros [$i];
}






?>