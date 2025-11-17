<?php include "processamento/verifica-funcionario.php"; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cadastrar Funcionário | Amamenta+</title>
  <link rel="stylesheet" href="/SistemaBL/style/login.css"> <!-- Reaproveita o CSS -->
</head>
<body>
  <div class="login-container">
    <form class="login-form" action="processamento/processa-funcionario.php" method="POST">
      <h2>Cadastro de <span>Funcionário</span></h2>

      <div class="form-group">
        <label class="obrigatorio" for="nome">Nome completo:</label>
        <input type="text" id="nome" name="nome" placeholder="Digite o nome completo" required />
      </div>

      <div class="form-group">
        <label class="obrigatorio" for="cpf">CPF:</label>
        <input type="text" id="cpf" name="cpf" placeholder="Digite o CPF" required />
      </div>

      <div class="form-group">
        <label class="obrigatorio" for="email">E-mail:</label>
        <input type="text" id="email" name="email" placeholder="Digite o email" required />
      </div>

      <div class="form-group">
        <label class="obrigatorio" for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" placeholder="Digite a senha" required />
      </div>

      <div class="form-group">
        <label class="obrigatorio" for="nivel">Nível de acesso:</label>
        <select id="nivel" name="nivel" required>
          <option value="">Selecione o nível</option>
          <option value="admin">Administrador</option>
          <option value="funcionario">Funcionário</option>
        </select>
      </div>

      <input type="submit" value="Cadastrar" />

      <?php if(isset($_GET['sucesso']) && $_GET['sucesso'] == 1): ?>
        <p id="msg-sucesso" style="color:green; text-align:center; margin-top:10px;">Cadastrado com sucesso!</p>
        <script>
          setTimeout(function(){
            window.location.href = 'visualizar-funcionarios.php';
          }, 3000);
        </script>
      <?php endif; ?>
    </form>
  </div>
</body>
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
</html>
