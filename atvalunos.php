<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atividade Aluno</title>
</head>
<body>
    
    
</body>
</html>



<?php
session_start();

// Botão de sair / limpar sessão
if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    session_unset();
    session_destroy();
    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
    exit();
}

if (!isset($_SESSION['alunos'])) {
    $_SESSION['alunos'] = array();
}

$nome = "";
$idade = "";
$curso = "";
$nota_final = "";
$search_nome = "";
$search_result = null;
$search_message = "";
$errors = array();

// Função para limpar e proteger os dados
function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // validação do nome
    if (empty($_POST["nome"])) {
        $errors[] = "O nome é obrigatório.";
    } else {
        $nome = clean_input($_POST["nome"]);
        if (!preg_match("/^[a-zA-ZÀ-ÿ\s]+$/", $nome)) {
            $errors[] = "O nome só pode conter letras e espaços.";
        }
    }

    // Validação da idade
    if (empty($_POST["idade"])) {
        $errors[] = "A idade é obrigatória.";
    } else {
        $idade = clean_input($_POST["idade"]);
        if (!filter_var($idade, FILTER_VALIDATE_INT) || $idade < 0 || $idade > 120) {
            $errors[] = "Idade inválida.";
        }
    }

    // Validação do curso
    if (empty($_POST["curso"])) {
        $errors[] = "O curso é obrigatório.";
    } else {
        $curso = clean_input($_POST["curso"]);
    }

    // Validação da nota final
    if (empty($_POST["nota_final"])) {
        $errors[] = "A nota final é obrigatória.";
    } else {
        $nota_final = clean_input($_POST["nota_final"]);
        if (!is_numeric($nota_final) || $nota_final < 0 || $nota_final > 10) {
            $errors[] = "A nota final deve ser um número entre 0 e 10.";
        }
    }

    // Se não houver erros, grava e exibe os dados
    if (empty($errors)) {
        $aluno = array(
            'nome' => $nome,
            'idade' => $idade,
            'curso' => $curso,
            'nota_final' => $nota_final
        );
        $_SESSION['alunos'][] = $aluno;

        echo "<p style='color:green;'>Formulário enviado com sucesso!</p>";
        echo "<strong>Nome:</strong> $nome<br>";
        echo "<strong>Idade:</strong> $idade<br>";
        echo "<strong>Curso:</strong> $curso<br>";
        echo "<strong>Nota Final:</strong> $nota_final<br>";

        // Limpa valores ao exibir sucesso para novo cadastro
        $nome = $idade = $curso = $nota_final = "";
    }
}
?>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label>Nome:</label><br>
    <input type="text" name="nome" value="<?php echo $nome; ?>"><br><br>

    <label>Idade:</label><br>
    <input type="number" name="idade" value="<?php echo $idade; ?>"><br><br>

    <label>Curso:</label><br>
    <input type="text" name="curso" value="<?php echo $curso; ?>"><br><br>

    <label>Nota Final:</label><br>
    <input type="number" step="0.01" name="nota_final" value="<?php echo $nota_final; ?>"><br><br>

    <input type="submit" value="Enviar">
</form>

<form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="margin-top:1em;">
    <input type="hidden" name="logout" value="1">
    <input type="submit" value="Sair / Limpar sessão" style="background-color:#c00;color:#fff;border:none;padding:8px 12px;cursor:pointer;">
</form>

<h2>Buscar Aluno pelo Nome</h2>
<form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label>Nome do aluno:</label><br>
    <input type="text" name="search_nome" value="<?php echo htmlspecialchars($search_nome); ?>"><br><br>
    <input type="submit" value="Buscar">
</form>

<?php
if (isset($_GET['search_nome'])) {
    $search_nome = clean_input($_GET['search_nome']);

    $found = false;
    foreach ($_SESSION['alunos'] as $alunoCadastrado) {
        if (strcasecmp($alunoCadastrado['nome'], $search_nome) === 0) {
            $search_result = $alunoCadastrado;
            $found = true;
            break;
        }
    }

    if ($found) {
        echo "<h3>Aluno encontrado:</h3>";
        echo "<p><strong>Nome:</strong> " . htmlspecialchars($search_result['nome']) . "</p>";
        echo "<p><strong>Idade:</strong> " . htmlspecialchars($search_result['idade']) . "</p>";
        echo "<p><strong>Curso:</strong> " . htmlspecialchars($search_result['curso']) . "</p>";
        echo "<p><strong>Nota:</strong> " . htmlspecialchars($search_result['nota_final']) . "</p>";
    } else {
        echo "<p style='color:orange;'>Aluno não encontrado.</p>";
    }
}
?>

<h2>Lista de Alunos Cadastrados</h2>
<?php
if (!empty($_SESSION['alunos'])) {
    echo "<table border='1' cellspacing='0' cellpadding='4'>";
    echo "<tr><th>Nome</th><th>Idade</th><th>Curso</th><th>Nota</th></tr>";

    $somaNotas = 0.0;
    $quantidade = count($_SESSION['alunos']);

    foreach ($_SESSION['alunos'] as $alunoCadastrado) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($alunoCadastrado['nome']) . "</td>";
        echo "<td>" . htmlspecialchars($alunoCadastrado['idade']) . "</td>";
        echo "<td>" . htmlspecialchars($alunoCadastrado['curso']) . "</td>";
        echo "<td>" . htmlspecialchars($alunoCadastrado['nota_final']) . "</td>";
        echo "</tr>";

        $somaNotas += floatval($alunoCadastrado['nota_final']);
    }

    echo "</table>";

    if ($quantidade > 0) {
        $media = $somaNotas / $quantidade;
        echo "<p><strong>Média da turma:</strong> " . number_format($media, 2, ',', '.') . "</p>";
    } else {
        echo "<p style='color:orange;'>Não é possível calcular a média porque não existem alunos cadastrados.</p>";
    }
} else {
    echo "<p>Nenhum aluno cadastrado ainda.</p>";
    echo "<p style='color:orange;'>Não é possível calcular a média porque não existem alunos cadastrados.</p>";
}

// Exibe erros, se houver
if (!empty($errors)) {
    echo "<ul style='color:red;'>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
}
?>