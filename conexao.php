<?php
// conexao.php - Versão corrigida

// Configurações de conexão com o banco de dados
$host = 'localhost';
$dbname = 'u686345830_techweek_utfpr';
$username = 'wellton';
$password = '123';

// Opções adicionais para o PDO
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
];

try {
    // Conectar ao banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, $options);
    
} catch (PDOException $e) {
    // Em caso de erro na conexão
    error_log("Erro de conexão: " . $e->getMessage());
    
    // Mensagem amigável para o usuário
    die(json_encode(['success' => false, 'message' => 'Erro ao conectar com o banco de dados.']));
}

// Definir timezone padrão
date_default_timezone_set('America/Sao_Paulo');

// Configurações de exibição de erros (apenas para desenvolvimento)
if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
}

// Retornar a conexão para ser usada em outros arquivos
return $pdo;
?>	
