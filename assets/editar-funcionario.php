<?php
session_start();
include "processamento/verifica-funcionario.php";
include "processamento/conexao.php";

// Verifica se o ID foi passado
if(!isset($_GET['id']) || intval($_GET['id']) <= 0){
    header("Location: visualizar-funcionarios.php");
    exit;
}

$id = intval($_GET['id']);
$erro = '';
$sucesso = '';
$funcionario = [];

// Buscar dados do funcionário
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0){
    header("Location: visualizar-funcionarios.php");
    exit;
}

$funcionario = $result->fetch_assoc();

// Atualizar funcionário
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nome = trim($_POST['nome']);
    $cpf = trim($_POST['cpf']);
    $email = trim($_POST['email']);
    $nivel = trim($_POST['nivel']);
    $senha = trim($_POST['senha']);
    $senha2 = trim($_POST['senha2']);

    if(empty($nome) || empty($cpf) || empty($email) || empty($nivel)){
        $erro = "Todos os campos obrigatórios exceto senha.";
    } elseif(!empty($senha) && $senha !== $senha2){
        $erro = "As senhas não coincidem.";
    } else {
        if(!empty($senha)){
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE usuarios SET nome=?, cpf=?, email=?, nivel=?, senha=? WHERE id=?");
            $stmt->bind_param("sssssi", $nome, $cpf, $email, $nivel, $senhaHash, $id);
        } else {
            $stmt = $conn->prepare("UPDATE usuarios SET nome=?, cpf=?, email=?, nivel=? WHERE id=?");
            $stmt->bind_param("ssssi", $nome, $cpf, $email, $nivel, $id);
        }

        if($stmt->execute()){
            $sucesso = "Editado com sucesso!";
            // Atualiza os dados do formulário
            $funcionario['nome'] = $nome;
            $funcionario['cpf'] = $cpf;
            $funcionario['email'] = $email;
            $funcionario['nivel'] = $nivel;
            // Redirecionar após 3 segundos
            echo "<script>setTimeout(function(){ window.location.href='visualizar-funcionarios.php'; }, 3000);</script>";
        } else {
            $erro = "Erro ao atualizar funcionário.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Editar Funcionário | Amamenta+</title>
<link rel="stylesheet" href="/SistemaBL/style/login.css">
</head>
<body>
<div class="login-container">
    <form class="login-form" method="POST">
        <h2>Editar <span>Funcionário</span></h2>

        <?php if($erro): ?>
            <p style="color:red; text-align:center;"><?php echo $erro; ?></p>
        <?php endif; ?>

        <div class="form-group">
            <label for="nome">Nome completo</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($funcionario['nome']); ?>" required />
        </div>

        <div class="form-group">
            <label for="cpf">CPF</label>
            <input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($funcionario['cpf']); ?>" required />
        </div>

        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($funcionario['email']); ?>" required />
        </div>

        <div class="form-group">
            <label for="nivel">Nível de acesso</label>
            <select id="nivel" name="nivel" required>
                <option value="">Selecione o nível</option>
                <option value="admin" <?php if($funcionario['nivel']=='admin') echo 'selected'; ?>>Administrador</option>
                <option value="funcionario" <?php if($funcionario['nivel']=='funcionario') echo 'selected'; ?>>Funcionário</option>
            </select>
        </div>

        <div class="form-group">
            <label for="senha">Nova Senha</label>
            <input type="password" id="senha" name="senha" placeholder="Deixe em branco para não alterar" />
        </div>

        <div class="form-group">
            <label for="senha2">Repita a Nova Senha</label>
            <input type="password" id="senha2" name="senha2" placeholder="Repita a nova senha" />
        </div>

        <input type="submit" value="Salvar Alterações" />

        <?php if($sucesso): ?>
            <p style="color:green; text-align:center; margin-top:10px;"><?php echo $sucesso; ?></p>
        <?php endif; ?>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cpfInput = document.getElementById('cpf');
    if (!cpfInput) return;

    const cpfErro = document.createElement('small');
    cpfErro.style.color = 'red';
    cpfErro.style.marginTop = '0.5rem';
    cpfErro.style.display = 'none';
    cpfErro.innerText = 'CPF inválido';
    cpfInput.parentNode.appendChild(cpfErro);

    cpfInput.addEventListener('input', () => {
        let value = cpfInput.value.replace(/\D/g, '');
        if (value.length > 11) value = value.slice(0, 11);
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        cpfInput.value = value;
    });

    function validarCPF(cpf) {
        cpf = cpf.replace(/\D/g, '');
        if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) return false;

        let soma = 0;
        for (let i = 0; i < 9; i++) soma += parseInt(cpf[i]) * (10 - i);
        let resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) resto = 0;
        if (resto !== parseInt(cpf[9])) return false;

        soma = 0;
        for (let i = 0; i < 10; i++) soma += parseInt(cpf[i]) * (11 - i);
        resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) resto = 0;
        return resto === parseInt(cpf[10]);
    }

    document.querySelector('.login-form').addEventListener('submit', function (e) {
        if (!validarCPF(cpfInput.value)) {
            e.preventDefault();
            cpfErro.style.display = 'block';
            cpfInput.style.borderColor = 'red';
        } else {
            cpfErro.style.display = 'none';
            cpfInput.style.borderColor = '';
        }
    });
});
</script>
</body>
</html>
