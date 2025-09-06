<?php
session_start();

// Verificar se o usuário está logado e é administrador
if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario']) || $_SESSION['usuario']['administrador'] != 1) {
    header("location: index.html#login");
    exit;
}

// Incluir arquivo de conexão
include("conexao.php");

// Verificar se foi passado um ID de participante
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID do participante não informado.");
}

$participante_id = $_GET['id'];
$atividade_id = isset($_GET['atividade_id']) ? $_GET['atividade_id'] : null;

// Buscar dados do participante
try {
    $stmt = $pdo->prepare("SELECT * FROM participantes WHERE id = :id");
    $stmt->execute([':id' => $participante_id]);
    $participante = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$participante) {
        die("Participante não encontrado.");
    }
} catch (PDOException $e) {
    die("Erro ao buscar participante: " . $e->getMessage());
}

// Buscar atividades que o participante compareceu
try {
    if ($atividade_id) {
        // Certificado para uma atividade específica
        $stmt = $pdo->prepare("
            SELECT a.* 
            FROM atividades a
            INNER JOIN presencas p ON a.id = p.id_atividade
            WHERE p.id_participante = :participante_id AND a.id = :atividade_id
        ");
        $stmt->execute([
            ':participante_id' => $participante_id,
            ':atividade_id' => $atividade_id
        ]);
    } else {
        // Certificado geral de participação
        $stmt = $pdo->prepare("
            SELECT a.* 
            FROM atividades a
            INNER JOIN presencas p ON a.id = p.id_atividade
            WHERE p.id_participante = :participante_id
            GROUP BY a.id
        ");
        $stmt->execute([':participante_id' => $participante_id]);
    }
    
    $atividades = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar atividades: " . $e->getMessage());
}

// Se não houver atividades, não pode emitir certificado
if (empty($atividades)) {
    die("Participante não possui presença registrada em nenhuma atividade.");
}

// Calcular carga horária total
$carga_horaria_total = 0;
foreach ($atividades as $atividade) {
    // Calcular diferença entre horário de início e fim
    $inicio = DateTime::createFromFormat('H:i:s', $atividade['hora_inicio']);
    $fim = DateTime::createFromFormat('H:i:s', $atividade['hora_fim']);
    
    if ($inicio && $fim) {
        $diferenca = $inicio->diff($fim);
        $horas = $diferenca->h + ($diferenca->i / 60);
        $carga_horaria_total += $horas;
    }
}

// Arredondar para o número inteiro mais próximo
$carga_horaria_total = round($carga_horaria_total);

// Gerar PDF do certificado
require_once('tcpdf/tcpdf.php');

// Criar novo documento PDF
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

// Definir informações do documento
$pdf->SetCreator('TechWeek');
$pdf->SetAuthor('TechWeek');
$pdf->SetTitle('Certificado de Participação');
$pdf->SetSubject('Certificado');

// Remover cabeçalho e rodapé padrão
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Adicionar uma página
$pdf->AddPage();

// Definir cor do texto
$pdf->SetTextColor(0, 0, 0);

// Adicionar imagem de fundo (se houver)
// $pdf->Image('imagens/certificado_bg.jpg', 0, 0, 297, 210, '', '', '', false, 300, '', false, false, 0);

// Adicionar conteúdo do certificado
$html = '
    <style>
        body {
            font-family: "helvetica", sans-serif;
            text-align: center;
        }
        h1 {
            font-size: 28px;
            color: #00BF63;
            margin-bottom: 30px;
        }
        p {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .participante {
            font-size: 20px;
            font-weight: bold;
            margin: 40px 0;
            color: #000;
        }
        .atividades {
            font-size: 14px;
            margin: 20px 0;
            text-align: left;
        }
        .carga-horaria {
            font-size: 16px;
            margin-top: 30px;
        }
        .data {
            font-size: 14px;
            margin-top: 40px;
        }
    </style>
    <body>
        <h1>Certificado de Participação</h1>
        <p>Certificamos que</p>
        <div class="participante">' . $participante['nome'] . '</div>
        <p>participou da <strong>1ª TechWeek</strong> do Curso de Análise e Desenvolvimento de Sistemas,';
        
if ($atividade_id) {
    $html .= ' na atividade <strong>' . $atividades[0]['titulo'] . '</strong>,';
} else {
    $html .= ' com participação nas seguintes atividades:';
}

$html .= ' realizada no período de 20 a 22 de Março de 2025.</p>';

if (!$atividade_id) {
    $html .= '<div class="atividades">
                <ul>';
    foreach ($atividades as $atividade) {
        $html .= '<li>' . $atividade['titulo'] . ' - ' . date('d/m/Y', strtotime($atividade['data'])) . ' - ' . $atividade['horario'] . '</li>';
    }
    $html .= '</ul>
            </div>';
}

$html .= '<div class="carga-horaria">Carga horária total: ' . $carga_horaria_total . ' horas</div>
        <div class="data">Data de emissão: ' . date('d/m/Y') . '</div>
    </body>';

// Escrever o conteúdo HTML
$pdf->writeHTML($html, true, false, true, false, '');

// Gerar PDF e enviar para o navegador
$pdf->Output('certificado_' . $participante['nome'] . '.pdf', 'I');
