<?php
include(__DIR__ . '/processamento/conexao.php');
include(__DIR__ . '/processamento/verifica-funcionario.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID da doadora não informado.");
}

$id = intval($_GET['id']);

// Se for POST para exclusão
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_id'])) {
    $idExcluir = intval($_POST['excluir_id']);
    $stmt = $conn->prepare("DELETE FROM doadoras WHERE id = ?");
    $stmt->bind_param("i", $idExcluir);
    if ($stmt->execute()) {
        echo "ok";
    } else {
        http_response_code(500);
        echo "Erro ao excluir: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
    exit;
}

// Busca os dados da doadora junto com o nome do funcionário que cadastrou
$stmt = $conn->prepare("
    SELECT d.*, u.nome AS funcionario_nome
    FROM doadoras d
    LEFT JOIN usuarios u ON d.id_funcionario = u.id
    WHERE d.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Doadora não encontrada.");
}
$doadora = $result->fetch_assoc();
$stmt->close();

// Funções de CPF
function mask_cpf($cpf) {
    $digits = preg_replace('/\D+/', '', $cpf);
    if (strlen($digits) !== 11) return str_repeat('*', max(0, strlen($digits)-2)) . substr($digits, -2);
    return '***.***.***-' . substr($digits, -2);
}
function format_cpf($cpf) {
    $d = preg_replace('/\D+/', '', $cpf);
    if (strlen($d) !== 11) return $cpf;
    return substr($d,0,3) . '.' . substr($d,3,3) . '.' . substr($d,6,3) . '-' . substr($d,9,2);
}

