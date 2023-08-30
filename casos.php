<?php
session_start();

$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "user";

$advogadoLogado = true; // Defina como verdadeiro se o usuário for advogado

$conn = new mysqli($hostname, $username, $password, $dbname);

if ($conn->connect_error) {
    die('Erro na conexão com o banco de dados: ' . $conn->connect_error);
}

if (!$advogadoLogado) {
    header("Location: login.php"); // Redirecionar para a página de login
    exit();
}

$nomeAdvogado = $_SESSION['advogado']['nome']; // Obtém o nome do advogado logado

$sql_casos = "SELECT * FROM pedidos_refugio WHERE advogado_nome = ?";
$stmt_casos = $conn->prepare($sql_casos);
$stmt_casos->bind_param("s", $nomeAdvogado);
$stmt_casos->execute();
$result_casos = $stmt_casos->get_result();

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <!-- Cabeçalho e metatags aqui -->
</head>

<body>
    <h1>Meus Casos</h1>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar'])) {
        $caso_id = $_POST['caso_id'];
        $descricao = $_POST['descricao'];
        $status = $_POST['status'];
        $concluido = isset($_POST['concluido']) ? 1 : 0;

        // Atualiza o caso na tabela pedidos_refugio
        $sql_update = "UPDATE pedidos_refugio SET descricao = ?, status = ?, concluido = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);

        if ($stmt_update) {
            $stmt_update->bind_param("ssii", $descricao, $status, $concluido, $caso_id);
            if ($stmt_update->execute()) {
                echo "Caso atualizado com sucesso!";
            } else {
                echo "Erro ao atualizar o caso: " . $stmt_update->error;
            }
        } else {
            echo "Erro ao preparar a atualização do caso: " . $conn->error;
        }
    }

    if ($result_casos->num_rows > 0) {
        while ($caso = $result_casos->fetch_assoc()) {
            echo '<div>';
            echo '<h3>Caso #' . $caso['id'] . '</h3>';
            echo '<p>Usuário: ' . $caso['usuario'] . '</p>'; // Exibe o nome do usuário
    
            // Exibe a foto
            echo '<img src="' . $caso['foto'] . '" alt="Foto do caso">';
    
            // Exibe o botão "Baixar Documento" apenas se houver um documento
            if (!empty($caso['documento'])) {
                echo '<a href="' . $caso['documento'] . '">Baixar Documento</a>';
            }
    
            echo '<form method="POST" enctype="multipart/form-data">';
            echo '<input type="hidden" name="caso_id" value="' . $caso['id'] . '">';
            echo '<textarea name="descricao">' . $caso['descricao'] . '</textarea>';
            echo '<input type="text" name="status" value="' . $caso['status'] . '">';
            echo '<label><input type="checkbox" name="concluido" value="1" ' . ($caso['concluido'] ? 'checked' : '') . '> Concluído</label>';
            echo '<input type="submit" name="atualizar" value="Atualizar">';
            echo '</form><hr>';
    
            echo '</div>';
        }
    } else {
        echo 'Nenhum caso encontrado.';
    }

    $conn->close();
    ?>


</body>

</html>