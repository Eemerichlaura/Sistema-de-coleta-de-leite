<?php
include(__DIR__ . '/processamento/conexao.php');
include "processamento/verifica-funcionario.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// Busca doadoras cadastradas
$sql = "SELECT id, nome FROM doadoras ORDER BY nome ASC";
$resultado = $conn->query($sql);

// Verifica se mensagem de sucesso deve aparecer
$cadastrado = isset($_GET['msg']) && $_GET['msg'] === 'cadastrado';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Formulário de Doação de Leite Materno</title>
  <link rel="stylesheet" href="/SistemaBL/style/doacao.css">
  <link rel="stylesheet" href="/SistemaBL/style/style.css">
  <style>
    .popup-overlay {
      position: fixed;
      top:0; left:0; right:0; bottom:0;
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 1000;
      padding: 1rem;
      background: rgba(0,0,0,0.2);
    }

    .popup {
      background-color: #fff;
      color: var(--preto);
      padding: 2rem;
      border-radius: 12px;
      max-width: 400px;
      width: 100%;
      text-align: center;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      animation: fadeIn 0.3s ease;
    }

    .popup h3 { margin-bottom: 1rem; color: var(--verde-escuro); font-size: 1.5rem; }
    .popup p { margin-bottom: 1rem; font-size: 1rem; }

    @keyframes fadeIn {
      from { opacity:0; transform:translateY(-20px);}
      to { opacity:1; transform:translateY(0);}
    }

    @media (max-width: 500px){
      .popup { padding: 1.5rem; }
      .popup h3 { font-size: 1.2rem; }
      .popup p { font-size: 0.9rem; }
    }
  </style>
</head>
<body>
<?php include "header.php"; ?>

<h2>Cadastrar Doação</h2>

<form id="formDoacao" action="processamento/processa-cadastro-doacao.php" method="post">

  <div class="form-column">
    <div class="form-group">
      <label class="obrigatorio" for="doadora">Doadora:</label>
      <select id="doadora" name="doadora" required style="width: 100%;">
        <option value="">Selecione a doadora</option>
        <?php
          if ($resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
              echo "<option value='{$row['id']}'>" . htmlspecialchars($row['nome']) . "</option>";
            }
          } else {
            echo "<option value=''>Nenhuma doadora cadastrada</option>";
          }
        ?>
      </select>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="datadoacao">Data da doação:</label>
      <input type="date" id="datadoacao" name="datadoacao" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="qtdleite">Quantidade de leite (ml):</label>
      <input type="number" id="qtdleite" name="qtdleite" placeholder="Quantidade em ML" required min="1" step="1">
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="tipo_leite">Tipo de leite:</label>
      <select id="tipo_leite" name="tipo_leite" required>
        <option value="">Selecione o tipo de leite</option>
        <option value="leite_maduro">Leite Maduro</option>
        <option value="leite_transicao">Leite de Transição</option>
        <option value="leite_colostro">Colostro</option>
        <option value="leite_humano_pasteurizado">Leite Humano Pasteurizado</option>
        <option value="leite_cru">Leite Cru</option>
      </select>
    </div>
  </div>

  <input type="submit" value="Cadastrar">
</form>

<!-- Popup de sucesso (depois do redirecionamento PHP) -->
<?php if ($cadastrado): ?>
<div class="popup-overlay" id="popupCadastrado">
  <div class="popup">
    <h3>Sucesso!</h3>
    <p>A doação foi cadastrada com sucesso.</p>
  </div>
</div>
<?php endif; ?>

<?php include "footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('formDoacao');



  // Popup de sucesso após redirecionamento
  const popupCadastrado = document.getElementById('popupCadastrado');
  if(popupCadastrado){
    popupCadastrado.style.display = 'flex';
    setTimeout(() => {
      window.location.href = 'visualizar-doacoes.php';
    }, 2000);
  }
});
</script>
</body>
</html>
