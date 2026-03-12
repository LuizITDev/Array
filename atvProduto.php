<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atividade Sistema de Controle de Produtos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        nav a { margin-right: 12px; text-decoration: none; }
        table { border-collapse: collapse; width: 100%; max-width: 760px; margin-top: 12px; }
        table, th, td { border: 1px solid #888; }
        th, td { padding: 8px; text-align: left; }
        .error { color: red; }
        .success { color: green; }
        .warning { color: darkorange; }
    </style>
</head>
<body>
<?php
session_start();

if (!isset($_SESSION['produto_nomes'])) {
    $_SESSION['produto_nomes'] = array();
    $_SESSION['produto_categorias'] = array();
    $_SESSION['produto_quantidades'] = array();
    $_SESSION['produto_precos'] = array();
}

function clean_input($value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

$mensagens = array();
erros:
$erros = array();

// Opção sair
if (isset($_GET['action']) && $_GET['action'] === 'sair') {
    session_unset();
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Cadastro de produto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar_produto'])) {
    $nomeProduto = clean_input($_POST['nomeProduto'] ?? '');
    $categoria = clean_input($_POST['categoria'] ?? '');
    $qtdEstoque = clean_input($_POST['qtdEstoque'] ?? '');
    $precoUnitario = clean_input($_POST['precoUnitario'] ?? '');

    if ($nomeProduto === '') { $erros[] = 'Nome do produto é obrigatório.'; }
    if ($categoria === '') { $erros[] = 'Categoria é obrigatória.'; }
    if ($qtdEstoque === '') { $erros[] = 'Quantidade em estoque é obrigatória.'; }
    if ($precoUnitario === '') { $erros[] = 'Preço unitário é obrigatório.'; }

    if ($qtdEstoque !== '' && (!is_numeric($qtdEstoque) || intval($qtdEstoque) < 0)) {
        $erros[] = 'Quantidade em estoque deve ser número inteiro >= 0.';
    }
    if ($precoUnitario !== '' && (!is_numeric($precoUnitario) || floatval($precoUnitario) < 0)) {
        $erros[] = 'Preço unitário deve ser número >= 0.';
    }

    $totalCadastrados = count($_SESSION['produto_nomes']);
    if ($totalCadastrados >= 10) {
        $erros[] = 'Limite de 10 produtos atingido. Não é possível cadastrar mais.';
    }

    if (empty($erros)) {
        $_SESSION['produto_nomes'][] = $nomeProduto;
        $_SESSION['produto_categorias'][] = $categoria;
        $_SESSION['produto_quantidades'][] = intval($qtdEstoque);
        $_SESSION['produto_precos'][] = floatval($precoUnitario);
        $mensagens[] = 'Produto cadastrado com sucesso!';
    }
}

$acao = $_GET['action'] ?? 'menu';

echo '<h1>Sistema de Controle de Produtos</h1>';

echo '<nav>';
echo '<a href="?action=menu">Menu</a>';
echo '<a href="?action=cadastrar">Cadastrar produto</a>';
echo '<a href="?action=listar">Listar produtos cadastrados</a>';
echo '<a href="?action=buscar">Buscar produto pelo nome</a>';
echo '<a href="?action=estoque_baixo">Produtos com estoque baixo</a>';
echo '<a href="?action=valor_total">Valor total do estoque</a>';
echo '<a href="?action=sair" style="color:#c00;">Sair</a>';
echo '</nav>';

if (!empty($mensagens)) {
    foreach ($mensagens as $msg) {
        echo '<p class="success">' . $msg . '</p>';
    }
}
if (!empty($erros)) {
    echo '<ul class="error">';
    foreach ($erros as $err) {
        echo '<li>' . $err . '</li>';
    }
    echo '</ul>';
}

$produtosTotal = count($_SESSION['produto_nomes']);

switch ($acao) {
    case 'cadastrar':
        if ($produtosTotal >= 10) {
            echo '<p class="warning">Já existem 10 produtos cadastrados. Remova/encerrar sessão para reiniciar.</p>';
        }
        echo '<h2>Cadastro de Produto</h2>';
        echo '<form method="post" action="?action=cadastrar">';
        echo 'Nome do produto:<br><input type="text" name="nomeProduto" required><br><br>';
        echo 'Categoria:<br><input type="text" name="categoria" required><br><br>';
        echo 'Quantidade em estoque:<br><input type="number" name="qtdEstoque" min="0" step="1" required><br><br>';
        echo 'Preço unitário:<br><input type="number" name="precoUnitario" min="0" step="0.01" required><br><br>';
        echo '<button type="submit" name="cadastrar_produto">Cadastrar</button>';
        echo '</form>';
        break;
    case 'listar':
        echo '<h2>Produtos Cadastrados</h2>';
        if ($produtosTotal === 0) {
            echo '<p class="warning">Não existem produtos cadastrados.</p>';
        } else {
            echo '<table><tr><th>Nome</th><th>Categoria</th><th>Quantidade</th><th>Preço unitário</th><th>Valor total</th></tr>';
            for ($i = 0; $i < $produtosTotal; $i++) {
                $valorItem = $_SESSION['produto_quantidades'][$i] * $_SESSION['produto_precos'][$i];
                echo '<tr>';
                echo '<td>' . htmlspecialchars($_SESSION['produto_nomes'][$i], ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($_SESSION['produto_categorias'][$i], ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . $_SESSION['produto_quantidades'][$i] . '</td>';
                echo '<td>R$ ' . number_format($_SESSION['produto_precos'][$i], 2, ',', '.') . '</td>';
                echo '<td>R$ ' . number_format($valorItem, 2, ',', '.') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        break;
    case 'buscar':
        echo '<h2>Buscar Produto pelo Nome</h2>';
        echo '<form method="get" action="?action=buscar">';
        echo 'Nome do produto: <input type="text" name="nome_buscar" required> ';
        echo '<button type="submit">Buscar</button>';
        echo '</form>';

        if (!empty($_GET['nome_buscar'])) {
            $termo = strtolower(clean_input($_GET['nome_buscar']));
            $encontrado = false;
            for ($i = 0; $i < $produtosTotal; $i++) {
                if (strtolower($_SESSION['produto_nomes'][$i]) === $termo) {
                    $encontrado = true;
                    echo '<h3>Produto encontrado:</h3>';
                    echo '<p><strong>Nome:</strong> ' . htmlspecialchars($_SESSION['produto_nomes'][$i], ENT_QUOTES, 'UTF-8') . '</p>';
                    echo '<p><strong>Categoria:</strong> ' . htmlspecialchars($_SESSION['produto_categorias'][$i], ENT_QUOTES, 'UTF-8') . '</p>';
                    echo '<p><strong>Quantidade:</strong> ' . $_SESSION['produto_quantidades'][$i] . '</p>';
                    echo '<p><strong>Preço unitário:</strong> R$ ' . number_format($_SESSION['produto_precos'][$i], 2, ',', '.') . '</p>';
                    echo '<p><strong>Valor total:</strong> R$ ' . number_format($_SESSION['produto_quantidades'][$i] * $_SESSION['produto_precos'][$i], 2, ',', '.') . '</p>';
                    break;
                }
            }
            if (!$encontrado) {
                echo '<p class="warning">Produto não encontrado.</p>';
            }
        }
        break;
    case 'estoque_baixo':
        echo '<h2>Produtos com Estoque Baixo (< 5)</h2>';
        $temBaixo = false;
        if ($produtosTotal === 0) {
            echo '<p class="warning">Não existem produtos cadastrados.</p>';
        } else {
            echo '<ul>';
            for ($i = 0; $i < $produtosTotal; $i++) {
                if ($_SESSION['produto_quantidades'][$i] < 5) {
                    $temBaixo = true;
                    echo '<li>' . htmlspecialchars($_SESSION['produto_nomes'][$i], ENT_QUOTES, 'UTF-8') . ' - Quantidade: ' . $_SESSION['produto_quantidades'][$i] . '</li>';
                }
            }
            echo '</ul>';
            if (!$temBaixo) {
                echo '<p class="warning">Não existem produtos com estoque baixo.</p>';
            }
        }
        break;
    case 'valor_total':
        echo '<h2>Valor Total do Estoque</h2>';
        if ($produtosTotal === 0) {
            echo '<p class="warning">Não é possível calcular: não existem produtos cadastrados.</p>';
        } else {
            $valorGeral = 0.0;
            echo '<table><tr><th>Produto</th><th>Quantidade</th><th>Preço</th><th>Valor total item</th></tr>';
            for ($i = 0; $i < $produtosTotal; $i++) {
                $valorItem = $_SESSION['produto_quantidades'][$i] * $_SESSION['produto_precos'][$i];
                $valorGeral += $valorItem;
                echo '<tr>';
                echo '<td>' . htmlspecialchars($_SESSION['produto_nomes'][$i], ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . $_SESSION['produto_quantidades'][$i] . '</td>';
                echo '<td>R$ ' . number_format($_SESSION['produto_precos'][$i], 2, ',', '.') . '</td>';
                echo '<td>R$ ' . number_format($valorItem, 2, ',', '.') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '<p><strong>Valor total geral do estoque:</strong> R$ ' . number_format($valorGeral, 2, ',', '.') . '</p>';
        }
        break;
    case 'menu':
    default:
        echo '<h2>Menu</h2>';
        echo '<p>Escolha uma opção no menu acima para iniciar.</p>';
        break;
}
?>
</body>
</html>
