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

// Preparar dados para retorno JSON
$dados_retorno = [
    'success' => true,
    'usuario' => $usuario,
    'info_inscricao' => [
        'categoria' => $categoria,
        'lote' => $lote,
        'preco' => $preco,
        'titulo' => !empty($categoria) && isset($textos_categoria[$categoria]) ? $textos_categoria[$categoria]['titulo'] : '',
        'descricao' => !empty($categoria) && isset($textos_categoria[$categoria]) ? $textos_categoria[$categoria]['descricao'] : '',
        'sistema_valores' => $categoria === 'universitario_ti' ? 
            "Para universitários de TI, oferecemos um sistema de lotes com desconto progressivo. 
            Os primeiros 50 inscritos que efetuarem o pagamento garantem o valor promocional do Lote 1. 
            Após isso, o valor passa para o Lote 2. Sua categoria garante o valor no momento do pagamento do comprovante." :
            "Sua categoria tem valor fixo, sem sistema de lotes. O valor da sua inscrição já está definido e 
            não sofrerá alterações independentemente do momento do pagamento.",
        'chave_pix' => $chave_pix,
        'imagem_pix' => $imagem_pix
    ],
    'comprovantes' => $comprovantes,
    'todas_atividades' => $todas_atividades,
    'atividades_inscritas' => $atividades_inscritas
];

// Retornar dados como JSON
header('Content-Type: application/json');
echo json_encode($dados_retorno);
exit;