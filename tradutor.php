<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdfFile'])) {
    $pdfFile = $_FILES['pdfFile'];

    if ($pdfFile['error'] === UPLOAD_ERR_OK) {
        $uploadedFilePath = $pdfFile['tmp_name'];
        $pdfText = shell_exec("pdftotext \"$uploadedFilePath\" -");
        $translatedText = traduzirTexto($pdfText);

        // Enviar o texto traduzido de volta para o cliente, por exemplo, como uma resposta JSON
        echo json_encode(['translatedText' => $translatedText]);
    } else {
        // Lida com erros de upload
        echo json_encode(['error' => 'Erro ao fazer o upload do arquivo.']);
    }
} else {
    // Lida com solicitações inválidas
    echo json_encode(['error' => 'Requisição inválida.']);
}

function traduzirTexto($texto) {
    $idiomaOrigem = 'en'; // Idioma de origem (inglês)
    $idiomaDestino = 'pt-br'; // Idioma de destino (português)

    // Use a API do Google Translate para traduzir o texto
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl={$idiomaOrigem}&tl={$idiomaDestino}&dt=t&q=" . urlencode($texto);
    $traducaoJSON = file_get_contents($url);
    $traducaoData = json_decode($traducaoJSON, true);

    if (isset($traducaoData[0][0][0])) {
        return $traducaoData[0][0][0];
    } else {
        return 'Erro ao traduzir o texto.';
    }
}
?>
