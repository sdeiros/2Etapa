<?php
// Simulando conexão com o banco de dados
$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "user";
$usertable = "cadastros";

// Simulação de autenticação de usuário advogado
$advogadoLogado = true; // Defina como verdadeiro se o usuário for advogado
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela do Advogado</title>
</head>

<body>
    <h1>Bem-vindo, Advogado!</h1>
    <a href="solicitacoes.php"><button>Ver Solicitações</button></a>

    <?php
    if ($advogadoLogado) {
        echo '<a href="casos.php"><button>Ver Meus Casos</button></a>';
    }
    ?>

    <?php
    session_start();

    // Verifica se o advogado está logado
    if (isset($_SESSION['advogado'])) {
        // Conexão com o banco de dados
        $hostname = "localhost";
        $username = "root";
        $password = "";
        $dbname = "user";
        $conn = new mysqli($hostname, $username, $password, $dbname);

        // Verifica se houve algum erro na conexão
        if ($conn->connect_error) {
            die('Erro na conexão com o banco de dados: ' . $conn->connect_error);
        }

    } else {
        echo "<p>Você não está logado como advogado.</p>";
    }
    ?>

</body>

</html>