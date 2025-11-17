<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login | Amamenta+</title>
  <link rel="stylesheet" href="/SistemaBL/style/login.css">

  <script>
    // Função para aplicar máscara de CPF
    function mascaraCPF(i) {
      var v = i.value;

      // Remove tudo que não for número
      v = v.replace(/\D/g, "");

      // Aplica a máscara
      v = v.replace(/^(\d{3})(\d)/, "$1.$2");
      v = v.replace(/^(\d{3})\.(\d{3})(\d)/, "$1.$2.$3");
      v = v.replace(/\.(\d{3})(\d)/, ".$1-$2");

      i.value = v;
    }
  </script>
</head>
<body>
  <div class="login-container">
    <form class="login-form" action="processamento/login.php" method="POST">
      <h2>Bem-vindo(a) ao <span>Amamenta+</span></h2>

      <div class="form-group">
        <label class="obrigatorio" for="usuario">Usuário:</label>
        <input type="text" id="usuario" name="usuario" placeholder="Digite seu CPF (Apenas números)" oninput="mascaraCPF(this)" maxlength="14" />
      </div>

      <div class="form-group">
        <label class="obrigatorio" for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required />
      </div>

      <input type="submit" id="btnEntrar" value="Entrar" />
    </form>
  </div>
</body>
</html>
