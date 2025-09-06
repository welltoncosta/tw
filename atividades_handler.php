<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

$usuario = $_SESSION['usuario'];
$response = [];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'inscrever':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atividade_id'])) {
            $participante_id = $usuario['id'];
            $atividade_id = $_POST['atividade_id'];
            
            try {
                // Verificar se o participante já está inscrito
                $stmt = $pdo->prepare("SELECT id FROM inscricoes_atividades WHERE participante_id = :participante_id AND atividade_id = :atividade_id");
                $stmt->execute([
                    ':participante_id' => $participante_id,
                    ':atividade_id' => $atividade_id
                ]);
                
                if ($stmt->rowCount() === 0) {
                    // Verificar se há vagas disponíveis
                    $stmt = $pdo->prepare("SELECT vagas FROM atividades WHERE id = :atividade_id");
                    $stmt->execute([':atividade_id' => $atividade_id]);
                    $atividade = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $stmt = $pdo->prepare("SELECT COUNT(*) as inscricoes FROM inscricoes_atividades WHERE atividade_id = :atividade_id");
                    $stmt->execute([':atividade_id' => $atividade_id]);
                    $inscricoes = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($inscricoes['inscricoes'] >= $atividade['vagas']) {
                        $response['success'] = false;
                        $response['message'] = "Não há vagas disponíveis para esta atividade.";
                        break;
                    }
                    
                    // Fazer a inscrição
                    $stmt = $pdo->prepare("INSERT INTO inscricoes_atividades (participante_id, atividade_id) VALUES (:participante_id, :atividade_id)");
                    $stmt->execute([
                        ':participante_id' => $participante_id,
                        ':atividade_id' => $atividade_id
                    ]);
                    
                    $response['success'] = true;
                    $response['message'] = "Inscrição realizada com sucesso!";
                } else {
                    $response['success'] = false;
                    $response['message'] = "Você já está inscrito nesta atividade.";
                }
            } catch (PDOException $e) {
                $response['success'] = false;
                $response['message'] = "Erro ao processar inscrição: " . $e->getMessage();
            }
        }
        break;
        
    case 'cancelar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atividade_id'])) {
            $participante_id = $usuario['id'];
            $atividade_id = $_POST['atividade_id'];
            
            try {
                // Verificar se o participante está inscrito
                $stmt = $pdo->prepare("SELECT id FROM inscricoes_atividades WHERE participante_id = :participante_id AND atividade_id = :atividade_id");
                $stmt->execute([
                    ':participante_id' => $participante_id,
                    ':atividade_id' => $atividade_id
                ]);
                
                if ($stmt->rowCount() > 0) {
                    // Cancelar a inscrição
                    $stmt = $pdo->prepare("DELETE FROM inscricoes_atividades WHERE participante_id = :participante_id AND atividade_id = :atividade_id");
                    $stmt->execute([
                        ':participante_id' => $participante_id,
                        ':atividade_id' => $atividade_id
                    ]);
                    
                    $response['success'] = true;
                    $response['message'] = "Inscrição cancelada com sucesso!";
                } else {
                    $response['success'] = false;
                    $response['message'] = "Você não está inscrito nesta atividade.";
                }
            } catch (PDOException $e) {
                $response['success'] = false;
                $response['message'] = "Erro ao processar cancelamento: " . $e->getMessage();
            }
        }
        break;
        
    case 'carregar':
        // Buscar atividades em que o participante está inscrito
        $stmt = $pdo->prepare("SELECT a.* FROM atividades a 
                              INNER JOIN inscricoes_atividades i ON a.id = i.atividade_id 
                              WHERE i.participante_id = :participante_id 
                              ORDER BY a.data, a.horario");
        $stmt->execute([':participante_id' => $usuario['id']]);
        $atividades_inscritas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($atividades_inscritas) > 0) {
            foreach ($atividades_inscritas as $atividade) {
                echo '
                <div class="atividade-card">
                    <div class="atividade-header">
                        <h3 class="atividade-title">
                            <span class="atividade-tipo">'.ucfirst($atividade['tipo']).'</span>
                            '.htmlspecialchars($atividade['titulo']).'
                        </h3>
                        <div class="atividade-vagas">
                            Inscrito
                        </div>
                    </div>
                    
                    <div class="atividade-meta">
                        <div class="atividade-meta-item">
                            <i class="fas fa-calendar"></i>
                            '.date('d/m/Y', strtotime($atividade['data'])).'
                        </div>
                        <div class="atividade-meta-item">
                            <i class="fas fa-clock"></i>
                            '.substr($atividade['hora_inicio'], 0, 5).' - '.substr($atividade['hora_fim'], 0, 5).'
                        </div>
                        <div class="atividade-meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            '.htmlspecialchars($atividade['sala']).'
                        </div>
                    </div>
                    
                    <div class="atividade-actions">
                        <button class="btn-primary btn-cancelar-atividade" data-atividade-id="'.$atividade['id'].'" style="background: linear-gradient(45deg, var(--error-color), #ff6b6b);">
                            <i class="fas fa-times"></i> Cancelar Inscrição
                        </button>
                    </div>
                </div>';
            }
        } else {
            echo '<div class="message">Você ainda não se inscreveu em nenhuma atividade.</div>';
        }
        exit;
        
    case 'editar_dados':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '';
            $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
            $instituicao = filter_input(INPUT_POST, 'instituicao', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
            $senha_atual = $_POST['senha_atual'] ?? '';
            $nova_senha = $_POST['nova_senha'] ?? '';
            $confirmar_senha = $_POST['confirmar_senha'] ?? '';
            $senha_visible = $_POST['senha_visible'] ?? '0';
            
            try {
                // Verificar se o email já existe (apenas se foi alterado)
                if ($email !== $usuario['email']) {
                    $stmt = $pdo->prepare("SELECT id FROM participantes WHERE email = :email AND id != :id");
                    $stmt->execute([':email' => $email, ':id' => $usuario['id']]);
                    if ($stmt->fetch()) {
                        $response['success'] = false;
                        $response['message'] = "Este e-mail já está sendo usado por outro usuário.";
                        echo json_encode($response);
                        exit;
                    }
                }
                
                // Iniciar transação
                $pdo->beginTransaction();
                
                // Atualizar dados básicos
                $stmt = $pdo->prepare("UPDATE participantes SET nome = :nome, email = :email, telefone = :telefone, instituicao = :instituicao WHERE id = :id");
                $stmt->execute([
                    ':nome' => $nome,
                    ':email' => $email,
                    ':telefone' => $telefone,
                    ':instituicao' => $instituicao,
                    ':id' => $usuario['id']
                ]);
                
                // Verificar se há alteração de senha
                if ($senha_visible === '1') {
                    if (!empty($senha_atual)) {
                        // Verificar se a senha atual está correta
                        $stmt = $pdo->prepare("SELECT senha FROM participantes WHERE id = :id");
                        $stmt->execute([':id' => $usuario['id']]);
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if (password_verify($senha_atual, $result['senha'])) {
                            if ($nova_senha === $confirmar_senha) {
                                // Atualizar a senha
                                $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                                $stmt = $pdo->prepare("UPDATE participantes SET senha = :senha WHERE id = :id");
                                $stmt->execute([':senha' => $nova_senha_hash, ':id' => $usuario['id']]);
                                
                                $response['message'] = "Dados e senha alterados com sucesso!";
                            } else {
                                $pdo->rollBack();
                                $response['success'] = false;
                                $response['message'] = "As novas senhas não coincidem!";
                                echo json_encode($response);
                                exit;
                            }
                        } else {
                            $pdo->rollBack();
                            $response['success'] = false;
                            $response['message'] = "Senha atual incorreta!";
                            echo json_encode($response);
                            exit;
                        }
                    } else {
                        $pdo->rollBack();
                        $response['success'] = false;
                        $response['message'] = "Para alterar a senha, é necessário informar a senha atual.";
                        echo json_encode($response);
                        exit;
                    }
                }
                
                $pdo->commit();
                $response['success'] = true;
                if (!isset($response['message'])) {
                    $response['message'] = "Dados alterados com sucesso!";
                }
                
                // Atualizar dados na sessão
                $_SESSION['usuario']['nome'] = $nome;
                $_SESSION['usuario']['email'] = $email;
                $_SESSION['usuario']['telefone'] = $telefone;
                $_SESSION['usuario']['instituicao'] = $instituicao;
                
            } catch (PDOException $e) {
                $pdo->rollBack();
                $response['success'] = false;
                $response['message'] = "Erro ao atualizar dados: " . $e->getMessage();
            }
        } else {
            $response['success'] = false;
            $response['message'] = "Método não permitido.";
        }
        break;
        
    default:
        $response['success'] = false;
        $response['message'] = "Ação não especificada.";
}

header('Content-Type: application/json');
echo json_encode($response);
