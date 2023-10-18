<!DOCTYPE html>
<html>

<head>
    <title>Tradutor de Texto e Documentos</title>
</head>

<body>
    <h1>Tradutor de Texto e Documentos</h1>

    <form method="post" enctype="multipart/form-data">
        <label for="idiomaOrigem">Idioma de Origem:</label>
        <select name="idiomaOrigem">
            <option value="en">Inglês</option>
            <option value="es">Espanhol</option>
            <option value="fr">Francês</option>
            <option value="de">Alemão</option>
            <option value="pt-br">Português</option>
            <!-- Adicione mais opções conforme necessário -->
        </select><br><br>

        <label for="idiomaDestino">Idioma de Destino:</label>
        <select name="idiomaDestino">
            <option value="" default selected>Selecione o idioma de destino</option>
            <option value="en">Inglês</option>
            <option value="es">Espanhol</option>
            <option value="fr">Francês</option>
            <option value="de">Alemão</option>
            <option value="pt-br">Português</option>
            <!-- Adicione mais opções conforme necessário -->
        </select><br><br>

        <label for="textoParaTraduzir">Texto para Traduzir:</label>
        <textarea name="textoParaTraduzir" placeholder="Digite o texto a ser traduzido"></textarea><BR><br>

        <label for="arquivoParaTraduzir">Selecione um arquivo para traduzir:</label>
        <input type="file" name="arquivoParaTraduzir"><br><br>

        <button type="submit" name="traduzirTexto">Traduzir Texto</button>
        <button type="submit" name="traduzirArquivo">Traduzir Arquivo</button>
    </form>

    <p><strong>Texto traduzido:</strong></p>
    <p id="textoTraduzido">
        <?php
        require 'vendor/autoload.php';        // Certifique-se de que as bibliotecas do fpdi estejam carregadas

        use setasign\Fpdi\Fpdi;

        // Função para extrair o texto de um arquivo PDF
        function extrairTextoDePDF($arquivoPDF)
        {
            $pdf = new Fpdi();
            $pdf->setSourceFile($arquivoPDF);
            $numPaginas = $pdf->getTemplateCount();
            $texto = '';

            for ($i = 1; $i <= $numPaginas; $i++) {
                $templateId = $pdf->importPage($i);
                $texto .= $pdf->getImportedPage($templateId)->getText();
            }

            return $texto;
        }

        // Substitua 'sua-chave-de-api' pela sua chave de acesso à API do Google Translate
        $chaveAPI = 'AIzaSyAnMd3BEYbBj91_N8y4atbxHwx0poA0svg';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['traduzirTexto'])) {
                // Traduzir texto
                $textoParaTraduzir = $_POST['textoParaTraduzir'];
                $idiomaOrigem = $_POST['idiomaOrigem'];
                $idiomaDestino = $_POST['idiomaDestino'];
                traduzirTexto($textoParaTraduzir, $idiomaOrigem, $idiomaDestino);
            } elseif (isset($_POST['traduzirArquivo'])) {
                // Traduzir arquivo
                $idiomaOrigem = $_POST['idiomaOrigem'];
                $idiomaDestino = $_POST['idiomaDestino'];
                $arquivo = $_FILES['arquivoParaTraduzir'];

                if ($arquivo['error'] === UPLOAD_ERR_OK) {
                    $textoDoArquivo = extrairTextoDePDF($arquivo['tmp_name']);
                    traduzirTexto($textoDoArquivo, $idiomaOrigem, $idiomaDestino);
                } else {
                    echo 'Erro ao carregar o arquivo.';
                }
            }
        }

        function traduzirTexto($texto, $idiomaOrigem, $idiomaDestino)
        {
            global $chaveAPI;

            // Solicitar a tradução à API do Google Translate
            $ch = curl_init("https://translation.googleapis.com/language/translate/v2?key=$chaveAPI");
            $data = array(
                'q' => $texto,
                'source' => $idiomaOrigem,
                'target' => $idiomaDestino,
                'format' => 'text'
            );
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            $traducao = json_decode($response, true);

            if ($traducao) {
                $textoTraduzido = $traducao['data']['translations'][0]['translatedText'];
                echo $textoTraduzido;
            } else {
                echo 'Erro ao traduzir.';
            }
        }
        ?>

    </p>
</body>

</html>