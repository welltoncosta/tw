<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include("conexao.php");

try {
    // Buscar realizadores
    $stmt = $pdo->prepare("SELECT * FROM organizadores WHERE tipo = 'realizacao' ORDER BY nome");
    $stmt->execute();
    $realizadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar apoiadores
    $stmt = $pdo->prepare("SELECT * FROM organizadores WHERE tipo = 'apoio' ORDER BY nome");
    $stmt->execute();
    $apoiadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'realizadores' => $realizadores, 'apoiadores' => $apoiadores]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao carregar organização: ' . $e->getMessage()]);
}
?>