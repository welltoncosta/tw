<?php
session_start();

if(!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])) {
    header("location: index.html#login");
    exit;
}

// Verificar se o acesso veio do auto_login.php (recuperação de senha)
$acesso_recuperacao = isset($_SESSION['acesso_recuperacao']) ? $_SESSION['acesso_recuperacao'] : false;

// Remover a flag após uso para não afetar navegações futuras
unset($_SESSION['acesso_recuperacao']);

include("conexao.php");

// Recuperar dados do usuário
$usuario = $_SESSION['usuario'];

// Buscar informações sobre preços e lotes (apenas aprovados)
$stmt = $pdo->prepare("
    SELECT 
        p.tipo_inscricao,
        p.lote_inscricao,
        COALESCE(NULLIF(p.preco_inscricao, 0), pi.valor) as preco_inscricao,
        (SELECT COUNT(DISTINCT p2.id) 
         FROM participantes p2 
         INNER JOIN comprovantes c ON p2.id = c.participante_id 
         WHERE p2.tipo_inscricao = p.tipo_inscricao
         AND p2.lote_inscricao = '1' 
         AND c.status = 'aprovado') as inscricoes_lote1,
        (SELECT COUNT(DISTINCT p2.id) 
         FROM participantes p2 
         INNER JOIN comprovantes c ON p2.id = c.participante_id 
         WHERE p2.tipo_inscricao = p.tipo_inscricao
         AND p2.lote_inscricao = '2' 
         AND c.status = 'aprovado') as inscricoes_lote2
    FROM participantes p 
    LEFT JOIN precos_inscricao pi ON p.tipo_inscricao = pi.categoria AND pi.ativo = 1
    WHERE p.id = :id
    GROUP BY p.id
");

$stmt->execute([':id' => $usuario['id']]);
$info_inscricao = $stmt->fetch(PDO::FETCH_ASSOC);

// Determinar a categoria e texto explicativo
$categoria = $info_inscricao['tipo_inscricao'] ?? '';
$lote = $info_inscricao['lote_inscricao'] ?? '';
$preco = $info_inscricao['preco_inscricao'] ?? 0;
$inscricoes_lote1 = $info_inscricao['inscricoes_lote1'] ?? 0;
$inscricoes_lote2 = $info_inscricao['inscricoes_lote2'] ?? 0;

// Textos explicativos baseados na categoria
$textos_categoria = [
    'universitario_ti' => [
        'titulo' => 'Universitário de TI',
        'descricao' => 'Desconto especial para estudantes de cursos de Tecnologia da Informação'
    ],
    'ensino_medio' => [
        'titulo' => 'Estudante de Ensino Médio ou Técnico',
        'descricao' => 'Valor promocional para estudantes do ensino médio e técnico'
    ],
    'publico_geral' => [
        'titulo' => 'Público Geral',
        'descricao' => 'Inscrição para o público em geral'
    ]
];


// Para universitários de TI, verificar se precisa mudar para o lote 2
if ($categoria === 'universitario_ti' && $lote === '1' && $inscricoes_lote1 >= 50) {
    // Atualizar o participante para o lote 2 no banco de dados
    $stmt_update = $pdo->prepare("UPDATE participantes SET lote_inscricao = '2', preco_inscricao = 35.00 WHERE id = :id");
    $stmt_update->execute([':id' => $usuario['id']]);
    
    // Atualizar as variáveis locais
    $lote = '2';
    $preco = 35.00;
}

// Definir informações PIX baseadas no valor
$pix_info = [
    '15.00' => [
        'chave' => '00020126580014BR.GOV.BCB.PIX0136e739b15b-9bde-46d0-b2ad-a135fd5d4187520400005303986540515.005802BR5901N6001C62070503***630489E1',
        'imagem' => 'imagens/pix/15e9j9efjef9jeffmkdjf90ejf9jkfj.png'
    ],
    '25.00' => [
        'chave' => '00020126580014BR.GOV.BCB.PIX0136e739b15b-9bde-46d0-b2ad-a135fd5d4187520400005303986540525.005802BR5901N6001C62070503***6304F222',
        'imagem' => 'imagens/pix/25pm3pm43pm342p432mp342m234.png'
    ],
    '35.00' => [
        'chave' => '00020126580014BR.GOV.BCB.PIX0136e739b15b-9bde-46d0-b2ad-a135fd5d4187520400005303986540535.005802BR5901N6001C62070503***6304DB63',
        'imagem' => 'imagens/pix/35ff4f3f43f3f3f3ff3f3ff3.png'
    ],
    '50.00' => [
        'chave' => '00020126580014BR.GOV.BCB.PIX0136e739b15b-9bde-46d0-b2ad-a135fd5d4187520400005303986540550.005802BR5901N6001C62070503***6304C725',
        'imagem' => 'imagens/pix/50sdmfiosdfmdmdsfiomsdimdf.png'
    ]
];

// Formatar o preço para duas casas decimais
$preco_formatado = number_format($preco, 2, '.', '');

// Verificar se temos informação PIX para esse valor
$chave_pix = '';
$imagem_pix = '';
if (isset($pix_info[$preco_formatado])) {
    $chave_pix = $pix_info[$preco_formatado]['chave'];
    $imagem_pix = $pix_info[$preco_formatado]['imagem'];
}

// Texto personalizado para universitários de TI sobre lotes
if ($categoria === 'universitario_ti') {
    $vagas_restantes = max(0, 50 - $inscricoes_lote1);
    $texto_lote = "Lote $lote - " . ($lote === '1' ? 
        "$vagas_restantes vagas com desconto restantes!" : 
        "Valor normal após esgotamento das vagas promocionais");
} else {
    $texto_lote = "Valor único - Sem sistema de lotes";
}

// Buscar atividades disponíveis (apenas oficinas e workshops) com contagem de inscrições
$stmt = $pdo->prepare("
    SELECT a.*, 
           (SELECT COUNT(*) FROM inscricoes_atividades i WHERE i.atividade_id = a.id) as inscricoes
    FROM atividades a 
    WHERE a.ativa = '1' AND a.tipo IN ('oficina', 'workshop') 
    ORDER BY a.data, a.horario
");
$stmt->execute();
$todas_atividades = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar atividades em que o participante está inscrito
$stmt = $pdo->prepare("SELECT a.* FROM atividades a 
                      INNER JOIN inscricoes_atividades i ON a.id = i.atividade_id 
                      WHERE i.participante_id = :participante_id 
                      ORDER BY a.data, a.horario");
$stmt->execute([':participante_id' => $usuario['id']]);
$atividades_inscritas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar comprovantes do participante
$stmt = $pdo->prepare("SELECT * FROM comprovantes WHERE participante_id = :participante_id ORDER BY data_envio DESC");
$stmt->execute([':participante_id' => $usuario['id']]);
$comprovantes = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Verificar se existe comprovante aprovado
$comprovante_aprovado = false;
foreach ($comprovantes as $comp) {
    if ($comp['status'] == 'aprovado') {
        $comprovante_aprovado = true;
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='%2300FF00' d='M13 2.03v2.02c4.39.54 7.5 4.53 6.96 8.92c-.46 3.64-3.32 6.53-6.96 6.96v2c5.5-.55 9.5-5.43 8.95-10.93c-.45-4.75-4.22-8.5-8.95-8.97m-2 .03c-1.95.19-3.81.94-5.33 2.2L7.1 5.74c1.12-.9 2.47-1.48 3.9-1.68v-2M4.26 5.67A9.885 9.885 0 0 0 2.05 11h2c.19-1.42.75-2.77 1.64-3.9L4.26 5.67M2.06 13c.2 1.96.97 3.81 2.21 5.33l1.42-1.43A8.002 8.002 0 0 1 4.06 13h-2m5.04 5.37l-1.43 1.37A9.994 9.994 0 0 0 11 22v-2a8.002 8.002 0 0 1-3.9-1.63m9.33-12.37l-1.59 1.59L16 10l-4-4V3l-1 1l4 4l.47-.53l-1.53-1.53l1.59-1.59l.94.94z'/%3E%3C/svg%3E" type="image/svg+xml">
    <title>Painel do Participante - 1ª TechWeek</title>
    <link href='https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --black: #000000;
            --neon-green: #00FF00;
            --tech-green: #00BF63;
            --light-gray: #D9D9D9;
            --white: #FFFFFF;
            --dark-gray: #1A1A1A;
            --bg-color: #000000;
            --text-color: #FFFFFF;
            --card-bg: rgba(26, 26, 26, 0.7);
            --border-color: #00BF63;
            --accent-color: #00BF63;
            --accent-hover: #00FF00;
            --error-color: #ff4d4d;
            --success-color: #00cc66;
        }
        
        .light-theme {
            --bg-color: #f8f9fa;
            --text-color: #2d3748;
            --card-bg: rgba(255, 255, 255, 0.95);
            --border-color: #2d7d5a;
            --black: #ffffff;
            --light-gray: #718096;
            --dark-gray: #e2e8f0;
            --accent-color: #2d7d5a;
            --accent-hover: #38a169;
            --neon-green: #2d7d5a;
            --tech-green: #38a169;
            --error-color: #e53e3e;
            --success-color: #38a169;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
            transition: background-color 0.3s, color 0.3s, border-color 0.3s;
        }
        
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
            overflow-x: hidden;
            position: relative;
            font-size: 16px;
        }
        
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 10% 20%, rgba(0, 191, 99, 0.1) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(0, 255, 0, 0.1) 0%, transparent 20%);
            pointer-events: none;
            z-index: -1;
        }
        
        .light-theme body::before {
            background: 
                radial-gradient(circle at 10% 20%, rgba(45, 125, 90, 0.05) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(56, 161, 105, 0.05) 0%, transparent 20%);
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        /* Header */
        header {
            background-color: var(--black);
            padding: 12px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 0 15px rgba(0, 255, 0, 0.2);
        }
        
        .light-theme header {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .logo {
            display: flex;
            align-items: center;
        }
        
        .logo img {
            height: 100px;
            transition: all 0.3s ease;
        }
        
        .logo-dark {
            display: block;
        }
        
        .logo-light {
            display: none;
        }
        
        .light-theme .logo-dark {
            display: none;
        }
        
        .light-theme .logo-light {
            display: block;
        }
        
        .logo:hover img {
            filter: drop-shadow(0 0 8px var(--accent-color));
        }
        
        .event-title {
            margin-left: 12px;
        }
        
        .event-title h1 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-color);
            line-height: 1.3;
        }
        
        .event-title span {
            color: var(--accent-color);
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .header-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .theme-toggle {
            background: none;
            border: none;
            color: var(--accent-color);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .theme-toggle:hover {
            background-color: rgba(45, 125, 90, 0.15);
        }
        
        .user-menu {
            position: relative;
        }
        
        .user-btn {
            background: linear-gradient(45deg, var(--tech-green), var(--neon-green));
            color: var(--black);
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .light-theme .user-btn {
            background: linear-gradient(45deg, var(--accent-color), var(--accent-hover));
            color: white;
        }
        
        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 10px;
            min-width: 200px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            display: none;
            z-index: 100;
        }
        
        .user-dropdown.active {
            display: block;
        }
        
        .user-dropdown a {
            display: block;
            color: var(--text-color);
            text-decoration: none;
            padding: 10px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        
        .user-dropdown a:hover {
            background-color: rgba(0, 191, 99, 0.2);
            color: var(--neon-green);
        }
        
        .light-theme .user-dropdown a:hover {
            background-color: rgba(45, 125, 90, 0.15);
            color: var(--accent-color);
        }
        
        /* Menu */
        .menu-toggle {
            display: block;
            background: none;
            border: none;
            color: var(--accent-color);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 8px;
            border-radius: 4px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .menu-toggle:hover {
            background-color: rgba(45, 125, 90, 0.15);
        }
        
        .menu {
            display: none;
            flex-direction: column;
            width: 100%;
            list-style: none;
            background-color: var(--black);
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 100;
            padding: 10px 0;
            border-top: 1px solid var(--border-color);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.5);
        }
        
        .light-theme .menu {
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
        
        .menu.active {
            display: flex;
        }
        
        .menu li {
            width: 100%;
        }
        
        .menu a {
            color: var(--text-color);
            text-decoration: none;
            font-weight: 600;
            padding: 12px 20px;
            display: block;
            transition: all 0.3s ease;
            position: relative;
            font-size: 1.1rem;
        }
        
        .menu a:hover {
            background-color: rgba(45, 125, 90, 0.15);
            color: var(--accent-color);
        }
        
        .menu a.vermelho {
            color: var(--accent-color);
            font-weight: 700;
        }
        
        /* Main Content */
        .main-content {
            padding: 40px 0;
        }
        
        .welcome-section {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .welcome-section h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--neon-green);
        }
        
        .light-theme .welcome-section h1 {
            color: var(--accent-color);
        }
        
        .welcome-section p {
            font-size: 1.2rem;
            color: var(--light-gray);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .dashboard-cards {
            display: grid;
            grid-template-columns: 1fr;
            gap: 25px;
            margin-bottom: 40px;
        }
        
        @media (min-width: 768px) {
            .dashboard-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (min-width: 992px) {
            .dashboard-cards {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        .dashboard-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 0 15px rgba(0, 191, 99, 0.1);
        }
        
        .light-theme .dashboard-card {
            box-shadow: 0 0 15px rgba(45, 125, 90, 0.1);
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 25px rgba(0, 255, 0, 0.3);
            border-color: var(--neon-green);
        }
        
        .light-theme .dashboard-card:hover {
            box-shadow: 0 0 25px rgba(45, 125, 90, 0.2);
            border-color: var(--accent-color);
        }
        
        .dashboard-card i {
            font-size: 2.5rem;
            color: var(--neon-green);
            margin-bottom: 15px;
        }
        
        .light-theme .dashboard-card i {
            color: var(--accent-color);
        }
        
        .dashboard-card h3 {
            color: var(--text-color);
            margin-bottom: 12px;
            font-size: 1.4rem;
        }
        
        .dashboard-card p {
            color: var(--light-gray);
            margin-bottom: 20px;
        }
        
        .dashboard-card .btn {
            display: inline-block;
            background: linear-gradient(45deg, var(--tech-green), var(--neon-green));
            color: var(--black);
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .light-theme .dashboard-card .btn {
            background: linear-gradient(45deg, var(--accent-color), var(--accent-hover));
            color: white;
        }
        
        .dashboard-card .btn:hover {
            background: linear-gradient(45deg, var(--neon-green), var(--tech-green));
            box-shadow: 0 0 10px rgba(0, 255, 0, 0.5);
        }
        
        .light-theme .dashboard-card .btn:hover {
            background: linear-gradient(45deg, var(--accent-hover), var(--accent-color));
            box-shadow: 0 0 10px rgba(45, 125, 90, 0.3);
        }
        
        /* User Info Section */
        .user-info {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 40px;
            box-shadow: 0 0 15px rgba(0, 191, 99, 0.1);
        }
        
        .light-theme .user-info {
            box-shadow: 0 0 15px rgba(45, 125, 90, 0.1);
        }
        
        .user-info h2 {
            color: var(--neon-green);
            margin-bottom: 20px;
            font-size: 1.8rem;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--border-color);
        }
        
        .light-theme .user-info h2 {
            color: var(--accent-color);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }
        
        @media (min-width: 768px) {
            .info-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--neon-green);
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        
        .light-theme .info-label {
            color: var(--accent-color);
        }
        
        .info-value {
            color: var(--text-color);
            font-size: 1.1rem;
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--neon-green);
            font-weight: 600;
        }
        
        .light-theme .form-group label {
            color: var(--accent-color);
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            background-color: var(--card-bg);
            color: var(--text-color);
            font-size: 1rem;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--neon-green);
            box-shadow: 0 0 10px rgba(0, 255, 0, 0.3);
        }
        
        .light-theme .form-group input:focus,
        .light-theme .form-group select:focus,
        .light-theme .form-group textarea:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 10px rgba(45, 125, 90, 0.2);
        }
        
        .btn-primary {
            background: linear-gradient(45deg, var(--tech-green), var(--neon-green));
            color: var(--black);
            border: none;
            border-radius: 5px;
            padding: 12px 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .light-theme .btn-primary {
            background: linear-gradient(45deg, var(--accent-color), var(--accent-hover));
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, var(--neon-green), var(--tech-green));
            box-shadow: 0 0 10px rgba(0, 255, 0, 0.5);
        }
        
        .light-theme .btn-primary:hover {
            background: linear-gradient(45deg, var(--accent-hover), var(--accent-color));
            box-shadow: 0 0 10px rgba(45, 125, 90, 0.3);
        }
        
        /* Message Styles */
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .message.sucesso {
            background-color: rgba(0, 204, 102, 0.2);
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }
        
        .message.erro {
            background-color: rgba(255, 77, 77, 0.2);
            color: var(--error-color);
            border: 1px solid var(--error-color);
        }
        
        /* Table Styles */
        .table-container {
            background: var(--card-bg);
            border-radius: 10px;
            border: 1px solid var(--border-color);
            padding: 15px;
            box-shadow: 0 0 20px rgba(0, 191, 99, 0.2);
            overflow-x: auto;
            margin-bottom: 30px;
        }
        
        .light-theme .table-container {
            box-shadow: 0 0 20px rgba(45, 125, 90, 0.1);
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }
        
        .data-table th {
            background-color: rgba(0, 191, 99, 0.2);
            color: var(--neon-green);
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid var(--border-color);
            font-size: 1rem;
        }
        
        .light-theme .data-table th {
            background-color: rgba(45, 125, 90, 0.1);
            color: var(--accent-color);
        }
        
        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid rgba(217, 217, 217, 0.1);
            color: var(--light-gray);
            font-size: 0.95rem;
        }
        
        .light-theme .data-table td {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .data-table tr:last-child td {
            border-bottom: none;
        }
        
        .data-table tr:hover {
            background-color: rgba(0, 191, 99, 0.05);
        }
        
        .light-theme .data-table tr:hover {
            background-color: rgba(45, 125, 90, 0.03);
        }
        
        .verde {
            color: var(--neon-green);
        }
        
        .light-theme .verde {
            color: var(--accent-color);
        }
        
        .vermelho {
            color: var(--error-color);
        }
        
        /* Footer */
        footer {
            background: var(--black);
            padding: 30px 0 15px;
            border-top: 1px solid var(--border-color);
        }
        
        .light-theme footer {
            background: var(--dark-gray);
        }
        
        .footer-content {
            display: flex;
            flex-direction: column;
            gap: 25px;
            margin-bottom: 25px;
        }
        
        .footer-logo {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .footer-logo img {
            max-width: 130px;
            margin-bottom: 15px;
        }
        
        .footer-logo-dark {
            display: block;
        }
        
        .footer-logo-light {
            display: none;
        }
        
        .light-theme .footer-logo-dark {
            display: none;
        }
        
        .light-theme .footer-logo-light {
            display: block;
        }
        
        .footer-info {
            text-align: center;
        }
        
        .footer-info h3 {
            color: var(--neon-green);
            margin-bottom: 15px;
            font-size: 1.4rem;
        }
        
        .light-theme .footer-info h3 {
            color: var(--accent-color);
        }
        
        .footer-info p {
            margin-bottom: 8px;
            color: var(--light-gray);
            font-size: 0.95rem;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 15px;
            border-top: 1px solid rgba(0, 191, 99, 0.3);
            color: var(--light-gray);
            font-size: 0.85rem;
        }
        
        .light-theme .footer-bottom {
            border-top: 1px solid rgba(45, 125, 90, 0.2);
        }
        
        /* Estilos para as atividades */
        .atividade-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .atividade-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .atividade-title {
            font-size: 1.3rem;
            color: var(--neon-green);
            margin-bottom: 10px;
        }
        
        .light-theme .atividade-title {
            color: var(--accent-color);
        }
        
        .atividade-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        
        .atividade-meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            color: var(--light-gray);
        }
        
        .atividade-vagas {
            background: rgba(0, 191, 99, 0.2);
            color: var(--neon-green);
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .light-theme .atividade-vagas {
            background: rgba(45, 125, 90, 0.1);
            color: var(--accent-color);
        }
        
        .atividade-descricao {
            margin-bottom: 15px;
            color: var(--light-gray);
        }
        
        .atividade-actions {
            display: flex;
            justify-content: flex-end;
        }

        /* Tabs */
        .tabs {
            display: flex;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }
        
        .tab.active {
            border-bottom-color: var(--neon-green);
            color: var(--neon-green);
        }
        
        .light-theme .tab.active {
            border-bottom-color: var(--accent-color);
            color: var(--accent-color);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }

        /* Estilos para comprovantes */
        .comprovante-item {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .comprovante-info {
            flex: 1;
        }
        
        .comprovante-actions {
            display: flex;
            gap: 10px;
        }

        /* Preview Modal */
        .preview-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }
        
        .preview-content {
            background-color: var(--card-bg);
            border-radius: 10px;
            padding: 20px;
            max-width: 90%;
            max-height: 90%;
            overflow: auto;
            position: relative;
        }
        
        .preview-close {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--error-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.2rem;
        }
        
        .preview-body {
            margin-top: 20px;
        }
        
        .preview-body img {
            max-width: 100%;
            height: auto;
        }
        
        .preview-body iframe {
            width: 100%;
            height: 500px;
            border: none;
        }

        /* File Upload */
        .file-upload {
            border: 2px dashed var(--border-color);
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .file-upload:hover {
            border-color: var(--neon-green);
            background-color: rgba(0, 191, 99, 0.05);
        }
        
        .file-upload i {
            font-size: 2rem;
            color: var(--neon-green);
            margin-bottom: 10px;
        }
        
        .file-upload p {
            color: var(--light-gray);
            margin-bottom: 10px;
        }
        
        .file-name {
            margin-top: 10px;
            font-style: italic;
            color: var(--neon-green);
        }
        
        /* Botões de ação */
        .btn-edit {
            background: linear-gradient(45deg, var(--tech-green), var(--neon-green));
            color: var(--black);
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .light-theme .btn-edit {
            background: linear-gradient(45deg, var(--accent-color), var(--accent-hover));
            color: white;
        }
        
        .btn-edit:hover {
            background: linear-gradient(45deg, var(--neon-green), var(--tech-green));
            box-shadow: 0 0 10px rgba(0, 255, 0, 0.5);
        }
        
        .light-theme .btn-edit:hover {
            background: linear-gradient(45deg, var(--accent-hover), var(--accent-color));
            box-shadow: 0 0 10px rgba(45, 125, 90, 0.3);
        }
        
        /* Formulário de edição */
        .edit-form {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
        }
        
        .password-fields {
            display: none;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
        }
        
        .toggle-password {
            background: none;
            border: none;
            color: var(--neon-green);
            cursor: pointer;
            font-size: 0.9rem;
            margin-top: 10px;
            text-decoration: underline;
        }
        
        .light-theme .toggle-password {
            color: var(--accent-color);
        }

        /* Media Queries para tablets e desktops */
        @media (min-width: 768px) {
            .menu-toggle {
                display: none;
            }
            
            .menu {
                display: flex;
                flex-direction: row;
                position: static;
                width: auto;
                background: transparent;
                padding: 0;
                border: none;
                box-shadow: none;
            }
            
            .menu li {
                width: auto;
            }
            
            .menu a {
                padding: 8px 15px;
                font-size: 0.95rem;
            }
        }
        
        @media (min-width: 992px) {
            .footer-content {
                flex-direction: row;
                gap: 40px;
            }
            
            .footer-logo, .footer-info {
                text-align: left;
            }
        }

        
        @media (max-width: 768px) {
                .header-content {
                    flex-direction: column;
                    gap: 0px;
                }
                
                .logo {
                    margin-bottom: 10px;
                }
                
                .event-title {
                    margin-left: 0;
                    text-align: center;
                }
                
                .dashboard-cards {
                    grid-template-columns: 1fr;
                }
                
                .tabs {
                    flex-direction: column;
                }
                
                .data-table td {
                    padding-left: 45%;
                }
                
                .data-table td:before {
                    width: 40%;
                }
            }
            
            @media (max-width: 576px) {
                .data-table td {
                    padding-left: 50%;
                }
                
                .data-table td:before {
                    width: 45%;
                }
                
                .btn-primary {
                    width: 100%;
                    margin-bottom: 5px;
                }
            }

          /* Novos estilos para a seção de preços */
        .price-display {
            background: rgba(0, 191, 99, 0.1);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            text-align: center;
            font-weight: 600;
            color: var(--neon-green);            
        }
        
        .light-theme .price-display {
            background: rgba(45, 125, 90, 0.1);
            color: var(--accent-color);
        }
         .price-value {
            font-size: 1.4rem;
            font-weight: 700;
            margin-top: 5px;
        }
        
        .category-description {
            font-size: 0.9rem;
            color: var(--light-gray);
            margin-top: 5px;
        }

        .required-field::after {
            content: " *";
            color: var(--error-color);
        }

        /* Estilos para a seção de valor de inscrição */
        .valor-info-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 0 15px rgba(0, 191, 99, 0.1);
        }

        .light-theme .valor-info-card {
            box-shadow: 0 0 15px rgba(45, 125, 90, 0.1);
        }

        .valor-destaque {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--neon-green);
            text-align: center;
            margin: 15px 0;
        }

        .light-theme .valor-destaque {
            color: var(--accent-color);
        }

        .info-destaque {
            background: rgba(0, 191, 99, 0.1);
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            border-left: 4px solid var(--neon-green);
        }

        .light-theme .info-destaque {
            background: rgba(45, 125, 90, 0.1);
            border-left-color: var(--accent-color);
        }

        .atividade-tipo {
            display: inline-block;
            background: rgba(0, 191, 99, 0.2);
            color: var(--neon-green);
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-right: 10px;
        }
        
        .light-theme .atividade-tipo {
            background: rgba(45, 125, 90, 0.1);
            color: var(--accent-color);
        }
        
        .atividade-conflito {
            background-color: rgba(255, 77, 77, 0.1);
            border: 1px solid var(--error-color);
        }
        
        .atividade-conflito-message {
            color: var(--error-color);
            font-size: 0.9rem;
            margin-top: 10px;
            padding: 8px;
            background-color: rgba(255, 77, 77, 0.1);
            border-radius: 4px;
        }
        
        .btn-disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <img src="imagens/logo.jpg" alt="Logo Tech Week" class="logo-dark">
                    <img src="imagens/logo-light.jpg" alt="Logo Tech Week" class="logo-light">
                    <div class="event-title">
                        <h1>1ª TechWeek</h1>
                        <span>Painel do Participante</span>
                    </div>
                </div>
                
                <ul class="menu">
                    <li><a href="#dashboard">Dashboard</a></li>
                    <li><a href="#dados">Meus Dados</a></li>                    
                    <li><a href="#comprovantes-pix">Comprovantes</a></li>
                    <li><a href="#atividades">Atividades</a></li>
                    <li><a href="#certificado">Certificado</a></li>                    
                </ul>

                <div class="header-controls">
                    <button class="theme-toggle" id="themeToggle">
                        <i class="fas fa-moon"></i>
                    </button>
                    
                    <div class="user-menu">
                        <button class="user-btn">
                            <i class="fas fa-user"></i>
                            <?php echo explode(' ', $usuario['nome'])[0]; ?>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="user-dropdown" id="userDropdown">
                            <a href="#dados"><i class="fas fa-user-circle"></i> Meu Perfil</a>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
                        </div>
                    </div>
                    
                    <button class="menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Welcome Section -->
            <section class="welcome-section">
                <h1>Olá, <?php echo $usuario['nome']; ?>!</h1>
                <p>Bem-vindo(a) ao seu painel de participante da 1ª TechWeek. Aqui você pode gerenciar suas inscrições, verificar suas atividades e acessar seu certificado.</p>
            </section>
            
            <!-- Dashboard Cards -->
            <section class="dashboard-cards" id="dashboard">
                <div class="dashboard-card">
                    <i class="fas fa-user-circle"></i>
                    <h3>Meus Dados</h3>
                    <p>Visualize e gerencie suas informações pessoais</p>
                    <a href="#dados" class="btn">Acessar</a>
                </div>
                
               <div class="dashboard-card">
        <i class="fas fa-receipt"></i>
        <h3>Comprovantes</h3>
        <p>Envie e acompanhe seus comprovantes PIX</p>
        <a href="#comprovantes-pix" class="btn">Ver Comprovantes</a>
    </div>
                
                <div class="dashboard-card">
                    <i class="fas fa-calendar-alt"></i>
                    <h3>Minhas Atividades</h3>
                    <p>Consulte as atividades nas quais você está inscrito(a)</p>
                    <a href="#atividades" class="btn">Ver Atividades</a>
                </div>
                
                <div class="dashboard-card">
                    <i class="fas fa-certificate"></i>
                    <h3>Certificado</h3>
                    <p>Acesse e baixe seu certificado de participação</p>
                    <a href="#certificado" class="btn">Obter Certificado</a>
                </div>
            </section>
            
            <!-- User Info Section -->
            <section class="user-info" id="dados">
                <div  style="display: flex; justify-content: space-between; align-items: center;">
                    <h2>Meus Dados Pessoais</h2>
                    <button class="btn-edit" id="btnEditDados">
                        <i class="fas fa-edit"></i> Editar Dados
                    </button>
                </div>
                
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Nome Completo</span>
                        <span class="info-value" id="info-nome"><?php echo htmlspecialchars($usuario['nome']); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">E-mail</span>
                        <span class="info-value" id="info-email"><?php echo htmlspecialchars($usuario['email']); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">CPF</span>
                        <span class="info-value" id="info-cpf"><?php echo !empty($usuario['cpf']) ? htmlspecialchars($usuario['cpf']) : 'Não informado'; ?></span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">Telefone</span>
                        <span class="info-value" id="info-telefone"><?php echo !empty($usuario['telefone']) ? htmlspecialchars($usuario['telefone']) : 'Não informado'; ?></span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">Instituição</span>
                        <span class="info-value" id="info-instituicao"><?php echo !empty($usuario['instituicao']) ? htmlspecialchars($usuario['instituicao']) : 'Não informado'; ?></span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">Data de Inscrição</span>
                        <span class="info-value" id="info-data-cadastro"><?php echo date('d/m/Y', strtotime($usuario['data_cadastro'])); ?></span>
                    </div>
                </div>
              
                
                <!-- Formulário de Edição -->
                 <div class="edit-form" id="editForm">
                    <h3 style="color: var(--neon-green); margin-bottom: 20px;">Editar Dados Pessoais</h3>
                    
                    <form id="formEditarDados">
                        <div class="form-group">
                            <label for="edit_nome" class="required-field">Nome Completo</label>
                            <input type="text" id="edit_nome" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_email" class="required-field">E-mail</label>
                            <input type="email" id="edit_email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="cpf" class="required-field">CPF</label>
                            <input type="text" id="cpf" name="cpf" required maxlength="14" value="<?php echo !empty($usuario['cpf']) ? htmlspecialchars($usuario['cpf']) : ''; ?>" disabled> * não pode ser editado
                        </div>
                    
                        <div class="form-group">
                            <label for="edit_telefone">Telefone</label>
                            <input type="text" id="edit_telefone" name="telefone" value="<?php echo !empty($usuario['telefone']) ? htmlspecialchars($usuario['telefone']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_instituicao" class="required-field">Instituição</label>
                            <input type="text" id="edit_instituicao" name="instituicao" value="<?php echo htmlspecialchars($usuario['instituicao']); ?>" required>
                        </div>
                        
                        <?php if (!$acesso_recuperacao): ?>
                        <button type="button" class="toggle-password" id="togglePassword">
                            <i class="fas fa-key"></i> Alterar senha
                        </button>
                        <?php else: ?>
                        <h4 style="color: var(--neon-green); margin: 20px 0 10px;">Alterar Senha</h4>
                        <p style="color: var(--light-gray); margin-bottom: 15px;">Como você acessou via recuperação de senha, defina uma nova senha abaixo:</p>
                        
                        <div class="password-fields" id="passwordFields" style="<?php echo $acesso_recuperacao ? 'display: block;' : ''; ?>">
                            
                            <div class="form-group">
                                <label for="edit_nova_senha" class="required-field">Nova Senha</label>
                                <input type="password" id="edit_nova_senha" name="nova_senha">
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_confirmar_senha" class="required-field">Confirmar Nova Senha</label>
                                <input type="password" id="edit_confirmar_senha" name="confirmar_senha">
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="password-fields" id="passwordFields" style="<?php echo $acesso_recuperacao ? 'display: block;' : ''; ?>">
                            <?php if (!$acesso_recuperacao): ?>
                            <div class="form-group">
                                <label for="edit_senha_atual" class="required-field">Senha Atual</label>
                                <input type="password" id="edit_senha_atual" name="senha_atual">
                            </div>
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <label for="edit_nova_senha" class="required-field">Nova Senha</label>
                                <input type="password" id="edit_nova_senha" name="nova_senha">
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_confirmar_senha" class="required-field">Confirmar Nova Senha</label>
                                <input type="password" id="edit_confirmar_senha" name="confirmar_senha">
                            </div>
                        </div>
                        
                        <div style="display: flex; gap: 10px; margin-top: 20px;">
                            <button type="submit" class="btn-primary">Salvar Alterações</button>
                            <button type="button" class="btn-primary" style="background: linear-gradient(45deg, var(--error-color), #ff6b6b);" id="btnCancelEdit">Cancelar</button>
                        </div>
                    </form>
                    <div id="messageEditarDados" style="margin-top: 15px;"></div>
                </div>
            </section>
            

        <?php if ($comprovante_aprovado): ?>
            <!-- Mensagem de pagamento processado -->
            <section id="pagamento-aprovado" style="margin-top: 40px;">
                <div class="container">
                    <div class="user-info">
                        <div style="text-align: center; padding: 40px;">
                            <i class="fas fa-check-circle" style="font-size: 4rem; color: var(--success-color); margin-bottom: 20px;"></i>
                            <h2 style="color: var(--success-color); margin-bottom: 20px;">Pagamento Processado com Sucesso!</h2>
                            <p style="font-size: 1.2rem; margin-bottom: 15px;">Seu pagamento foi confirmado e sua inscrição está ativa.</p>
                            <p style="font-size: 1.2rem; margin-bottom: 15px;">Aguarde a liberação das oficinas para você se inscrever em quantas quiser e puder.</p>
                            <p style="font-size: 1.2rem;">Agradecemos sua participação na <strong>1ª TechWeek</strong>!</p>
                        </div>
                    </div>
                </div>
            </section>
        <?php else: ?>
        <!-- Valor da Inscrição Section -->
            <section id="valor-inscricao" style="margin-top: 20px;">
                <h2 style="color: var(--neon-green); margin-bottom: 20px; font-size: 1.8rem;">VALOR DA INSCRIÇÃO</h2>
                
                <div class="user-info">
                    <div style="display: flex; flex-wrap: wrap; gap: 30px;">
                        <!-- Coluna da Esquerda - Informações da Inscrição -->
                        <div style="flex: 1; min-width: 300px;">
                            <div class="info-grid">
                                <div class="info-item">
                                    <span class="info-label">Categoria de Inscrição</span>
                                    <span class="info-value">
                                        <?php 
                                        if (!empty($categoria) && isset($textos_categoria[$categoria])) {
                                            echo htmlspecialchars($textos_categoria[$categoria]['titulo']);
                                        } else {
                                            echo 'Não especificada';
                                        }
                                        ?>
                                    </span>
                                </div>
                                
                                <div class="info-item">
                                    <span class="info-label">Lote</span>
                                    <span class="info-value"><?php echo !empty($lote) ? "Lote $lote" : 'Não definido'; ?></span>
                                </div>
                                
                                <div class="info-item">
                                    <span class="info-label">Valor da Inscrição</span>
                                    <span class="info-value" style="font-size: 1.4rem; font-weight: 700; color: var(--neon-green);">
                                        R$ <?php echo number_format($preco, 2, ',', '.'); ?>
                                    </span>
                                </div>
                                
                                <div class="info-item" style="grid-column: 1 / -1;">
                                    <span class="info-label">Informações</span>
                                    <span class="info-value">
                                        <?php
                                        if (!empty($categoria) && isset($textos_categoria[$categoria])) {
                                            echo htmlspecialchars($textos_categoria[$categoria]['descricao']);
                                        }
                                        
                                        if ($categoria === 'universitario_ti') {
                                            echo "<br><br>";
                                            echo "<strong>Sistema de Lotes para Universitários de TI:</strong><br>";
                                            echo "- Lote 1: R$ 25,00 (50 primeiras inscrições pagas)<br>";
                                            echo "- Lote 2: R$ 35,00 (após esgotamento do lote 1)<br><br>";
                                            
                                            if ($lote === '1') {
                                                $vagas_restantes = max(0, 50 - $inscricoes_lote1);
                                                echo "Vagas restantes no Lote 1: <strong>$vagas_restantes</strong>";
                                            } else {
                                                echo "As 50 vagas do Lote 1 foram preenchidas. Você está no Lote 2.";
                                            }
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Coluna da Direita - Informações do PIX -->
                        <?php if ($chave_pix && $imagem_pix): ?>
                        <div style="flex: 1; min-width: 300px;">
                            <div class="pix-section" style="padding: 20px; background: rgba(0, 191, 99, 0.1); border-radius: 8px; border-left: 4px solid var(--neon-green); height: 100%;">
                                <h4 style="color: var(--neon-green); margin-bottom: 15px; text-align: center;">💳 Pagamento via PIX</h4>
                                
                                <div style="text-align: center; margin-bottom: 20px;">
                                    <img src="<?php echo $imagem_pix; ?>" alt="QR Code PIX" style="max-width: 100%; max-height: 200px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.2);">
                                    <p style="margin-top: 10px; font-size: 0.9rem; color: var(--light-gray);">Escaneie o QR Code para pagar</p>
                                </div>
                                
                                <div>
                                    <p style="font-weight: 600; color: var(--neon-green); margin-bottom: 8px; text-align: center;">Chave PIX:</p>
                                    <div style="background: rgba(0,0,0,0.2); padding: 12px; border-radius: 5px; margin-bottom: 15px;">
                                        <p id="pix-key" style="word-break: break-all; font-family: monospace; margin: 0; color: var(--light-gray); font-size: 0.9rem; text-align: center;">
                                            <?php echo trim($chave_pix); ?>
                                        </p>
                                    </div>
                                    <div style="text-align: center;">
                                        <button onclick="copiarChavePIX()" style="padding: 10px 15px; background: linear-gradient(45deg, var(--tech-green), var(--neon-green)); color: var(--black); border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">
                                            <i class="fas fa-copy"></i> Copiar Chave PIX
                                        </button>
                                    </div>
                                </div>
                                
                                <div style="margin-top: 20px; padding: 15px; background: rgba(0,0,0,0.1); border-radius: 5px;">
                                    <p style="margin: 0; color: var(--light-gray); font-size: 0.9rem; text-align: center;">
                                        <strong>Instruções:</strong> Após realizar o pagamento PIX, não se esqueça de enviar o comprovante na seção "Comprovantes PIX" abaixo.
                                    </p>
                                    </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div style="margin-top: 20px; padding: 15px; background: rgba(0, 191, 99, 0.1); border-radius: 8px; border-left: 4px solid var(--neon-green);">
                        <h4 style="color: var(--neon-green); margin-bottom: 10px;">💡 Como funciona o sistema de valores?</h4>
                        <p style="margin: 0; color: var(--light-gray); font-size: 0.95rem;">
                            <?php
                            if ($categoria === 'universitario_ti') {
                                echo "Para universitários de TI, oferecemos um sistema de lotes com desconto progressivo. 
                                Os primeiros 50 inscritos que efetuarem o pagamento garantem o valor promocional do Lote 1. 
                                Após isso, o valor passa para o Lote 2. Sua categoria garante o valor no momento do pagamento do comprovante.";
                            } else {
                                echo "Sua categoria tem valor fixo, sem sistema de lotes. O valor da sua inscrição já está definido e 
                                não sofrerá alterações independentemente do momento do pagamento.";
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </section>

            <script>
            function copiarChavePIX() {
    // Pegar o texto exato da chave PIX (sem espaços extras)
    const chavePix = document.getElementById('pix-key').textContent.replace(/\s+/g, '');
    
    // Usando a API moderna de clipboard
    navigator.clipboard.writeText(chavePix).then(() => {
        alert('Chave PIX copiada com sucesso!');
    }).catch(err => {
        console.error('Erro ao copiar chave PIX:', err);
        // Fallback para método antigo se necessário
        const textArea = document.createElement('textarea');
        textArea.value = chavePix;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            alert('Chave PIX copiada com sucesso!');
        } catch (fallbackErr) {
            console.error('Erro ao copiar chave PIX (fallback):', fallbackErr);
            alert('Erro ao copiar chave PIX. Tente selecionar e copiar manualmente.');
        }
        document.body.removeChild(textArea);
    });
}
            </script>
            
            <!-- Comprovantes Section -->
            <section id="comprovantes-pix" style="margin-top: 40px;">
                <h2 style="color: var(--neon-green); margin-bottom: 40px; font-size: 1.8rem;">Comprovantes PIX</h2>
                
                <div class="user-info">
                    <h3>Enviar Comprovante</h3>
                    <form id="formComprovante" method="POST" enctype="multipart/form-data">
                        <div class="file-upload" id="fileUploadArea">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Clique para selecionar or arraste um arquivo</p>
                            <p>Aceitamos arquivos PDF, JPG, PNG ou GIF (até 5MB)</p>
                            <input type="file" name="comprovante" id="comprovante" accept=".jpg,.jpeg,.png,.gif,.pdf" style="display: none;" required>
                            <div id="fileName" class="file-name"></div>
                        </div>
                        
                        <button type="submit" class="btn-primary">Enviar Comprovante</button>
                    </form>
                    <div id="messageComprovante"></div>
                </div>
                
                <h3 style="color: var(--neon-green); margin: 30px 0 20px; font-size: 1.5rem;">Meus Comprovantes</h3>
                <div id="comprovantesList">
                    <?php if (count($comprovantes) > 0): ?>
                        <?php foreach ($comprovantes as $comprovante): ?>
                        <div class="comprovante-item">
                            <div class="comprovante-info">
                                <h4>Comprovante enviado em <?php echo date('d/m/Y H:i', strtotime($comprovante['data_envio'])); ?></h4>
                                <div class="verification-status">
                                    <span class="status-icon">
                                        <?php if ($comprovante['status'] == 'aprovado'): ?>
                                            <i class="fas fa-check-circle" style="color: var(--success-color);"></i>
                                        <?php elseif ($comprovante['status'] == 'excluido'): ?>
                                            <i class="fas fa-times-circle" style="color: var(--error-color);"></i>
                                        <?php elseif ($comprovante['status'] == 'rejeitado'): ?>
                                            <i class="fas fa-times-circle" style="color: var(--error-color);"></i>
                                        <?php else: ?>
                                            <i class="fas fa-clock" style="color: #ffc107;"></i>
                                        <?php endif; ?>
                                    </span>
                                    <span>
                                        Status: 
                                        <?php if ($comprovante['status'] == 'aprovado'): ?>
                                            <span class="verde">Aprovado</span>
                                        <?php elseif ($comprovante['status'] == 'excluido'): ?>
                                            <span class="vermelho">Excluído</span>
                                        <?php elseif ($comprovante['status'] == 'rejeitado'): ?>
                                            <span class="vermelho">Rejeitado</span>
                                            <?php if (!empty($comprovante['observacao'])): ?>
                                                <br><small>Motivo: <?php echo htmlspecialchars($comprovante['observacao']); ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span style="color: #ffc107;">Pendente</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="comprovante-actions">
                                <button class="btn-primary btn-view-comprovante" data-file="<?php echo $comprovante['arquivo']; ?>" data-type="<?php echo $comprovante['tipo_arquivo']; ?>">
                                    <i class="fas fa-eye"></i> Visualizar
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="message">
                            Você ainda não enviou nenhum comprovante.
                        </div>
                    <?php endif; ?>
                </div>
            </section>
<?php endif; ?>


            
            
             <!-- Atividades Section -->
            <section id="atividades" style="margin-top: 40px;">
                <h2 style="color: var(--neon-green); margin-bottom: 20px; font-size: 1.8rem;">Atividades da TechWeek</h2>
                
                <div id="messageAtividade" style="margin-bottom: 20px;"></div>
                
                <div class="tabs">
                    <div class="tab active" data-tab="todas">Todas as Atividades</div>
                    <div class="tab" data-tab="minhas">Minhas Inscrições</div>
                </div>
                
                <div class="tab-content active" id="todas-atividades">
                    <?php if (count($todas_atividades) > 0): ?>
                        <?php foreach ($todas_atividades as $atividade): 
                            $vagas_restantes = $atividade['vagas'] - $atividade['inscricoes'];
                        ?>
                        <div class="atividade-card" id="atividade-<?php echo $atividade['id']; ?>" 
                             data-id="<?php echo $atividade['id']; ?>"
                             data-data="<?php echo $atividade['data']; ?>"
                             data-hora-inicio="<?php echo $atividade['hora_inicio']; ?>"
                             data-hora-fim="<?php echo $atividade['hora_fim']; ?>">
                            <div class="atividade-header">
                                <h3 class="atividade-title">
                                    <span class="atividade-tipo"><?php echo ucfirst($atividade['tipo']); ?></span>
                                    <?php echo htmlspecialchars($atividade['titulo']); ?>
                                </h3>
                                <div class="atividade-vagas">
                                    <?php echo $vagas_restantes > 0 ? $vagas_restantes . ' vagas restantes' : 'Esgotada'; ?>
                                </div>
                            </div>
                            
                            <div class="atividade-meta">
                                <div class="atividade-meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo date('d/m/Y', strtotime($atividade['data'])); ?>
                                </div>
                                <div class="atividade-meta-item">
                                    <i class="fas fa-clock"></i>
                                    <?php echo substr($atividade['hora_inicio'], 0, 5) . ' - ' . substr($atividade['hora_fim'], 0, 5); ?>
                                </div>
                                <div class="atividade-meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($atividade['sala']); ?>
                                </div>
                            </div>
                            
                            <?php if (!empty($atividade['palestrante'])): ?>
                            <div class="atividade-descricao">
                                <strong>Palestrante:</strong> <?php echo htmlspecialchars($atividade['palestrante']); ?>
                            </div>
                            <?php endif; ?>
                            
                            <div class="atividade-actions">                        
                                <?php
                                $ja_inscrito = false;
                                foreach ($atividades_inscritas as $inscrita) {
                                    if ($inscrita['id'] == $atividade['id']) {
                                        $ja_inscrito = true;
                                        break;
                                    }
                                }
                                
                                if ($ja_inscrito): ?>
                                    <button class="btn-primary btn-cancelar-atividade" data-atividade-id="<?php echo $atividade['id']; ?>" style="background: linear-gradient(45deg, var(--error-color), #ff6b6b);">
                                        <i class="fas fa-times"></i> Cancelar Inscrição
                                    </button>
                                <?php else: ?>
                                    <button class="btn-primary btn-inscrever-atividade <?php echo $vagas_restantes <= 0 ? 'btn-disabled' : ''; ?>" 
                                            data-atividade-id="<?php echo $atividade['id']; ?>"
                                            <?php echo $vagas_restantes <= 0 ? 'disabled' : ''; ?>>
                                        <i class="fas fa-plus"></i> Inscrever-se
                                    </button>
                                <?php endif; ?>
                            </div>
                            
                            <div class="atividade-conflito-message" id="conflito-<?php echo $atividade['id']; ?>" style="display: none;"></div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="message">
                            Não há atividades disponíveis no momento.
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="tab-content" id="minhas-atividades">
                    <?php if (count($atividades_inscritas) > 0): ?>
                        <?php foreach ($atividades_inscritas as $atividade): ?>
                        <div class="atividade-card">
                            <div class="atividade-header">
                                <h3 class="atividade-title">
                                    <span class="atividade-tipo"><?php echo ucfirst($atividade['tipo']); ?></span>
                                    <?php echo htmlspecialchars($atividade['titulo']); ?>
                                </h3>
                                <div class="atividade-vagas">
                                    Inscrito
                                </div>
                            </div>
                            
                            <div class="atividade-meta">
                                <div class="atividade-meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo date('d/m/Y', strtotime($atividade['data'])); ?>
                                </div>
                                <div class="atividade-meta-item">
                                    <i class="fas fa-clock"></i>
                                    <?php echo substr($atividade['hora_inicio'], 0, 5) . ' - ' . substr($atividade['hora_fim'], 0, 5); ?>
                                </div>
                                <div class="atividade-meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($atividade['sala']); ?>
                                </div>
                            </div>
                            
                            <?php if (!empty($atividade['palestrante'])): ?>
                            <div class="atividade-descricao">
                                <strong>Palestrante:</strong> <?php echo htmlspecialchars($atividade['palestrante']); ?>
                            </div>
                            <?php endif; ?>
                            
                            <div class="atividade-actions">
                                <button class="btn-primary btn-cancelar-atividade" data-atividade-id="<?php echo $atividade['id']; ?>" style="background: linear-gradient(45deg, var(--error-color), #ff6b6b);">
                                    <i class="fas fa-times"></i> Cancelar Inscrição
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="message">
                            Você ainda não se inscreveu em nenhuma atividade.
                        </div>
                    <?php endif; ?>
                </div>
            </section>
            
            <!-- Certificado Section -->
            <section id="certificado" style="margin-top: 40px;">
                <h2 style="color: var(--neon-green); margin-bottom: 20px; font-size: 1.8rem;">Certificado de Participação</h2>
                
                <div class="dashboard-card">
                    <i class="fas fa-certificate" style="font-size: 3rem;"></i>
                    <h3>Certificado da 1ª TechWeek</h3>
                    <p>Seu certificado de participação estará disponível para download após o término do evento.</p>
                    <button class="btn" style="opacity: 0.7; cursor: not-allowed;">Disponível em Breve</button>
                </div>
            </section>
        </div>
    </main>

    <!-- Preview Modal -->
    <div class="preview-modal" id="previewModal">
        <div class="preview-content">
            <button class="preview-close" id="previewClose">
                <i class="fas fa-times"></i>
            </button>
            <h3>Visualizar Comprovante</h3>
            <div class="preview-body" id="previewBody">
                <!-- Conteúdo será inserido via JavaScript -->
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <img src="imagens/logo.jpg" alt="Tech Week" class="footer-logo-dark">
                    <img src="imagens/logo-light.jpg" alt="Tech Week" class="footer-logo-light">
                    <p style="color: var(--light-gray);">1ª TechWeek</p>
                    <p style="color: var(--tech-green); font-weight: 600; margin-top: 5px;">Semana Acadêmica de Tecnologia e Inovação</p>
                </div>
                
                <div class="footer-info">
                    <h3>Informações</h3>
                    <p><strong>Data:</strong> 28 a 31 de Outubro de 2025</p>
                    <p><strong>Horário:</strong> 14:00 às 22:00</p>
                    <p><strong>Locais:</strong> UTFPR e Teatro Municipal</p>
                    <p>Organização: UTFPR, CASIS, TypeX, CESUL</p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>Implementado por Wellton Costa de Oliveira</p>
            <p>Identidade Visual por David Junior</p>
            <p>&copy; 2025 1ª TechWeek - Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        // Menu mobile
        const menuToggle = document.querySelector('.menu-toggle');
        const menu = document.querySelector('.menu');
        
        menuToggle.addEventListener('click', () => {
            menu.classList.toggle('active');
        });
        
        // User dropdown
        const userBtn = document.querySelector('.user-btn');
        const userDropdown = document.getElementById('userDropdown');
        
        userBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('active');
        });
        
        // Fechar dropdown ao clicar fora
        document.addEventListener('click', (e) => {
            if (!userBtn.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.remove('active');
            }
        });
        
        // Sistema de tema claro/escuro
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = themeToggle.querySelector('i');
        const body = document.body;
        
        // Verificar preferência salva no localStorage
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'light') {
            body.classList.add('light-theme');
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        }
        
        themeToggle.addEventListener('click', function() {
            body.classList.toggle('light-theme');
            
            if (body.classList.contains('light-theme')) {
                localStorage.setItem('theme', 'light');
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            } else {
                localStorage.setItem('theme', 'dark');
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            }
        });
        
        // Suavizar rolagem para âncoras
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    const headerHeight = document.querySelector('header').offsetHeight;
                    window.scrollTo({
                        top: targetElement.offsetTop - headerHeight,
                        behavior: 'smooth'
                    });
                    
                    // Fechar menu mobile após clicar em um link
                    if (menu.classList.contains('active')) {
                        menu.classList.remove('active');
                    }
                    
                    // Fechar dropdown do usuário
                    userDropdown.classList.remove('active');
                }
            });
        });
        
        // Upload de comprovante
        const fileUploadArea = document.getElementById('fileUploadArea');
        const fileInput = document.getElementById('comprovante');
        const fileName = document.getElementById('fileName');
        const formComprovante = document.getElementById('formComprovante');
        const messageComprovante = document.getElementById('messageComprovante');
        
        
        if (fileUploadArea) {
            fileUploadArea.addEventListener('click', () => {
                fileInput.click();
            });
            
            fileInput.addEventListener('change', () => {
                if (fileInput.files.length > 0) {
                    fileName.textContent = fileInput.files[0].name;
                } else {
                    fileName.textContent = '';
                }
            });
            
            // Arrastar e soltar arquivo
            fileUploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                fileUploadArea.style.borderColor = 'var(--neon-green)';
                fileUploadArea.style.backgroundColor = 'rgba(0, 191, 99, 0.1)';
            });
            
            fileUploadArea.addEventListener('dragleave', () => {
                fileUploadArea.style.borderColor = 'var(--border-color)';
                fileUploadArea.style.backgroundColor = 'transparent';
            });
            
            fileUploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                fileUploadArea.style.borderColor = 'var(--border-color)';
                fileUploadArea.style.backgroundColor = 'transparent';
                
                if (e.dataTransfer.files.length > 0) {
                    fileInput.files = e.dataTransfer.files;
                    fileName.textContent = e.dataTransfer.files[0].name;
                }
            });
            
            // Envio do formulário via AJAX
            formComprovante.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('upload_comprovante.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        messageComprovante.innerHTML = `<div class="message sucesso">${data.message}</div>`;
                        
                        // Adicionar o novo comprovante à lista
                        const comprovantesList = document.getElementById('comprovantesList');
                        const comprovanteItem = document.createElement('div');
                        comprovanteItem.className = 'comprovante-item';
                        comprovanteItem.innerHTML = `
                            <div class="comprovante-info">
                                <h4>Comprovante enviado em ${new Date().toLocaleString('pt-BR')}</h4>
                                <div class="verification-status">
                                    <span class="status-icon">
                                        <i class="fas fa-clock" style="color: #ffc107;"></i>
                                    </span>
                                    <span>
                                        Status: <span style="color: #ffc107;">Pendente</span>
                                    </span>
                                </div>
                            </div>
                            <div class="comprovante-actions">
                                <button class="btn-primary btn-view-comprovante" data-file="${data.file_path}" data-type="${data.file_type}">
                                    <i class="fas fa-eye"></i> Visualizar
                                </button>
                            </div>
                        `;
                        
                        // Adicionar evento de visualização ao novo botão
                        comprovanteItem.querySelector('.btn-view-comprovante').addEventListener('click', viewComprovanteHandler);
                        
                        // Adicionar à lista
                        if (comprovantesList.querySelector('.message')) {
                            comprovantesList.innerHTML = '';
                        }
                        comprovantesList.prepend(comprovanteItem);
                        
                        // Limpar o formulário
                        formComprovante.reset();
                        fileName.textContent = '';
                    } else {
                        messageComprovante.innerHTML = `<div class="message erro">${data.message}</div>`;
                    }
                })
                .catch(error => {
                    messageComprovante.innerHTML = `<div class="message erro">Erro ao enviar comprovante: ${error}</div>`;
                });
            });
            
            // Visualizar comprovante
            const previewModal = document.getElementById('previewModal');
            const previewClose = document.getElementById('previewClose');
            const previewBody = document.getElementById('previewBody');
            
            const viewComprovanteHandler = function() {
                const file = this.getAttribute('data-file');
                const type = this.getAttribute('data-type');
                
                previewBody.innerHTML = '';
                
                if (type === 'pdf') {
                    previewBody.innerHTML = `<iframe src="${file}"></iframe>`;
                } else {
                    previewBody.innerHTML = `<img src="${file}" alt="Comprovante">`;
                }
                
                previewModal.style.display = 'flex';
            };
            
            // Adicionar evento a todos os botões de visualização
            document.querySelectorAll('.btn-view-comprovante').forEach(button => {
                button.addEventListener('click', viewComprovanteHandler);
            });
            
            previewClose.addEventListener('click', () => {
                previewModal.style.display = 'none';
            });
            
            previewModal.addEventListener('click', (e) => {
                if (e.target === previewModal) {
                    previewModal.style.display = 'none';
                }
            });
        }

        // Tabs de atividades
        const tabs = document.querySelectorAll('.tab');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const tabId = tab.getAttribute('data-tab');
                
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(tc => tc.classList.remove('active'));
                
                tab.classList.add('active');
                document.getElementById(`${tabId}-atividades`).classList.add('active');
            });
        });
        
        // Array para armazenar as atividades inscritas com seus horários
        const atividadesInscritas = [];
        
        <?php foreach ($atividades_inscritas as $atividade): ?>
        atividadesInscritas.push({
            id: <?php echo $atividade['id']; ?>,
            data: '<?php echo $atividade['data']; ?>',
            hora_inicio: '<?php echo $atividade['hora_inicio']; ?>',
            hora_fim: '<?php echo $atividade['hora_fim']; ?>',
            titulo: '<?php echo addslashes($atividade['titulo']); ?>'
        });
        <?php endforeach; ?>
        
        // Função para verificar conflito de horários
        function temConflito(novaAtividade, atividadesInscritas) {
            for (const atividade of atividadesInscritas) {
                // Se for no mesmo dia
                if (novaAtividade.data === atividade.data) {
                    // Verificar se os horários se sobrepõem
                    const novaInicio = novaAtividade.hora_inicio;
                    const novaFim = novaAtividade.hora_fim;
                    const inicio = atividade.hora_inicio;
                    const fim = atividade.hora_fim;
                    
                    // Converte para minutos para facilitar a comparação
                    function toMinutes(timeStr) {
                        const [h, m] = timeStr.split(':').map(Number);
                        return h * 60 + m;
                    }
                    
                    const novaInicioMin = toMinutes(novaInicio);
                    const novaFimMin = toMinutes(novaFim);
                    const inicioMin = toMinutes(inicio);
                    const fimMin = toMinutes(fim);
                    
                    // Verifica se há sobreposição
                    if (novaInicioMin < fimMin && novaFimMin > inicioMin) {
                        return atividade; // Retorna a atividade conflitante
                    }
                }
            }
            return false;
        }
        
        // Função para verificar e atualizar conflitos
      // Função para verificar e atualizar conflitos - CORRIGIDA
