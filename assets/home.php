<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Amamenta+</title>
  <link rel="stylesheet" href="/SistemaBL/style/style.css">
  <link rel="shortcut icon" href="/SistemaBL/img/logo.png" type="image/x-icon">
  <?php include 'processamento/verifica-funcionario.php'; ?>

  <style>
    /* === Estilo do Popup de sucesso === */
    .popup {
      display: flex;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      justify-content: center;
      align-items: center;
      z-index: 9999;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s ease;
    }

    .popup.active {
      opacity: 1;
      pointer-events: all;
    }

    .popup-content {
      background: #fff;
      padding: 30px 40px;
      border-radius: 10px;
      text-align: center;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
      max-width: 320px;
      width: 90%;
      animation: fadeIn 0.3s ease;
    }

    .popup-content p {
      font-size: 18px;
      color: #333;
      margin-bottom: 20px;
    }

    #popup-close {
      background-color: #2e7d32;
      color: white;
      border: none;
      border-radius: 6px;
      padding: 10px 20px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease, transform 0.2s ease;
    }

    #popup-close:hover {
      background-color: #1b5e20;
      transform: scale(1.05);
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: scale(0.95);
      }
      to {
        opacity: 1;
        transform: scale(1);
      }
    }
  </style>
</head>
<body>
  <?php include "header.php"; ?>
  
  <div class="estoque-container">
    <h2>Controle de Estoque de Leite</h2>

    <div class="tipos-leite">
      <?php
      include 'processamento/conexao.php';
      $sql = "SELECT * FROM estoqueleite";
      $result = $conn->query($sql);

      while ($row = $result->fetch_assoc()):
      ?>
      <div class="caixa-leite">
        <h3>
          <?php
          switch ($row['tipo']) {
            case 'leitemaduro': echo 'Maduro'; break;
            case 'leitetransicao': echo 'Transição'; break;
            case 'colostro': echo 'Colostro'; break;
            case 'leitepasteurizado': echo 'Pasteurizado'; break;
            case 'leitecru': echo 'Leite Cru'; break;
          }
          ?>
        </h3>

        <div class="mamadeira-container">
          <div class="bico"></div>
          <div class="mamadeira">
            <div class="marcadores">
              <span>1000</span>
              <span>800</span>
              <span>600</span>
              <span>400</span>
              <span>200</span>
              <span>0</span>
            </div>

            <?php 
              $max = 10000; // 10 litros
              $percentual = min(100, ($row['quantidade'] / $max) * 100);
            ?>
            <div class="nivel" style="height: <?= $percentual ?>%;"></div>
            <div class="onda"></div>
          </div>
        </div>

        <p><?= $row['quantidade'] ?> ml</p>

        <div class="botoes">
          <button class="btn-add" data-tipo="<?= $row['tipo'] ?>" data-acao="entrada">+</button>
          <button class="btn-remove" data-tipo="<?= $row['tipo'] ?>" data-acao="saida">−</button>
        </div>

        <form 
          action="processamento/estoque.php" 
          method="POST" 
          class="form-estoque form-<?= $row['tipo'] ?>" 
          style="display:none;"
        >
          <input type="hidden" name="tipo" value="<?= $row['tipo'] ?>">
          <input type="hidden" name="acao" value="">
          <input 
            type="number" 
            name="quantidade" 
            class="input-estoque-qtd" 
            min="1" 
            placeholder="Quantidade" 
            required
          >
          <button type="submit" class="btn-confirmar-estoque">Confirmar</button>
        </form>
      </div>
      <?php endwhile; ?>
    </div>

    <div class="log-estoque">
      <h3>Movimentações Recentes</h3>
      <ul>
        <?php
        $logs = $conn->query("SELECT * FROM logleite ORDER BY data DESC LIMIT 5");
        while ($log = $logs->fetch_assoc()):
        ?>
          <li>
            <strong><?= ucfirst($log['acao']) ?></strong> de <?= $log['quantidade'] ?> ml de 
            <em><?= $log['tipo'] ?></em> por <?= $log['funcionario'] ?> em 
            <?= date('d/m/Y H:i', strtotime($log['data'])) ?>
          </li>
        <?php endwhile; ?>
      </ul>
    </div>
  </div>

  <!-- Popup de Sucesso -->
  <div id="popup" class="popup">
    <div class="popup-content">
      <p id="popup-msg"></p>
      <button id="popup-close">Fechar</button>
    </div>
  </div>

  <?php include "footer.php"; ?>

  <script src="/SistemaBL/scripts/estoque.js"></script>

  <script>
    const popup = document.getElementById('popup');
    const popupClose = document.getElementById('popup-close');
    const popupMsg = document.getElementById('popup-msg');

    // Fecha ao clicar no botão "Fechar"
    popupClose.addEventListener('click', () => {
      popup.classList.remove('active');
    });

    // Fecha ao clicar fora da área branca
    popup.addEventListener('click', (event) => {
      if (!event.target.closest('.popup-content')) {
        popup.classList.remove('active');
      }
    });

    // Função para abrir popup com mensagem
    function abrirPopup(mensagem) {
      popupMsg.textContent = mensagem;
      popup.classList.add('active');
    }

    // Exemplo de uso automático (pode remover se já fizer via PHP)
    const params = new URLSearchParams(window.location.search);
    const msg = params.get("msg");
    if (msg === "atualizado") abrirPopup("Estoque atualizado com sucesso!");
    if (msg === "erro") abrirPopup("Erro ao atualizar o estoque.");
  </script>

</body>
</html>