$cpf_masked = mask_cpf($doadora['cpf']);
$cpf_full = format_cpf($doadora['cpf']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <title>Detalhes da Doadora — <?= htmlspecialchars($doadora['nome']) ?></title>
  <link rel="stylesheet" href="/SistemaBL/style/style.css">
  <link rel="stylesheet" href="/SistemaBL/style/tabela.css">
  <style>
    body { font-family: Arial,sans-serif; background:#f8f9fa; }
    .detalhes-doadora-container {
      max-width: 920px; margin:2rem auto; background:#fff;
      padding:2rem; border-radius:10px; box-shadow:0 6px 18px rgba(0,0,0,0.06);
    }
    .detalhes-doadora-title { text-align:center; color:#2c6b2f; font-size:1.6rem; margin-bottom:1.5rem; }
    .detalhes-doadora-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem 1.25rem; }
    .detalhes-doadora-field { display:flex; flex-direction:column; gap:0.35rem; }
    .detalhes-doadora-field label { font-weight:700; color:#2c6b2f; font-size:0.95rem; }
    .detalhes-doadora-value { padding:0.55rem 0.75rem; background:#f3fbf6; border-radius:8px; border:1px solid rgba(44,107,47,0.06); word-break:break-word; }
    .detalhes-doadora-actions { display:flex; gap:0.75rem; margin-top:1.25rem; flex-wrap:wrap; }
    .detalhes-doadora-link { padding:0.6rem 0.9rem; background:#007bff; color:#fff; border-radius:8px; text-decoration:none; font-weight:700; }
    .detalhes-doadora-link.secondary { background:#6c757d; }
    .detalhes-doadora-link:hover { opacity:0.9; }
    .detalhes-doadora-cpf-row { display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap; }
    .detalhes-doadora-cpf { flex:1 1 auto; padding:0.55rem 0.75rem; background:#f3fbf6; border-radius:8px; border:1px solid rgba(44,107,47,0.06); }
    .detalhes-doadora-btn { background-color:#17a2b8; color:#fff; border:none; padding:0.5rem 0.75rem; border-radius:8px; cursor:pointer; font-weight:700; font-size:0.85rem; }
    .detalhes-doadora-btn:hover { opacity:0.95; }
    .detalhes-doadora-copy-msg { color:green; font-size:0.85rem; margin-left:0.5rem; display:none; }

    /* === POPUPS EXCLUSIVOS DESTA PÁGINA === */
    .doadora-popup-overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.6);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 9999;
      animation: fadeIn 0.3s ease;
    }
    .doadora-popup-overlay.active {
      display: flex !important;
    }
    .doadora-popup {
      background: #fff;
      padding: 25px 30px;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 8px 20px rgba(0,0,0,0.3);
      animation: zoomIn 0.3s ease;
      z-index: 10000;
    }
    .doadora-popup button {
      padding: 10px 18px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.2s ease, background 0.2s ease;
    }
    .doadora-popup button:hover { transform: scale(1.05); }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes zoomIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    @media (max-width: 820px) {
      .detalhes-doadora-grid { grid-template-columns:1fr; }
      .detalhes-doadora-actions { justify-content:flex-start; }
      .detalhes-doadora-cpf-row { flex-direction:column; align-items:stretch; }
      .detalhes-doadora-btn { width:100%; }
      .doadora-popup { padding:1.5rem; font-size:0.95rem; }
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>

<div class="detalhes-doadora-container">
  <h2 class="detalhes-doadora-title">Detalhes da Doadora</h2>

  <div class="detalhes-doadora-grid">
    <div class="detalhes-doadora-field"><label>Nome:</label><div class="detalhes-doadora-value"><?= htmlspecialchars($doadora['nome']) ?></div></div>
    <div class="detalhes-doadora-field"><label>Data de Nascimento:</label><div class="detalhes-doadora-value"><?= date('d/m/Y', strtotime($doadora['data_nascimento'])) ?></div></div>
    <div class="detalhes-doadora-field"><label>Telefone:</label><div class="detalhes-doadora-value"><?= htmlspecialchars($doadora['telefone']) ?></div></div>
    <div class="detalhes-doadora-field"><label>CEP:</label><div class="detalhes-doadora-value"><?= htmlspecialchars($doadora['cep']) ?></div></div>
    <div class="detalhes-doadora-field"><label>Endereço:</label><div class="detalhes-doadora-value"><?= htmlspecialchars($doadora['endereco']) ?>, Nº <?= htmlspecialchars($doadora['numero']) ?> — <?= htmlspecialchars($doadora['bairro']) ?></div></div>

    <div class="detalhes-doadora-field"><label>CPF:</label>
      <div class="detalhes-doadora-cpf-row">
        <div id="cpfField" class="detalhes-doadora-cpf"><?= htmlspecialchars($cpf_masked) ?></div>
        <button id="toggleCpfBtn" class="detalhes-doadora-btn" type="button">Mostrar CPF</button>
        <button id="copyCpfBtn" class="detalhes-doadora-btn" type="button">Copiar CPF</button>
        <span id="copyMsg" class="detalhes-doadora-copy-msg">Copiado!</span>
      </div>
    </div>

    <div class="detalhes-doadora-field"><label>Observações médicas:</label>
      <div class="detalhes-doadora-value"><?= nl2br(htmlspecialchars($doadora['observacoes']) ?: 'Nenhuma') ?></div>
    </div>
  </div><br>

  <div class="detalhes-doadora-field">
    <label>Funcionário responsável pelo cadastro:</label>
    <div class="detalhes-doadora-value"><?= htmlspecialchars($doadora['funcionario_nome'] ?? 'Não informado') ?></div>
</div>


  <div class="detalhes-doadora-actions">
    <a href="editar-doadora.php?id=<?= $doadora['id'] ?>" class="detalhes-doadora-link">Editar</a>
    <button id="btnExcluir" class="detalhes-doadora-link" style="background-color:#dc3545; border:none;">Excluir</button>
    <a href="visualizar-doadoras.php" class="detalhes-doadora-link secondary">Voltar à Lista</a>
  </div>
</div>

<!-- === POPUPS EXCLUSIVOS DESTA PÁGINA === -->
<div class="doadora-popup-overlay" id="popupExcluir">
  <div class="doadora-popup">
    <h3>Confirmar exclusão</h3>
    <p>Tem certeza que deseja excluir <strong><?= htmlspecialchars($doadora['nome']) ?></strong>?</p>
    <div style="display:flex;justify-content:center;gap:10px;margin-top:1rem;">
      <button id="confirmExcluir" style="background-color:#2e7d32;color:white;">Sim, excluir</button>
      <button id="cancelExcluir" style="background-color:#ccc;color:black;">Cancelar</button>
    </div>
  </div>
</div>

<div class="doadora-popup-overlay" id="popupExcluido">
  <div class="doadora-popup">
    <h3>Registro excluído com sucesso!</h3>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>

<script>
(function() {
  const cpfField = document.getElementById('cpfField');
  const toggleBtn = document.getElementById('toggleCpfBtn');
  const copyBtn = document.getElementById('copyCpfBtn');
  const copyMsg = document.getElementById('copyMsg');
  const cpfFull = '<?= $cpf_full ?>';
  const cpfMasked = '<?= $cpf_masked ?>';
  let shown = false;

  // Mostrar / Ocultar CPF
  toggleBtn.addEventListener('click', () => {
    cpfField.textContent = shown ? cpfMasked : cpfFull;
    toggleBtn.textContent = shown ? 'Mostrar CPF' : 'Ocultar CPF';
    shown = !shown;
  });

  // Copiar CPF
  copyBtn.addEventListener('click', () => {
    navigator.clipboard.writeText(cpfFull).then(() => {
      copyMsg.style.display = 'inline';
      setTimeout(() => (copyMsg.style.display = 'none'), 2000);
    });
  });

  // Exclusão com popup isolado
  const btnExcluir = document.getElementById('btnExcluir');
  const popupConfirm = document.getElementById('popupExcluir');
  const btnConfirmarExcluir = document.getElementById('confirmExcluir');
  const btnCancelarExcluir = document.getElementById('cancelExcluir');
  const popupExcluido = document.getElementById('popupExcluido');

  // Abrir e fechar o popup de confirmação
  btnExcluir.addEventListener('click', () => popupConfirm.classList.add('active'));
  btnCancelarExcluir.addEventListener('click', () => popupConfirm.classList.remove('active'));
  popupConfirm.addEventListener('click', (e) => {
    if (e.target === popupConfirm) popupConfirm.classList.remove('active');
  });

  // Confirmar exclusão (corrigido para JSON)
  btnConfirmarExcluir.addEventListener('click', () => {
    popupConfirm.classList.remove('active');

    const formData = new FormData();
    formData.append('excluir_id', '<?= $doadora['id'] ?>');

    fetch('processamento/excluir-detalhes-doadora.php', {
      method: 'POST',
      body: formData,
      credentials: 'same-origin'
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === 'ok') {
          popupExcluido.classList.add('active');
          setTimeout(() => (window.location.href = 'visualizar-doadoras.php'), 1800);
        } else {
          console.error('Erro ao excluir:', data);
          alert('Erro ao excluir: ' + (data.mensagem || 'Erro desconhecido.'));
        }
      })
      .catch((error) => {
        console.error('Erro na requisição:', error);
        alert('Erro na requisição: ' + error);
      });
  });
})();
</script>

</body>
</html>
