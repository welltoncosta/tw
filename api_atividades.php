<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Permite acesso de qualquer domínio (ajuste conforme necessário)

// Incluir arquivo de conexão
include("conexao.php");

try {
    // Buscar atividades ativas ordenadas por data e hora de início
    $stmt = $pdo->prepare("SELECT * FROM atividades WHERE ativa = '1' ORDER BY data, hora_inicio");
    $stmt->execute();
    $atividades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatar os dados para resposta
    $response = [];
    
    foreach ($atividades as $atividade) {
        // Formatar a data para exibição (dd/mm)
        $dataFormatada = date('d/m', strtotime($atividade['data']));
        
        // Formatar o dia da semana em português
        $diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
        $diaSemana = $diasSemana[date('w', strtotime($atividade['data']))];
        
        // Verificar se é um evento de destaque
        $isDestaque = false;
        $palavrasDestaque = ['abertura', 'cerimônia', 'magna', 'encerramento', 'competição'];
        foreach ($palavrasDestaque as $palavra) {
            if (stripos($atividade['titulo'], $palavra) !== false) {
                $isDestaque = true;
                break;
            }
        }
        
        // Formatar horário se necessário
        $horario = $atividade['horario'];
        if (empty($horario) && !empty($atividade['hora_inicio']) && !empty($atividade['hora_fim'])) {
            $horaInicio = date('H:i', strtotime($atividade['hora_inicio']));
            $horaFim = date('H:i', strtotime($atividade['hora_fim']));
            $horario = $horaInicio . ' - ' . $horaFim;
        }
        
        $response[] = [
            'dataFormatada' => $dataFormatada,
            'diaSemana' => $diaSemana,
            'horario' => $horario,
            'titulo' => $atividade['titulo'],
            'palestrante' => $atividade['palestrante'],
            'sala' => $atividade['sala'],
            'vagas' => $atividade['vagas'],
            'destaque' => $isDestaque
        ];
    }
    
    echo json_encode(['success' => true, 'atividades' => $response]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao carregar atividades: ' . $e->getMessage()]);
}
?>
