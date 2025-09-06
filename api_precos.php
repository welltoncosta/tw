<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include("conexao.php");

try {
    $stmt = $pdo->prepare("SELECT * FROM precos_inscricao WHERE ativo = 1 ORDER BY id");
    $stmt->execute();
    $precos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'precos' => $precos]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao carregar preços: ' . $e->getMessage()]);
}
?>