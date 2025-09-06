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
        // Cancelar a inscrição
        $stmt = $pdo->prepare("DELETE FROM inscricoes_atividades WHERE participante_id = :participante_id AND atividade_id = :atividade_id");
        $stmt->execute([
            ':participante_id' => $participante_id,
            ':atividade_id' => $atividade_id
        ]);
        
        $response['success'] = true;
        $response['message'] = "Inscrição cancelada com sucesso!";
    } catch (PDOException $e) {
        $response['success'] = false;
        $response['message'] = "Erro ao cancelar inscrição: " . $e->getMessage();
    }
} else {
    $response['success'] = false;
    $response['message'] = "Parâmetros inválidos.";
}

header('Content-Type: application/json');
echo json_encode($response);
?>
