<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');

// Verificar se o usuário está logado e é administrador
if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario']) || $_SESSION['usuario_administrador'] != 1) {
    header("location: index.html#login");
    exit;
}

// Incluir arquivo de conexão
include("conexao.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Buscar informações do backup
        $stmt = $pdo->prepare("SELECT * FROM backups WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $backup = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($backup && file_exists($backup['caminho_arquivo'])) {
            // Configurar headers para download
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($backup['caminho_arquivo']) . '"');
            header('Content-Length: ' . filesize($backup['caminho_arquivo']));
            readfile($backup['caminho_arquivo']);
            exit;
        } else {
            die('Arquivo de backup não encontrado.');
        }
    } catch (PDOException $e) {
        die('Erro ao buscar backup: ' . $e->getMessage());
    }
} else {
    die('ID do backup não especificado.');
}
?>