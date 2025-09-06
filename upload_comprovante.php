<?php
session_start();

if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

include('conexao.php');

$usuario = $_SESSION['usuario'];
$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar se foi enviado um arquivo
    if (isset($_FILES['comprovante']) && $_FILES['comprovante']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['comprovante'];
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_error = $file['error'];
        
        // Obter a extensão do arquivo
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Extensões permitidas
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'pdf');
        
        if (in_array($file_ext, $allowed_extensions)) {
            // Verificar se não há erros
            if ($file_error === 0) {
                // Limitar o tamanho do arquivo (5MB)
                if ($file_size <= 5242880) {
                    // Criar diretório se não existir
                    if (!file_exists('comprovantes')) {
                        mkdir('comprovantes', 0777, true);
                    }
                    
                    // Gerar um nome único para o arquivo
                    $new_file_name = uniqid('', true) . '.' . $file_ext;
                    
                    // Determinar o caminho de destino
                    $file_destination = 'comprovantes/' . $new_file_name;
                    
                    // Mover o arquivo para o diretório de comprovantes
                    if (move_uploaded_file($file_tmp, $file_destination)) {
                        // Determinar o tipo de arquivo
                        $tipo_arquivo = ($file_ext === 'pdf') ? 'pdf' : 'imagem';
                        
                        // Inserir informações no banco de dados
                        $stmt = $pdo->prepare("INSERT INTO comprovantes (participante_id, arquivo, tipo_arquivo) VALUES (:participante_id, :arquivo, :tipo_arquivo)");
                        $stmt->execute([
                            ':participante_id' => $usuario['id'],
                            ':arquivo' => $file_destination,
                            ':tipo_arquivo' => $tipo_arquivo
                        ]);
                        
                        $response['success'] = true;
                        $response['message'] = "Comprovante enviado com sucesso! Aguarde a avaliação.";
                        $response['file_path'] = $file_destination;
                        $response['file_type'] = $tipo_arquivo;
                    } else {
                        $response['success'] = false;
                        $response['message'] = "Erro ao fazer upload do arquivo.";
                    }
                } else {
                    $response['success'] = false;
                    $response['message'] = "O arquivo é muito grande. Tamanho máximo permitido: 5MB.";
                }
            } else {
                $response['success'] = false;
                $response['message'] = "Erro no upload do arquivo.";
            }
        } else {
            $response['success'] = false;
            $response['message'] = "Tipo de arquivo não permitido. Use apenas JPG, PNG, GIF ou PDF.";
        }
    } else {
        $response['success'] = false;
        $response['message'] = "Por favor, selecione um arquivo.";
    }
} else {
    $response['success'] = false;
    $response['message'] = "Método não permitido.";
}

header('Content-Type: application/json');
echo json_encode($response);
?>
