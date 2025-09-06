<?php
session_start();

// Verificar se o usuário está logado e é administrador
if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario']) || $_SESSION['usuario']['administrador'] != 1) {
    header("location: index.html#login");
    exit;
}

// Incluir a função de geração de código de barras
include("barcode.php");

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => 'Ação não reconhecida'];
    
    // Verificar token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $response['message'] = 'Token de segurança inválido';
        echo json_encode($response);
        exit;
    }
    
    // Ação: Gerar código de barras
    if (isset($_POST['action']) && $_POST['action'] === 'gerar_codigo_barras') {
        $codigo = $_POST['codigo'];
        
        if (!empty($codigo)) {
            $imagemBarcode = generateBarcodeImage($codigo);
            if ($imagemBarcode) {
                $response['success'] = true;
                $response['imagem'] = $imagemBarcode;
            } else {
                $response['message'] = 'Erro ao gerar código de barras';
            }
        } else {
            $response['message'] = 'Código não fornecido';
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
