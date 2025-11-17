<?php include "processamento/verifica-funcionario.php"; ?>
<?php
// conexão com banco
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "bancodeleite";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Buscar bebês cadastrados
$sql = "SELECT id, nome_bebe FROM bebes ORDER BY nome_bebe ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Formulário de Retirada de Leite Materno</title>
  <link rel="stylesheet" href="/SistemaBL/style/retirada.css">
  <link rel="stylesheet" href="/SistemaBL/style/style.css">

  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <style>
  /* ================= Popup de sucesso ================= */
  .popup-overlay.popup-success {
    position: fixed;
    top:0; left:0; right:0; bottom:0;
    background-color: rgba(0,0,0,0.5);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1000;
  }
  .popup-overlay.popup-success .popup {
    background-color: #fff;
    padding: 2rem;
    border-radius: 10px;
    text-align: center;
    max-width: 400px;
    width: 90%;
    box-shadow: 0 0 10px rgba(0,0,0,0.3);
  }
  .popup-overlay.popup-success h3 { color: #28a745; margin-bottom: 1rem; }
  </style>
</head>
<body>
  <?php include "header.php"; ?>

  <main>
    <div class="doacoes-container">
      <h2>Cadastrar Retirada</h2>

      <form action="processamento/processa-retirada.php" method="post">
        <div class="form-column">

          <!-- Select nome do bebê -->
          <div class="form-group">
            <label class="obrigatorio" for="nomebebe">Nome do bebê:</label>
            <select id="nomebebe" name="nomebebe" required style="width: 100%;">
              <option value="">Selecione o bebê</option>
              <?php
              if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                      echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['nome_bebe']) . '</option>';
                  }
              }
              ?>
            </select>
          </div>

          <!-- Data da retirada -->
          <div class="form-group">
            <label class="obrigatorio" for="dataretirada">Data da retirada:</label>
            <input type="date" id="dataretirada" name="dataretirada" required>
          </div>

          <!-- Tipo de leite -->
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

          <!-- Quantidade de leite -->
          <div class="form-group">
            <label class="obrigatorio" for="qtdleite">Quantidade de leite (ml):</label>
            <input type="number" id="qtdleite" name="qtdleite" placeholder="Quantidade em ML" required min="1" step="1">
          </div>

        </div>

        <input type="submit" value="Enviar">
      </form>

      <!-- Popup de sucesso -->
      <div class="popup-overlay popup-success" id="popupSuccess">
        <div class="popup">
          <h3>Sucesso!</h3>
          <p>Retirada cadastrada com sucesso.</p>
        </div>
      </div>

    </div>
  </main>

  <?php include "footer.php"; ?>

  <!-- jQuery (necessário para Select2) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script>
    $(document).ready(function() {
      $('#nomebebe').select2({
        placeholder: "Selecione o bebê",
        allowClear: true,
        width: 'resolve'
      });
    });

    // ================= Popup de sucesso =================
    document.addEventListener('DOMContentLoaded', () => {
      const popupSuccess = document.getElementById('popupSuccess');

      // Verifica se a URL tem ?msg=cadastrado
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('msg') === 'cadastrado') {
        popupSuccess.style.display = 'flex';
        // Oculta automaticamente após 2 segundos
        setTimeout(() => {
          popupSuccess.style.display = 'none';
          // Remove o parâmetro da URL
          window.history.replaceState({}, document.title, window.location.pathname);
        }, 2000);
      }

      // Fecha o popup se clicar fora
      popupSuccess.addEventListener('click', e => {
        if (e.target === popupSuccess) popupSuccess.style.display = 'none';
      });
    });
  </script>

</body>
</html>

<?php $conn->close(); ?>
