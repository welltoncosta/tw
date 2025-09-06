    <?php
    session_start();

    $pdo = include("conexao.php");

    // Receber os dados JSON
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Verificar se os dados foram recebidos corretamente
    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        exit;
    }

    // Extrair e sanitizar os dados
    $nome = filter_var($data['nome'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
    $cpf = filter_var($data['cpf'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $senha = $data['senha'];
    $telefone = isset($data['telefone']) ? filter_var($data['telefone'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : '';
    $instituicao = isset($data['instituicao']) ? filter_var($data['instituicao'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : '';
    $categoria = isset($data['categoria']) ? filter_var($data['categoria'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : '';

    // No início do seu código PHP, adicione esta verificação adicional:
    error_log("Dados recebidos: " . print_r($data, true)); // Para debug

    // Verificar se todos os campos obrigatórios estão presentes no JSON
    $camposObrigatorios = ['nome', 'email', 'cpf', 'senha', 'categoria'];
    foreach ($camposObrigatorios as $campo) {
        if (!isset($data[$campo]) || empty($data[$campo])) {
            echo json_encode(['success' => false, 'message' => "Campo obrigatório '$campo' não encontrado ou vazio"]);
            exit;
        }
    }

    // Sua validação atual continua...
    if (empty($nome) || empty($email) || empty($cpf) || empty($senha) || empty($categoria)) {
        echo json_encode(['success' => false, 'message' => 'Preencha todos os campos obrigatórios']);
        exit;
    }

    // Validar formato de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Email inválido']);
        exit;
    }

    // Validar formato de CPF
    function validarCPF($cpf) {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        // Verifica se o número de dígitos é igual a 11
        if (strlen($cpf) != 11) {
            return false;
        }
        
        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        
        // Calcula os dígitos verificadores
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }

    if (!validarCPF($cpf)) {
        echo json_encode(['success' => false, 'message' => 'CPF inválido']);
        exit;
    }

    // Validar força da senha
    if (strlen($senha) < 6) {
        echo json_encode(['success' => false, 'message' => 'A senha deve ter pelo menos 6 caracteres']);
        exit;
    }

    try {
        // Verificar se já existe um participante com o mesmo CPF ou email
        $stmt = $pdo->prepare("SELECT id FROM participantes WHERE cpf = :cpf OR email = :email");
        $stmt->execute([':cpf' => $cpf, ':email' => $email]);
        
        if ($stmt->rowCount() > 0) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'Já existe um participante com este CPF ou email']);
            exit;
        }
        
        // Determinar o lote com base na categoria e número de inscrições pagas
        $lote_inscricao = '1'; // Lote padrão
        
        if ($categoria === 'universitario_ti') {
            // No código de inscrição (participante_cadastrar.php)
    // Contar inscrições pagas de universitários de TI
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM participantes p
        INNER JOIN transacoes t ON p.id = t.participante_id
        INNER JOIN comprovantes c ON t.comprovante_id = c.id
        WHERE p.tipo_inscricao = 'universitario_ti' 
        AND c.status = 'aprovado'
        AND t.tipo = 'entrada'
    ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Se já houver 50 inscrições pagas, mudar para o segundo lote
            if ($result['total'] >= 50) {
                $lote_inscricao = '2';
            }
        } else {
            // Para outras categorias, usar lote regular
            $lote_inscricao = 'regular';
        }
        
        // Buscar o preço correspondente à categoria e lote
        $stmt = $pdo->prepare("
            SELECT valor 
            FROM precos_inscricao 
            WHERE categoria = :categoria 
            AND lote = :lote
            AND ativo = 1
        ");
        $stmt->execute([':categoria' => $categoria, ':lote' => $lote_inscricao]);
        $preco = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $preco_inscricao = $preco ? $preco['valor'] : 0;
        
        // Obter o próximo número de inscrição
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM participantes");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $numero_inscricao = $result['total'] + 1;
        
        // Gerar código de barras no formato TW20250001
        $codigo_barra = 'TW2025' . str_pad($numero_inscricao, 4, '0', STR_PAD_LEFT);
        
        // Criptografar a senha
        $senhaHash = password_hash($senha, PASSWORD_BCRYPT);
        
        // Gerar hash único para o participante
        $hash = md5($cpf . $nome . time());
        
        // Inserir o novo participante no banco de dados
        $stmt = $pdo->prepare("
            INSERT INTO participantes 
            (administrador, tipo, hash, nome, email, senha, cpf, codigo_barra, telefone, instituicao, tipo_inscricao, lote_inscricao, preco_inscricao, data_cadastro) 
            VALUES (0, 'participante', :hash, :nome, :email, :senha, :cpf, :codigo_barra, :telefone, :instituicao, :tipo_inscricao, :lote_inscricao, :preco_inscricao, NOW())
        ");
        
        $stmt->execute([
            ':hash' => $hash,
            ':nome' => $nome,
            ':email' => $email,
            ':senha' => $senhaHash,
            ':cpf' => $cpf,
            ':codigo_barra' => $codigo_barra,
            ':telefone' => $telefone,
            ':instituicao' => $instituicao,
            ':tipo_inscricao' => $categoria,
            ':lote_inscricao' => $lote_inscricao,
            ':preco_inscricao' => $preco_inscricao
        ]);
        
        $idParticipante = $pdo->lastInsertId();
        
        // Armazenar dados na sessão para uso no painel
        $_SESSION['usuario'] = [
            'id' => $idParticipante,        
            'nome' => $nome,
            'email' => $email,
            'cpf' => $cpf,
            'telefone' => $telefone,
            'instituicao' => $instituicao,
            'tipo_inscricao' => $categoria,
            'lote_inscricao' => $lote_inscricao,
            'preco_inscricao' => $preco_inscricao,
            'hash' => $hash,
            'data_cadastro' => date("Y-m-d H:i:s")
        ];
        
        // Responder com sucesso
        http_response_code(201);
        echo json_encode([
            'success' => true, 
            'message' => 'Cadastro realizado com sucesso', 
            'redirect' => 'painel.php',
            'preco' => $preco_inscricao,
            'lote' => $lote_inscricao
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro no servidor: ' . $e->getMessage()]);
    }