<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include("conexao.php");

try {
    // Buscar informações do evento
    $stmt = $pdo->prepare("SELECT * FROM informacoes_evento WHERE id = 1");
    $stmt->execute();
    $evento = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'evento' => $evento]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao carregar informações do evento: ' . $e->getMessage()]);
}
?>