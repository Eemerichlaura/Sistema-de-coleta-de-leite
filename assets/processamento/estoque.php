<?php
require_once 'conexao.php';
session_start();

if (isset($_POST['tipo'], $_POST['quantidade'], $_POST['acao'])) {
    $tipo = $_POST['tipo'];
    $quantidade = intval($_POST['quantidade']);
    $acao = $_POST['acao'];
    $funcionario = $_SESSION['nome'] ?? 'Desconhecido';

    // Verifica se o tipo existe no estoque
    $stmt = $conn->prepare("SELECT id, quantidade FROM estoqueleite WHERE tipo = ?");
    $stmt->bind_param("s", $tipo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $dados = $resultado->fetch_assoc();
        $novo_total = $dados['quantidade'];

        // Atualiza o total conforme a ação
        if ($acao === 'entrada') {
            $novo_total += $quantidade;
        } elseif ($acao === 'saida') {
            $novo_total = max(0, $novo_total - $quantidade);
        }

        // Atualiza a tabela de estoque
        $update = $conn->prepare("UPDATE estoqueleite SET quantidade = ? WHERE id = ?");
        $update->bind_param("ii", $novo_total, $dados['id']);
        $update->execute();

        // Registra no log de movimentações
        $log = $conn->prepare("INSERT INTO logleite (tipo, quantidade, acao, funcionario, data) VALUES (?, ?, ?, ?, NOW())");
        $log->bind_param("siss", $tipo, $quantidade, $acao, $funcionario);
        $log->execute();

        // Redireciona com sucesso
        header("Location: ../home.php?msg=atualizado");
        exit;
    } else {
        // Tipo não encontrado no estoque
        header("Location: ../home.php?msg=erro");
        exit;
    }
} else {
    // Parâmetros ausentes
    header("Location: ../home.php?msg=erro");
    exit;
}
?>
