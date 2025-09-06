<?php
session_start();
date_default_timezone_set('America/Sao_Paulo'); // Definir fuso horário correto

// Verificar se o usuário está logado e é administrador
if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario']) || $_SESSION['usuario_administrador'] != 1) {
    header("location: index.html#login");
    exit;
}

// Incluir arquivo de conexão
include("conexao.php");

// Processar ações do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => 'Ação não reconhecida'];

    // Verificar token CSRF (simplificado para este exemplo)
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $response['message'] = 'Token de segurança inválido';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    


    // Ação: Editar participante
    if (isset($_POST['action']) && $_POST['action'] === 'editar_participante') {
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $cpf = $_POST['cpf'];
        $telefone = $_POST['telefone'];
        $instituicao = $_POST['instituicao'];
        $tipo = $_POST['tipo'];
        $tipo_inscricao = $_POST['tipo_inscricao'];
    
        try {
            $stmt = $pdo->prepare("UPDATE participantes SET nome = :nome, email = :email, cpf = :cpf, telefone = :telefone, instituicao = :instituicao, tipo = :tipo, tipo_inscricao = :tipo_inscricao WHERE id = :id");
            $stmt->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':cpf' => $cpf,
                ':telefone' => $telefone,
                ':instituicao' => $instituicao,
                ':tipo' => $tipo,
                ':tipo_inscricao' => $tipo_inscricao,
                ':id' => $id
            ]);
            
            $response['success'] = true;
            $response['message'] = 'Participante atualizado com sucesso';
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao atualizar participante: ' . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Ação: Buscar participante
    if (isset($_POST['action']) && $_POST['action'] === 'buscar_participante') {
        $id = $_POST['id'];
    
        try {
            $stmt = $pdo->prepare("SELECT * FROM participantes WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $participante = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($participante) {
                $response['success'] = true;
                $response['participante'] = $participante;
            } else {
                $response['message'] = 'Participante não encontrado';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao buscar participante: ' . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Ação: Buscar atividade
    if (isset($_POST['action']) && $_POST['action'] === 'buscar_atividade') {
        $id = $_POST['id'];
    
        try {
            $stmt = $pdo->prepare("SELECT * FROM atividades WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $atividade = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if ($atividade) {
                $response['success'] = true;
                $response['atividade'] = $atividade;
            } else {
                $response['message'] = 'Atividade não encontrada';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao buscar atividade: ' . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Ação: Visualizar comprovante
    if (isset($_POST['action']) && $_POST['action'] === 'visualizar_comprovante') {
        $id = $_POST['id'];
    
        try {
            $stmt = $pdo->prepare("SELECT * FROM comprovantes WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $comprovante = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if ($comprovante) {
                $response['success'] = true;
                $response['arquivo'] = $comprovante['arquivo'];
                $response['tipo_arquivo'] = pathinfo($comprovante['arquivo'], PATHINFO_EXTENSION);
            } else {
                $response['message'] = 'Comprovante não encontrado';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao buscar comprovante: ' . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Ação: Alternar status de administrador
    if (isset($_POST['action']) && $_POST['action'] === 'toggle_admin') {
        $id = $_POST['id'];
        $administrador = $_POST['administrador'];
        
        if($_POST["administrador"]){ $tipo="administrador";}
        else if(!$_POST["administrador"]){ $tipo="participante";}
        
        try {
            $stmt = $pdo->prepare("UPDATE participantes SET administrador = :administrador, tipo = :tipo WHERE id = :id");
            $stmt->execute([
                ':administrador' => $administrador,
                ':tipo' => $tipo,
                ':id' => $id
            ]);
            
            $response['success'] = true;
            $response['message'] = 'Status de administrador atualizado';
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao atualizar status: ' . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Ação: Cadastrar atividade
    if (isset($_POST['action']) && $_POST['action'] === 'cadastrar_atividade') {
        $titulo = $_POST['titulo'];
        $tipo = $_POST['tipo'];
        $palestrante = $_POST['palestrante'];
        $local = $_POST['local'];
        $data = $_POST['data'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_fim = $_POST['hora_fim'];
        $vagas = $_POST['vagas'];
        
        try {
            $stmt = $pdo->prepare("INSERT INTO atividades (titulo, tipo, palestrante, sala, data, hora_inicio, hora_fim, vagas, ativa) VALUES (:titulo, :tipo, :palestrante, :sala, :data, :hora_inicio, :hora_fim, :vagas, 1)");
            $stmt->execute([
                ':titulo' => $titulo,
                ':tipo' => $tipo,
                ':palestrante' => $palestrante,
                ':sala' => $local,
                ':data' => $data,
                ':hora_inicio' => $hora_inicio,
                ':hora_fim' => $hora_fim,
                ':vagas' => $vagas
            ]);
            
            $response['success'] = true;
            $response['message'] = 'Atividade cadastrada com sucesso';
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao cadastrar atividade: ' . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Ação: Editar atividade
    if (isset($_POST['action']) && $_POST['action'] === 'editar_atividade') {
        $id = $_POST['id'];
        $titulo = $_POST['titulo'];
        $tipo = $_POST['tipo'];
        $palestrante = $_POST['palestrante'];
        $local = $_POST['local'];
        $data = $_POST['data'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_fim = $_POST['hora_fim'];
        $vagas = $_POST['vagas'];
        
        try {
            $stmt = $pdo->prepare("UPDATE atividades SET titulo = :titulo, tipo = :tipo, palestrante = :palestrante, sala = :sala, data = :data, horario = :horario, hora_inicio = :hora_inicio, vagas = :vagas WHERE id = :id");
            $stmt->execute([
                ':titulo' => $titulo,
                ':tipo' => $tipo,
                ':palestrante' => $palestrante,
                ':sala' => $local,
                ':data' => $data,
                ':horario' => $hora_inicio . ' - ' . $hora_fim,
                ':hora_inicio' => $hora_inicio,
                ':vagas' => $vagas,
                ':id' => $id
            ]);
            
            $response['success'] = true;
            $response['message'] = 'Atividade atualizada com sucesso';
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao atualizar atividade: ' . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Ação: Registrar presença
    if (isset($_POST['action']) && $_POST['action'] === 'registrar_presenca') {
        $atividade_id = $_POST['atividade_id'];
        $codigo_barras = $_POST['codigo_barras'];
        
        try {
            // Buscar participante pelo código de barras
            $stmt = $pdo->prepare("SELECT * FROM participantes WHERE codigo_barra = :codigo_barra");
            $stmt->execute([':codigo_barra' => $codigo_barras]);
            $participante = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($participante) {
                $participante_id = $participante['id'];
                
                // Verificar se já registrou presença
                $stmt = $pdo->prepare("SELECT id FROM presencas WHERE id_participante = :participante_id AND id_atividade = :atividade_id");
                $stmt->execute([
                    ':participante_id' => $participante_id,
                    ':atividade_id' => $atividade_id
                ]);
                
                if ($stmt->rowCount() === 0) {
                    // Registrar presença
                    $stmt = $pdo->prepare("INSERT INTO presencas (id_participante, id_atividade, data_hora) VALUES (:participante_id, :atividade_id, NOW())");
                    $stmt->execute([
                        ':participante_id' => $participante_id,
                        ':atividade_id' => $atividade_id
                    ]);
                    
                    $response['success'] = true;
                    $response['message'] = 'Presença registrada com sucesso';
                } else {
                    $response['message'] = 'Presença já registrada anteriormente';
                }
            } else {
                $response['message'] = 'Participante não encontrado com este código de barras';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao registrar presença: ' . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Ação: Validar pagamento (com adição de transação)
    if (isset($_POST['action']) && $_POST['action'] === 'validar_pagamento') {
        $id = $_POST['id'];
        $aprovado = $_POST['aprovado'] == '1';
        
        // Buscar informações do comprovante
        $stmt = $pdo->prepare("SELECT c.*, p.nome as participante_nome, p.valor_pago 
                              FROM comprovantes c 
                              JOIN participantes p ON c.participante_id = p.id 
                              WHERE c.id = :id");
        $stmt->execute([':id' => $id]);
        $comprovante = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($comprovante) {
            // Atualizar status do comprovante
            $status = $aprovado ? 'aprovado' : 'rejeitado';
            $stmt = $pdo->prepare("UPDATE comprovantes SET status = :status, data_avaliacao = NOW() WHERE id = :id");
            $stmt->execute([':status' => $status, ':id' => $id]);
            
            // Se foi aprovado, criar uma transação correspondente
            if ($aprovado) {
                // Verificar si já existe uma transação para este comprovante
                $stmt = $pdo->prepare("SELECT id FROM transacoes WHERE comprovante_id = :comprovante_id");
                $stmt->execute([':comprovante_id' => $id]);
                $transacao_existente = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$transacao_existente) {
                    // Criar nova transação de entrada
                    $stmt = $pdo->prepare("INSERT INTO transacoes 
                                        (categoria_id, descricao, valor, data, tipo, comprovante_id, participante_id) 
                                        VALUES 
                                        (:categoria_id, :descricao, :valor, :data, 'entrada', :comprovante_id, :participante_id)");
                    
                    $stmt->execute([
                        ':categoria_id' => 1, // ID da categoria "Inscrições"
                        ':descricao' => 'Inscrição - ' . $comprovante['participante_nome'],
                        ':valor' => $comprovante['valor_pago'],
                        ':data' => date('Y-m-d'),
                        ':comprovante_id' => $id,
                        ':participante_id' => $comprovante['participante_id']
                    ]);
                }
            }
            
            $response['success'] = true;
            $response['message'] = 'Comprovante ' . ($aprovado ? 'aprovado' : 'rejeitado') . ' com sucesso!';
        } else {
            $response['message'] = 'Comprovante não encontrado!';
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Ação: Adicionar transação manualmente
    if (isset($_POST['action']) && $_POST['action'] === 'adicionar_transacao') {
        $categoria_id = $_POST['categoria_id'];
        $descricao = $_POST['descricao'];
        $valor = $_POST['valor'];
        $data = $_POST['data'];
        $tipo = $_POST['tipo'];
        
        try {
            $stmt = $pdo->prepare("INSERT INTO transacoes (categoria_id, descricao, valor, data, tipo) 
                                  VALUES (:categoria_id, :descricao, :valor, :data, :tipo)");
            $stmt->execute([
                ':categoria_id' => $categoria_id,
                ':descricao' => $descricao,
                ':valor' => $valor,
                ':data' => $data,
                ':tipo' => $tipo
            ]);
            
            $response['success'] = true;
            $response['message'] = 'Transação adicionada com sucesso';
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao adicionar transação: ' . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Ação: Excluir transação
    if (isset($_POST['action']) && $_POST['action'] === 'excluir_transacao') {
        $id = $_POST['id'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM transacoes WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
            $response['success'] = true;
            $response['message'] = 'Transação excluída com sucesso';
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao excluir transação: ' . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Ação: Buscar preço
    if (isset($_POST['action']) && $_POST['action'] === 'buscar_preco') {
        $id = $_POST['id'];
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM precos_inscricao WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $preco = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($preco) {
                $response['success'] = true;
                $response['preco'] = $preco;
            } else {
                $response['message'] = 'Preço não encontrado';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao buscar preço: ' . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Ação: Adicionar preço
    if (isset($_POST['action']) && $_POST['action'] === 'adicionar_preco') {
        $categoria = $_POST['categoria'];
        $descricao = $_POST['descricao'];
        $valor = $_POST['valor'];
        $lote = $_POST['lote'];
        $ativo = $_POST['ativo'];
        
        try {
            $stmt = $pdo->prepare("INSERT INTO precos_inscricao (categoria, descricao, valor, lote, ativo) VALUES (:categoria, :descricao, :valor, :lote, :ativo)");
            $stmt->execute([
                ':categoria' => $categoria,
                ':descricao' => $descricao,
                ':valor' => $valor,
                ':lote' => $lote,
                ':ativo' => $ativo
            ]);
            
            $response['success'] = true;
            $response['message'] = 'Preço adicionado com sucesso';
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao adicionar preço: ' . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Ação: Editar preço
    if (isset($_POST['action']) && $_POST['action'] === 'editar_preco') {
        $id = $_POST['id'];
        $categoria = $_POST['categoria'];
        $descricao = $_POST['descricao'];
        $valor = $_POST['valor'];
        $lote = $_POST['lote'];
        $ativo = $_POST['ativo'];
        
        try {
            $stmt = $pdo->prepare("UPDATE precos_inscricao SET categoria = :categoria, descricao = :descricao, valor = :valor, lote = :lote, ativo = :ativo WHERE id = :id");
            $stmt->execute([
                ':categoria' => $categoria,
                ':descricao' => $descricao,
                ':valor' => $valor,
                ':lote' => $lote,
                ':ativo' => $ativo,
                ':id' => $id
            ]);
            
            $response['success'] = true;
            $response['message'] = 'Preço atualizado com sucesso';
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao editar preço: ' . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Ação: Cadastrar participante
    if (isset($_POST['action']) && $_POST['action'] === 'cadastrar_participante') {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $cpf = $_POST['cpf'];
        $telefone = $_POST['telefone'];
        $instituicao = $_POST['instituicao'];
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $tipo = $_POST['tipo'];
        $tipo_inscricao = $_POST['tipo_inscricao'];
        $voucher = $_POST['voucher'];
        $isento_pagamento = isset($_POST['isento_pagamento']) ? 1 : 0;
        
        // Gerar código de barras único
        $codigo_barra = uniqid('TW');
        
        try {
            $stmt = $pdo->prepare("INSERT INTO participantes (nome, email, cpf, telefone, instituicao, senha, tipo, tipo_inscricao, codigo_barra, voucher, isento_pagamento) VALUES (:nome, :email, :cpf, :telefone, :instituicao, :senha, :tipo, :tipo_inscricao, :codigo_barra, :voucher, :isento_pagamento)");
            $stmt->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':cpf' => $cpf,
                ':telefone' => $telefone,
                ':instituicao' => $instituicao,
                ':senha' => $senha,
                ':tipo' => $tipo,
                ':tipo_inscricao' => $tipo_inscricao,
                ':codigo_barra' => $codigo_barra,
                ':voucher' => $voucher,
                ':isento_pagamento' => $isento_pagamento
            ]);
            
            $response['success'] = true;
            $response['message'] = 'Participante cadastrado com sucesso';
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao cadastrar participante: ' . $e->getMessage();
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Ação: Alternar status de atividade
    if (isset($_POST['action']) && $_POST['action'] === 'toggle_ativa') {
        $id = $_POST['id'];
        $ativa = $_POST['ativa'] ? 1 : 0;
        
        try {
            $stmt = $pdo->prepare("UPDATE atividades SET ativa = :ativa WHERE id = :id");
            $stmt->execute([
                ':ativa' => $ativa,
                ':id' => $id
            ]);
            
            $response['success'] = true;
            $response['message'] = 'Status da atividade atualizado';
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao atualizar status: ' . $e->getMessage();
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Ação: Excluir preço
    if (isset($_POST['action']) && $_POST['action'] === 'excluir_preco') {
        $id = $_POST['id'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM precos_inscricao WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
            $response['success'] = true;
            $response['message'] = 'Preço excluído com sucesso';
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao excluir preço: ' . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }


// Ação: Sincronizar comprovantes antigos
    if (isset($_POST['action']) && $_POST['action'] === 'sincronizar_comprovantes_antigos') {
        try {
            // Buscar comprovantes aprovados que não têm transações
            $stmt = $pdo->prepare("SELECT c.*, p.nome as participante_nome, p.valor_pago 
                                  FROM comprovantes c 
                                  JOIN participantes p ON c.participante_id = p.id 
                                  WHERE c.status = 'aprovado' 
                                  AND c.id NOT IN (SELECT comprovante_id FROM transacoes WHERE comprovante_id IS NOT NULL)");
            $stmt->execute();
            $comprovantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $count = 0;
            foreach ($comprovantes as $comprovante) {
                // Criar transação para cada comprovante
                $stmt = $pdo->prepare("INSERT INTO transacoes 
                                      (categoria_id, descricao, valor, data, tipo, comprovante_id, participante_id) 
                                      VALUES 
                                      (:categoria_id, :descricao, :valor, :data, 'entrada', :comprovante_id, :participante_id)");
                
                $stmt->execute([
                    ':categoria_id' => 1, // ID da categoria "Inscrições"
                    ':descricao' => 'Inscrição - ' . $comprovante['participante_nome'],
                    ':valor' => $comprovante['valor_pago'],
                    ':data' => date('Y-m-d', strtotime($comprovante['data_avaliacao'])),
                    ':comprovante_id' => $comprovante['id'],
                    ':participante_id' => $comprovante['participante_id']
                ]);
                
                $count++;
            }
            
            $response['success'] = true;
            $response['message'] = "Foram criadas $count transações para comprovantes antigos.";
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao sincronizar comprovantes: ' . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Dentro do if ($_SERVER['REQUEST_METHOD'] === 'POST')
    if (isset($_POST['action']) && $_POST['action'] === 'excluir_comprovante') {
        try {

            if (isset($_POST['id'])) {
                $id = $_POST['id'];
                // Verificar se o comprovante existe e está aprovado
                $stmt = $pdo->prepare("SELECT status FROM comprovantes WHERE id = ?");
                $stmt->execute([$id]);
                $comprovante = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($comprovante && $comprovante['status'] == 'aprovado') {
                    // Atualizar para excluído
                    $stmt = $pdo->prepare("UPDATE comprovantes SET status = 'excluido' WHERE id = ?");
                    if ($stmt->execute([$id])) {
                        $response = ['success' => true, 'message' => 'Comprovante excluído com sucesso'];
                    } else {
                        $response = ['success' => false, 'message' => 'Erro ao atualizar comprovante'];
                    }
                } else {
                    $response = ['success' => false, 'message' => 'Comprovante não encontrado ou não está aprovado'];
                }
            } else {
                $response = ['success' => false, 'message' => 'ID não fornecido'];
            }
        
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao sincronizar comprovantes: ' . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Ação: Criar backup
if (isset($_POST['action']) && $_POST['action'] === 'criar_backup') {
    $tipo = $_POST['tipo'];
    $response = ['success' => false, 'message' => ''];
    
    // Diretório para armazenar backups (fora do root público)
    $backupDir = '../backups/';
    if (!file_exists($backupDir)) {
        mkdir($backupDir, 0755, true);
    }
    
    // Nome do arquivo com timestamp
    $timestamp = date('Y-m-d_His');
    $backupFile = '';
    
    try {
        if ($tipo === 'database' || $tipo === 'completo') {
            // Backup do banco de dados
            $dbBackupFile = $backupDir . 'backup_db_' . $timestamp . '.sql';
            exec("mysqldump --user={$username} --password={$password} --host={$host} {$dbname} > {$dbBackupFile}");
            
            if (file_exists($dbBackupFile)) {
                $backupFile = $dbBackupFile;
                
                // Registrar no banco de dados
                $stmt = $pdo->prepare("INSERT INTO backups (nome_arquivo, tipo, tamanho, caminho_arquivo) VALUES (?, 'database', ?, ?)");
                $stmt->execute([basename($dbBackupFile), formatFileSize(filesize($dbBackupFile)), $dbBackupFile]);
                
                $response['success'] = true;
                $response['message'] = 'Backup do banco criado com sucesso!';
            } else {
                $response['message'] = 'Falha ao criar backup do banco.';
            }
        }
        
        if ($tipo === 'arquivos' || $tipo === 'completo') {
            // Backup dos arquivos (compactar diretório raiz)
            $zip = new ZipArchive();
            $zipFile = $backupDir . 'backup_files_' . $timestamp . '.zip';
            
            if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
                // Adicionar arquivos ao ZIP (excluindo a pasta backups)
                addFolderToZip('.', $zip, 'backups');
                $zip->close();
                
                if (file_exists($zipFile)) {
                    $backupFile = $zipFile;
                    
                    // Registrar no banco de dados
                    $stmt = $pdo->prepare("INSERT INTO backups (nome_arquivo, tipo, tamanho, caminho_arquivo) VALUES (?, 'arquivos', ?, ?)");
                    $stmt->execute([basename($zipFile), formatFileSize(filesize($zipFile)), $zipFile]);
                    
                    $response['success'] = true;
                    $response['message'] = 'Backup de arquivos criado com sucesso!';
                } else {
                    $response['message'] = 'Falha ao criar backup de arquivos.';
                }
            } else {
                $response['message'] = 'Não foi possível criar o arquivo ZIP.';
            }
        }
    } catch (Exception $e) {
        $response['message'] = 'Erro: ' . $e->getMessage();
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Ação: Excluir backup
if (isset($_POST['action']) && $_POST['action'] === 'excluir_backup') {
    $id = $_POST['id'];
    $response = ['success' => false, 'message' => ''];
    
    try {
        // Buscar informações do backup
        $stmt = $pdo->prepare("SELECT * FROM backups WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $backup = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($backup) {
            // Excluir arquivo físico
            if (file_exists($backup['caminho_arquivo'])) {
                unlink($backup['caminho_arquivo']);
            }
            
            // Excluir registro do banco
            $stmt = $pdo->prepare("DELETE FROM backups WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
            $response['success'] = true;
            $response['message'] = 'Backup excluído com sucesso!';
        } else {
            $response['message'] = 'Backup não encontrado.';
        }
    } catch (PDOException $e) {
        $response['message'] = 'Erro ao excluir backup: ' . $e->getMessage();
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}



    // Se for uma requisição AJAX, retornar JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}


// Função para formatar tamanho de arquivo (adicionar no início do arquivo)
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

// Função para adicionar pasta ao ZIP recursivamente
function addFolderToZip($folder, &$zip, $excludeFolder = '') {
    $handle = opendir($folder);
    while (false !== ($file = readdir($handle))) {
        if ($file != '.' && $file != '..') {
            $fullpath = $folder . '/' . $file;
            // Pular a pasta de backups
            if ($excludeFolder && strpos($fullpath, $excludeFolder) !== false) {
                continue;
            }
            if (is_dir($fullpath)) {
                addFolderToZip($fullpath, $zip, $excludeFolder);
            } else {
                $zip->addFile($fullpath, $fullpath);
            }
        }
    }
    closedir($handle);
}



// Gerar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}