function atualizarConflitos() {
    document.querySelectorAll('.atividade-card').forEach(card => {
        const atividadeId = card.dataset.id;
        const messageElement = document.getElementById(`conflito-${atividadeId}`);
        
        // Verificar se o elemento existe antes de acessá-lo
        if (!messageElement) return;
        
        // Se já está inscrito, não precisa verificar conflito
        if (atividadesInscritas.some(a => a.id == atividadeId)) {
            messageElement.style.display = 'none';
            return;
        }
        
        const atividade = {
            id: card.dataset.id,
            data: card.dataset.data,
            hora_inicio: card.dataset.horaInicio,
            hora_fim: card.dataset.horaFim
        };
        
        const conflito = temConflito(atividade, atividadesInscritas);
        const btnInscrever = card.querySelector('.btn-inscrever-atividade');
        
        if (conflito) {
            card.classList.add('atividade-conflito');
            messageElement.innerHTML = `Conflito de horário com: ${conflito.titulo} (${conflito.hora_inicio.slice(0,5)} - ${conflito.hora_fim.slice(0,5)})`;
            messageElement.style.display = 'block';
            
            if (btnInscrever) {
                btnInscrever.classList.add('btn-disabled');
                btnInscrever.disabled = true;
            }
        } else {
            card.classList.remove('atividade-conflito');
            messageElement.style.display = 'none';
            
            if (btnInscrever) {
                btnInscrever.classList.remove('btn-disabled');
                btnInscrever.disabled = false;
            }
        }
    });
}
        
        // Chamar a função para verificar conflitos inicialmente
        atualizarConflitos();
        atualizarMinhasInscricoes();

        
        // Inscrição em atividades via AJAX
        document.querySelectorAll('.btn-inscrever-atividade').forEach(button => {
            button.addEventListener('click', function() {
                if (this.disabled) return;
                
                const atividadeElement = this.closest('.atividade-card');
                const atividadeId = this.getAttribute('data-atividade-id');
                const messageAtividade = document.getElementById('messageAtividade');
                
                // Obter dados da nova atividade
                const novaAtividade = {
                    id: atividadeElement.dataset.id,
                    data: atividadeElement.dataset.data,
                    hora_inicio: atividadeElement.dataset.horaInicio,
                    hora_fim: atividadeElement.dataset.horaFim,
                    titulo: atividadeElement.querySelector('.atividade-title').textContent.trim()
                };
                
                // Verificar conflito
                const conflito = temConflito(novaAtividade, atividadesInscritas);
                if (conflito) {
                    messageAtividade.innerHTML = `<div class="message erro">Conflito de horário com a atividade: ${conflito.titulo} (${conflito.hora_inicio.slice(0,5)} - ${conflito.hora_fim.slice(0,5)}).</div>`;
                    // Esconder a mensagem após 5 segundos
                    setTimeout(() => {
                        messageAtividade.innerHTML = '';
                    }, 5000);
                    return;
                }
                
                fetch('atividades_handler.php?action=inscrever', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `atividade_id=${atividadeId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        messageAtividade.innerHTML = `<div class="message sucesso">${data.message}</div>`;
                        
                        // Adicionar a nova atividade ao array de atividadesInscritas
                        atividadesInscritas.push({
                            id: novaAtividade.id,
                            data: novaAtividade.data,
                            hora_inicio: novaAtividade.hora_inicio,
                            hora_fim: novaAtividade.hora_fim,
                            titulo: novaAtividade.titulo
                        });
                        
                        // Atualizar o botão
                        this.innerHTML = '<i class="fas fa-times"></i> Cancelar Inscrição';
                        this.classList.remove('btn-inscrever-atividade');
                        this.classList.add('btn-cancelar-atividade');
                        this.style.background = 'linear-gradient(45deg, var(--error-color), #ff6b6b)';
                        
                        // Adicionar evento de cancelamento
                        this.addEventListener('click', cancelarInscricaoHandler);
                        
                        // Atualizar a contagem de vagas
                        const vagasElement = atividadeElement.querySelector('.atividade-vagas');
                        const vagasText = vagasElement.textContent;
                        const match = vagasText.match(/(\d+)/);
                        if (match) {
                            const vagas = parseInt(match[1]);
                            if (vagas > 1) {
                                vagasElement.textContent = (vagas - 1) + ' vagas restantes';
                            } else {
                                vagasElement.textContent = 'Esgotada';
                                // Desabilitar outros botões de inscrição para esta atividade
                                document.querySelectorAll(`.btn-inscrever-atividade[data-atividade-id="${atividadeId}"]`).forEach(btn => {
                                    btn.classList.add('btn-disabled');
                                    btn.disabled = true;
                                });
                            }
                        }
                        
                        // Atualizar conflitos
                        atualizarConflitos();
                        atualizarMinhasInscricoes();
                        // Atualizar a aba "Minhas Inscrições"
                        setTimeout(() => {
                            document.querySelector('[data-tab="minhas"]').click();
                        }, 1500);
                    } else {
                        messageAtividade.innerHTML = `<div class="message erro">${data.message}</div>`;
                    }
                    
                    // Esconder a mensagem após 5 segundos
                    setTimeout(() => {
                        messageAtividade.innerHTML = '';
                    }, 5000);
                })
                .catch(error => {
                    messageAtividade.innerHTML = `<div class="message erro">Erro: ${error}</div>`;
                });
            });
        });
        
        // Cancelamento de inscrição via AJAX
        const cancelarInscricaoHandler = function() {
            const atividadeId = this.getAttribute('data-atividade-id');
            const messageAtividade = document.getElementById('messageAtividade');
            
            fetch('atividades_handler.php?action=cancelar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `atividade_id=${atividadeId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageAtividade.innerHTML = `<div class="message sucesso">${data.message}</div>`;
                    
                    // Remover a atividade do array de atividadesInscritas
                    const index = atividadesInscritas.findIndex(a => a.id == atividadeId);
                    if (index !== -1) {
                        atividadesInscritas.splice(index, 1);
                    }
                    
                    // Atualizar a interface
                    atualizarInterfaceAtividade(atividadeId, false);
                    atualizarConflitos();
                                    atualizarMinhasInscricoes();

                    // Atualizar a aba "Minhas Inscrições"
                    setTimeout(() => {
                        document.querySelector('[data-tab="minhas"]').click();
                    }, 1000);

                    //atualizarMinhasInscricoes();
                } else {
                    messageAtividade.innerHTML = `<div class="message erro">${data.message}</div>`;
                }
                
                setTimeout(() => {
                    messageAtividade.innerHTML = '';
                }, 5000);
            })
            .catch(error => {
                messageAtividade.innerHTML = `<div class="message erro">Erro: ${error}</div>`;
            });
        };
        
        // Adicionar evento de cancelamento aos botões existentes
        document.querySelectorAll('.btn-cancelar-atividade').forEach(button => {
            button.addEventListener('click', cancelarInscricaoHandler);
        });

        
function atualizarInterfaceAtividade(atividadeId, inscrito) {
    const atividadeElement = document.getElementById(`atividade-${atividadeId}`);
    if (!atividadeElement) return;
    
    const actionsDiv = atividadeElement.querySelector('.atividade-actions');
    const vagasElement = atividadeElement.querySelector('.atividade-vagas');
    
    // Limpar todos os botões existentes
    actionsDiv.innerHTML = '';
    
    if (inscrito) {
        // Criar apenas o botão de cancelar
        const btnCancelar = document.createElement('button');
        btnCancelar.className = 'btn-primary btn-cancelar-atividade';
        btnCancelar.setAttribute('data-atividade-id', atividadeId);
        btnCancelar.style.background = 'linear-gradient(45deg, var(--error-color), #ff6b6b)';
        btnCancelar.innerHTML = '<i class="fas fa-times"></i> Cancelar Inscrição';
        
        // Adicionar evento de cancelamento
        btnCancelar.addEventListener('click', cancelarInscricaoHandler);
        
        actionsDiv.appendChild(btnCancelar);
    } else {
        // Criar apenas o botão de inscrever
        const btnInscrever = document.createElement('button');
        btnInscrever.className = 'btn-primary btn-inscrever-atividade';
        btnInscrever.setAttribute('data-atividade-id', atividadeId);
        btnInscrever.innerHTML = '<i class="fas fa-plus"></i> Inscrever-se';
        
        // Adicionar event listener para o novo botão
        btnInscrever.addEventListener('click', function() {
            if (this.disabled) return;
            
            const atividadeElement = this.closest('.atividade-card');
            const atividadeId = this.getAttribute('data-atividade-id');
            const messageAtividade = document.getElementById('messageAtividade');
            
            // Obter dados da nova atividade
            const novaAtividade = {
                id: atividadeElement.dataset.id,
                data: atividadeElement.dataset.data,
                hora_inicio: atividadeElement.dataset.horaInicio,
                hora_fim: atividadeElement.dataset.horaFim,
                titulo: atividadeElement.querySelector('.atividade-title').textContent.trim()
            };
            
            // Verificar conflito
            const conflito = temConflito(novaAtividade, atividadesInscritas);
            if (conflito) {
                messageAtividade.innerHTML = `<div class="message erro">Conflito de horário com a atividade: ${conflito.titulo} (${conflito.hora_inicio.slice(0,5)} - ${conflito.hora_fim.slice(0,5)}).</div>`;
                // Esconder a mensagem após 5 segundos
                setTimeout(() => {
                    messageAtividade.innerHTML = '';
                }, 5000);
                return;
            }
            
            fetch('atividades_handler.php?action=inscrever', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `atividade_id=${atividadeId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageAtividade.innerHTML = `<div class="message sucesso">${data.message}</div>`;
                    
                    // Adicionar a nova atividade ao array de atividadesInscritas
                    atividadesInscritas.push({
                        id: novaAtividade.id,
                        data: novaAtividade.data,
                        hora_inicio: novaAtividade.hora_inicio,
                        hora_fim: novaAtividade.hora_fim,
                        titulo: novaAtividade.titulo
                    });
                    
                    // Atualizar a interface para mostrar o botão de cancelar
                    atualizarInterfaceAtividade(atividadeId, true);
                    
                    // Atualizar a contagem de vagas
                    if (vagasElement) {
                        const vagasText = vagasElement.textContent;
                        const match = vagasText.match(/(\d+)/);
                        if (match) {
                            const vagas = parseInt(match[1]);
                            if (vagas > 1) {
                                vagasElement.textContent = (vagas - 1) + ' vagas restantes';
                            } else {
                                vagasElement.textContent = 'Esgotada';
                                // Desabilitar outros botões de inscrição para esta atividade
                                document.querySelectorAll(`.btn-inscrever-atividade[data-atividade-id="${atividadeId}"]`).forEach(btn => {
                                    btn.classList.add('btn-disabled');
                                    btn.disabled = true;
                                });
                            }
                        }
                    }
                    
                    // Atualizar conflitos

                    atualizarConflitos();
                    
                    // Atualizar a aba "Minhas Inscrições"
                    setTimeout(() => {
                        document.querySelector('[data-tab="minhas"]').click();
                    }, 1500);
                } else {
                    messageAtividade.innerHTML = `<div class="message erro">${data.message}</div>`;
                }
                
                // Esconder a mensagem após 5 segundos
                setTimeout(() => {
                    messageAtividade.innerHTML = '';
                }, 5000);
            })
            .catch(error => {
                messageAtividade.innerHTML = `<div class="message erro">Erro: ${error}</div>`;
            });
        });
        
        actionsDiv.appendChild(btnInscrever);

        // Atualizar contador de vagas
        if (vagasElement) {
            const vagasText = vagasElement.textContent;
            if (vagasText === 'Esgotada') {
                vagasElement.textContent = '1 vaga restante';
            } else {
                const match = vagasText.match(/(\d+)/);
                if (match) {
                    const vagas = parseInt(match[1]);
                    vagasElement.textContent = (vagas + 1) + ' vagas restantes';
                }
            }
        }
    }
    
    // Atualizar conflitos de horário
    atualizarConflitos();
}



        // Adicione esta função para atualizar a aba de minhas inscrições
        function atualizarMinhasInscricoes() {
            fetch('atividades_handler.php?action=carregar')
            .then(response => response.text())
            .then(html => {
                document.getElementById('minhas-atividades').innerHTML = html;
                // Reaplicar event listeners aos novos botões
                document.querySelectorAll('.btn-cancelar-atividade').forEach(button => {
                    button.addEventListener('click', cancelarInscricaoHandler);
                });
            })
            .catch(error => {
                console.error('Erro ao carregar inscrições:', error);
            });
        }
        
        // Variável para controlar se os campos de senha estão visíveis
        let passwordFieldsVisible = false;

        // Edição de dados do usuário
        const btnEditDados = document.getElementById('btnEditDados');
        const editForm = document.getElementById('editForm');
        const btnCancelEdit = document.getElementById('btnCancelEdit');
        const togglePassword = document.getElementById('togglePassword');
        const passwordFields = document.getElementById('passwordFields');
        const formEditarDados = document.getElementById('formEditarDados');
        const messageEditarDados = document.getElementById('messageEditarDados');

        // Elementos dos dados exibidos (com IDs específicos)
        const infoNome = document.getElementById('info-nome');
        const infoEmail = document.getElementById('info-email');
        const infoTelefone = document.getElementById('info-telefone');
        const infoInstituicao = document.getElementById('info-instituicao');

        btnEditDados.addEventListener('click', () => {
            editForm.style.display = 'block';
            btnEditDados.style.display = 'none';
            // Garantir que os campos de senha estejam ocultos ao abrir o formulário
            passwordFields.style.display = 'none';
            passwordFieldsVisible = false;
        });

        btnCancelEdit.addEventListener('click', () => {
            editForm.style.display = 'none';
            btnEditDados.style.display = 'block';
            passwordFields.style.display = 'none';
            passwordFieldsVisible = false;
            // Limpar campos de senha ao cancelar
            document.getElementById('edit_senha_atual').value = '';
            document.getElementById('edit_nova_senha').value = '';
            document.getElementById('edit_confirmar_senha').value = '';
        });

        togglePassword.addEventListener('click', () => {
            passwordFieldsVisible = !passwordFieldsVisible;
            passwordFields.style.display = passwordFieldsVisible ? 'block' : 'none';
        });

        formEditarDados.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Se os campos de senha estão visíveis, validar as senhas
            if (passwordFieldsVisible) {
                const senhaAtual = document.getElementById('edit_senha_atual').value;
                const novaSenha = document.getElementById('edit_nova_senha').value;
                const confirmarSenha = document.getElementById('edit_confirmar_senha').value;
                
                if (!senhaAtual) {
                    messageEditarDados.innerHTML = '<div class="message erro">Por favor, informe sua senha atual para alterar a senha.</div>';
                    return;
                }
                
                if (novaSenha !== confirmarSenha) {
                    messageEditarDados.innerHTML = '<div class="message erro">A nova senha e a confirmação não coincidem.</div>';
                    return;
                }
                
                if (novaSenha.length < 6) {
                    messageEditarDados.innerHTML = '<div class="message erro">A nova senha deve ter pelo menos 6 caracteres.</div>';
                    return;
                }
            }
            
            const formData = new FormData(this);
            // Adicionar flag indicando se os campos de senha estão visíveis
            formData.append('senha_visible', passwordFieldsVisible ? '1' : '0');
            
            fetch('atividades_handler.php?action=editar_dados', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageEditarDados.innerHTML = `<div class="message sucesso">${data.message}</div>`;
                    
                    // Atualizar os dados exibidos usando os elementos com IDs específicos
                    if (infoNome) infoNome.textContent = document.getElementById('edit_nome').value;
                    if (infoEmail) infoEmail.textContent = document.getElementById('edit_email').value;
                    
                    const telefoneValue = document.getElementById('edit_telefone').value;
                    if (infoTelefone) infoTelefone.textContent = telefoneValue || 'Não informado';
                    
                    if (infoInstituicao) infoInstituicao.textContent = document.getElementById('edit_instituicao').value;
                    
                    // Esconder o formulário após 2 segundos
                    setTimeout(() => {
                        editForm.style.display = 'none';
                        btnEditDados.style.display = 'block';
                        passwordFields.style.display = 'none';
                        passwordFieldsVisible = false;
                        messageEditarDados.innerHTML = '';
                        // Limpar campos de senha
                        document.getElementById('edit_senha_atual').value = '';
                        document.getElementById('edit_nova_senha').value = '';
                        document.getElementById('edit_confirmar_senha').value = '';
                    }, 2000);
                } else {
                    messageEditarDados.innerHTML = `<div class="message erro">${data.message}</div>`;
                }
            })
            .catch(error => {
                messageEditarDados.innerHTML = `<div class="message erro">Erro: ${error}</div>`;
            });
        });
        
        // Formatação de CPF
        const cpfInput = document.getElementById('cpf');
        cpfInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if(value.length > 11) value = value.slice(0,11);
            
            if(value.length > 9) {
                value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
            } else if(value.length > 6) {
                value = value.replace(/(\d{3})(\d{3})(\d+)/, '$1.$2.$3');
            } else if(value.length > 3) {
                value = value.replace(/(\d{3})(\d+)/, '$1.$2');
            }
            
            e.target.value = value;
        });
        
        // Formatação de telefone
        const telefoneInput = document.getElementById('edit_telefone');
        telefoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if(value.length > 11) value = value.slice(0,11);
            
            if(value.length > 10) {
                value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            } else if(value.length > 6) {
                value = value.replace(/(\d{2})(\d{4})(\d+)/, '($1) $2-$3');
            } else if(value.length > 2) {
                value = value.replace(/(\d{2})(\d+)/, '($1) $2');
            } else if(value.length > 0) {
                value = value.replace(/(\d+)/, '($1');
            }
            
            e.target.value = value;
        });
    </script>
</body>
</html>
