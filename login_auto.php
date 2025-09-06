<?php
session_start();
include("conexao.php");

// Verificar se o token foi fornecido
if (!isset($_GET['token']) || empty($_GET['token'])) {
    header("Location: index.html#login?erro=token_invalido");
    exit;
}

$token = $_GET['token'];

try {
    // Buscar usuário pelo token válido
    $stmt = $pdo->prepare("SELECT id, administrador, tipo_inscricao, lote_inscricao, nome, cpf, email, telefone, instituicao, data_cadastro FROM participantes WHERE token_recuperacao = :token AND expiracao_token < NOW()");
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->execute();
    
    if ($stmt->rowCount() === 1) {
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Iniciar sessão
        $_SESSION['usuario'] = $usuario;
        $_SESSION['acesso_recuperacao'] = true;
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_tipo_inscricao'] = $usuario['tipo_inscricao'];
        $_SESSION['usuario_lote_inscricao'] = $usuario['lote_inscricao'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_telefone'] = $usuario['telefone'];
        $_SESSION['usuario_administrador'] = $usuario['administrador'];
        $_SESSION['logado'] = true;
        
        // Invalidar o token após uso
        $stmt = $pdo->prepare("UPDATE participantes SET token_recuperacao = NULL, expiracao_token = NULL WHERE id = :id");
        $stmt->bindParam(':id', $usuario['id'], PDO::PARAM_INT);
        $stmt->execute();
        
        // Redirecionar para o painel
        header("Location: painel.php");
        exit;
    } else {
        // Token inválido ou expirado
        header("Location: index.html#login?erro=token_invalido");
        exit;
    }
} catch (PDOException $e) {
    error_log("Erro no login automático: " . $e->getMessage());
    header("Location: index.html#login?erro=interno");
    exit;
}
?>