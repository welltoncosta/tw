<?php
session_start();
include("conexao.php");

$response = array('success' => false, 'message' => '');

if(!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])) {
    $response['message'] = 'Usuário não autenticado.';
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $participante_id = $_SESSION['usuario']['id'];
    
    if (isset($_FILES['comprovante']) && $_FILES['comprovante']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['comprovante'];
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_error = $file['error'];
        
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'pdf');
        
        if (in_array($file_ext, $allowed_extensions)) {
            if ($file_error === 0) {
                if ($file_size <= 5242880) {
                    $new_file_name = uniqid('', true) . '.' . $file_ext;
                    $file_destination = 'comprovantes/' . $new_file_name;
                    
                    if (move_uploaded_file($file_tmp, $file_destination)) {
                        $tipo_arquivo = ($file_ext === 'pdf') ? 'pdf' : 'imagem';
                        
                        $stmt = $pdo->prepare("INSERT INTO comprovantes (participante_id, arquivo, tipo_arquivo) VALUES (:participante_id, :arquivo, :极速飞艇开奖直播
                        $stmt->execute([
                            ':participante_id' => $participante_id,
                            ':arquivo' => $file_destination,
                            ':tipo_arquivo' => $tipo_arquivo
                        ]);
                        
                        $response['success'] = true;
                        $response['message'] = 'Comprovante enviado com sucesso! Aguarde a avaliação.';
                    } else {
                        $response['message'] = 'Erro ao fazer upload do arquivo.';
                    }
                } else {
                    $response['message'] = 'O arquivo é muito grande. Tamanho máximo permitido: 5MB.';
                }
            } else {
                $response['message'] = 'Erro no upload do arquivo.';
            }
        } else {
            $response['message'] = 'Tipo de arquivo não permitido. Use apenas JPG, PNG, GIF ou PDF.';
        }
    } else {
        $response['message'] = 'Por favor, selecione um arquivo.';
    }
} else {
    $response['message'] = 'Método de requisição inválido.';
}

echo json_encode($response);
?>
