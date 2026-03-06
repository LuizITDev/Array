<?php
$quantidadeNotas = 4;

// cria um array preenchido com vazio
$notas = array_fill(0, $quantidadeNotas, ""); // mantém valores após postback

// criar um array para erros
$erros = array();
$resultado = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $soma = 0;

    // preencher as informacoes no formulario
    for ($i = 0; $i < $quantidadeNotas; $i++) {
        $campo = "nota{$i}";
        // se existir valor no campo retira os espacos senao colocar vazio
        $valor = isset($_POST[$campo]) ? trim($_POST[$campo]) : "";

        // insere o valor que a pessoa digitou
        $notas[$i] = $valor; // repovoar

        // verificacao de erros

        // se a variavel e string e se estiver vazia
        if ($valor === "") {
            // salva a mensagem no array de erros
            $erros[] = "A nota " . ($i + 1) . " está vazia.";
            continue;
        }

        // quando nao for numerico
        if (!is_numeric($valor)) {
            // coloca no array de erro
            $erros[] = "A nota " . ($i + 1) . " precisa ser numérica.";
            continue;
        }

        // converte em float
        $num = (float)$valor;

        // se valor for menor que 0 ou maior que 10
        if ($num < 0 || $num > 10) {
            $erros[] = "A nota " . ($i + 1) . " deve estar entre 0 e 10.";
            continue;
        }

        // soma as notas
        $soma += $num;
    }

    // se nao existir informacoes no array de erro
    if (empty($erros)) {
        $media = $soma / $quantidadeNotas;

        // cria um array associativo com 3 chaves
        $resultado = array(
            'soma'   => $soma,
            'media'  => $media,
            'status' => ($media >= 6) ? 'Aprovado' : 'Reprovado'
        );
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Notas do Aluno (4 notas)</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <div class="card">
    <h1>Cadastro de 4 Notas</h1>
    <p class="footer">Exemplo A — inputs e processamento com <strong>for</strong></p>

    <?php if (!empty($erros)): ?>
      <div class="msg erro">
        <strong>Corrija os seguintes pontos:</strong> 
        <ul class="lista-erros">
          <?php foreach ($erros as $e): ?>
            <li><?php echo htmlspecialchars($e); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if (!empty($resultado)): ?>
      <div class="msg ok resultados">
        <p><strong>Soma:</strong> <?php echo number_format($resultado['soma'], 2, ',', '.'); ?></p>
        <p><strong>Média:</strong> <?php echo number_format($resultado['media'], 2, ',', '.'); ?></p>
        <p><strong>Status:</strong> <?php echo $resultado['status']; ?></p>
      </div>
    <?php endif; ?>

    <form method="post" action="">
      <div class="grid">
        <?php for ($i = 0; $i < $quantidadeNotas; $i++): ?>
          <div>
            <label for="nota<?php echo $i; ?>">Nota <?php echo ($i + 1); ?> (0 a 10)</label>
            <input
              type="number"
              id="nota<?php echo $i; ?>"
              name="nota<?php echo $i; ?>"
              min="0" max="10" step="0.01"
              value="<?php echo htmlspecialchars($notas[$i]); ?>"
              required
            >
          </div>
        <?php endfor; ?>
      </div>
      <button type="submit">Calcular</button>
    </form>
  </div>
</div>
</body>
</html>