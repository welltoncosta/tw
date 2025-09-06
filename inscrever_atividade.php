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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atividade_id'])) {
    $participante_id = $usuario['id'];
    $atividade_id = $_POST['atividade_id'];
    
    try {
        // Verificar se o participante já está inscrito
        $stmt = $pdo->prepare("SELECT id FROM inscricoes_atividades WHERE participante_id = :participante_id AND atividade_id = :atividade_id");
        $stmt->execute([
            ':participante_id' => $participante_id,
            ':atividade_id' => $atividade_id
        ]);
        
        if ($stmt->rowCount() === 0) {
            // Fazer a inscrição
            $stmt = $pdo->prepare("INSERT INTO inscricoes_atividades (participante_id, atividade_id) VALUES (:participante_id, :atividade_id)");
            $stmt->execute([
                ':participante_id' => $participante_id,
                ':atividade_id' => $atividade_id
            ]);
            
            $response['success'] = true;
            $response['message'] = "Inscrição realizada com sucesso!";
        } else {
            $response['success'] = false;
            $response['message'] = "Você já está inscrito nesta atividade.";
        }
    } catch (PDOException $e) {
        $response['success'] = false;
        $response['message'] = "Erro ao processar inscrição: " . $e->getMessage();
    }
} else {
    $response['success'] = false;
    $response['message'] = "Parâmetros inválidos.";
}

header('Content-Type: application/json');
echo json_encode($response);
?>
