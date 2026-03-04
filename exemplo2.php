<?php
$quantidadeNotas = 4 ;

?>








<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro do aluno</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
 <div class = "container">
    <div class="card">
        <h1> Cadastro do Aluno</h1>
        <p class = "footer"> Exemplo A - inputs e processamento com 
            <strong>for</strong>      </p>


  

    </div>

<form method="post">
    <div class = "grid">
    <?php for ($i=0; $i<$quantidadeNotas;$i++);?>
    </div>
    <div>
        <label for="nota<?= $i ?>">Nota <?= $i+1?>(0 a 0)</label>
        <input type = "number" id ="nota<?= $i ?> name ="nota <?= $i ?>"
        min="0" max="10" step="0.01" value = "<?=htmlspecialchars($notas[$i])?>"
        required >
    </div>



</form>

 </div>   
</body>
</html>