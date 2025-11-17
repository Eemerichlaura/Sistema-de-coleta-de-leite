<?php
include(__DIR__ . '/processamento/conexao.php');
include(__DIR__ . '/processamento/verifica-funcionario.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID do bebê não informado.");
}

$id = intval($_GET['id']);

// Busca os dados do bebê
$sql = "SELECT * FROM bebes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Bebê não encontrado.");
}

$bebe = $result->fetch_assoc();
$stmt->close();

// Função para mascarar CPF
function mask_cpf($cpf) {
    $digits = preg_replace('/\D+/', '', $cpf);
    if (strlen($digits) !== 11) return str_repeat('*', max(0, strlen($digits)-2)) . substr($digits, -2);
    return '***.***.***-' . substr($digits, -2);
}

// Formata CPF completo
function format_cpf($cpf) {
    $d = preg_replace('/\D+/', '', $cpf);
    if (strlen($d) !== 11) return $cpf;
    return substr($d,0,3) . '.' . substr($d,3,3) . '.' . substr($d,6,3) . '-' . substr($d,9,2);
}

// CPFs
$cpf_responsavel_masked = mask_cpf($bebe['cpf_responsavel']);
$cpf_responsavel_full = format_cpf($bebe['cpf_responsavel']);

$cpf_bebe_masked = mask_cpf($bebe['cpf_bebe']);
$cpf_bebe_full = format_cpf($bebe['cpf_bebe']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<title>Detalhes do Bebê — <?= htmlspecialchars($bebe['nome_bebe']) ?></title>
<link rel="stylesheet" href="/SistemaBL/style/style.css">
<style>
/* Container principal */
.detalhes-bebe-container {
  max-width: 920px;
  margin: 2rem auto;
  background-color: #fff;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.06);
  font-family: Inter, Roboto, Arial, sans-serif;
  line-height: 1.4;
}

#btnExcluir {
  cursor: pointer;
}
.detalhes-bebe-title { text-align:center; color:#2c6b2f; margin-bottom:1.25rem; font-size:1.6rem; }
.detalhes-bebe-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem 1.25rem; }
.detalhes-bebe-field { display:flex; flex-direction:column; gap:0.35rem; }
.detalhes-bebe-field label { font-weight:700; color:#2c6b2f; font-size:0.95rem; }
.detalhes-bebe-value, .detalhes-bebe-cpf { padding:0.55rem 0.75rem; background:#f3fbf6; border-radius:8px; border:1px solid rgba(44,107,47,0.06); font-size:1rem; color:#222; word-break:break-word; }
.detalhes-bebe-cpf-row { display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap; }
.detalhes-bebe-btn { background-color:#17a2b8; color:#fff; border:none; padding:0.5rem 0.75rem; border-radius:8px; cursor:pointer; font-weight:700; font-size:0.85rem; transition: transform .12s ease, opacity .12s ease; }
.detalhes-bebe-btn:hover { transform:translateY(-2px); opacity:0.95; }
.detalhes-bebe-actions { display:flex; gap:0.75rem; margin-top:1.25rem; flex-wrap:wrap; }
.detalhes-bebe-link { display:inline-block; text-decoration:none; padding:0.6rem 0.9rem; background:#007bff; color:#fff; border-radius:8px; font-weight:700; font-size:0.95rem; transition: transform .12s ease, opacity .12s ease; border:none; }
.detalhes-bebe-link.secondary { background:#6c757d; }
.detalhes-bebe-link:hover { transform:translateY(-2px); opacity:0.95; }
.detalhes-bebe-observacoes { grid-column:1/-1; margin-top:0.5rem; }
.detalhes-bebe-observacoes .detalhes-bebe-value { min-height:80px; }
.popup-overlay { position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); display:none; justify-content:center; align-items:center; z-index:1000; padding:1rem; }
.popup { background-color:#fff; color:#222; padding:2rem; border-radius:12px; max-width:400px; width:100%; text-align:center; box-shadow:0 0 15px rgba(0,0,0,0.3); font-family:'Arial',sans-serif; animation:fadeIn 0.3s ease; }
.popup h3 { margin-bottom:1rem; color:#2c6b2f; font-size:1.5rem; }
.popup p { margin-bottom:1rem; font-size:1rem; }
@keyframes fadeIn { from { opacity:0; transform:translateY(-20px); } to { opacity:1; transform:translateY(0); } }
@media (max-width:820px) {
  .detalhes-bebe-grid { grid-template-columns:1fr; }
  .detalhes-bebe-actions { justify-content:flex-start; }
  .detalhes-bebe-cpf-row { flex-direction:column; align-items:stretch; }
  .detalhes-bebe-btn { width:100%; }
}
.detalhes-bebe-copy-msg { color:green; font-size:0.85rem; margin-left:0.5rem; display:none; }
</style>
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>

<div class="detalhes-bebe-container" role="main" aria-labelledby="detalhes-title">
  <h2 id="detalhes-title" class="detalhes-bebe-title">Detalhes do Bebê</h2>

<div class="detalhes-bebe-grid">
    <!-- Dados do bebê -->
    <h3 style="grid-column:1/-1; color:#2c6b2f; margin-top:1rem; border-bottom:2px solid #2c6b2f; padding-bottom:0.25rem;">Dados do Bebê</h3>

    <div class="detalhes-bebe-field">
      <label>Nome do Bebê</label>
      <div class="detalhes-bebe-value"><?= htmlspecialchars($bebe['nome_bebe']) ?></div>
    </div>
    <div class="detalhes-bebe-field">
      <label>Sexo</label>
      <div class="detalhes-bebe-value"><?= ucfirst($bebe['sexo_bebe']) ?></div>
    </div>

    <div class="detalhes-bebe-field">
  <label>Unidade de Saúde</label>
  <div class="detalhes-bebe-value"><?= htmlspecialchars($bebe['unidade_saude']) ?></div>
</div>

    <div class="detalhes-bebe-field">
      <label>Data de Nascimento</label>
      <div class="detalhes-bebe-value"><?= date('d/m/Y', strtotime($bebe['data_nascimento_bebe'])) ?></div>
    </div>
    <div class="detalhes-bebe-field">
      <label>Situação Clínica</label>
      <div class="detalhes-bebe-value"><?= htmlspecialchars($bebe['situacao_clinica']) ?></div>
    </div>
    <div class="detalhes-bebe-field">
      <label>CPF do Bebê</label>
      <div class="detalhes-bebe-cpf-row">
        <div id="cpfBebeField" class="detalhes-bebe-cpf"><?= htmlspecialchars($cpf_bebe_masked) ?></div>
        <button id="toggleCpfBebeBtn" class="detalhes-bebe-btn" type="button">Mostrar CPF</button>
        <button id="copyCpfBebeBtn" class="detalhes-bebe-btn" type="button">Copiar CPF</button>
        <span id="copyBebeMsg" class="detalhes-bebe-copy-msg">Copiado!</span>
      </div>
    </div>
    <div class="detalhes-bebe-field detalhes-bebe-observacoes">
      <label>Observações médicas</label>
      <div class="detalhes-bebe-value"><?= nl2br(htmlspecialchars($bebe['observacoes_bebe'] ?: 'Nenhuma')) ?></div>
    </div>

    <!-- Separação dos dados do responsável -->
    <h3 style="grid-column:1/-1; color:#2c6b2f; margin-top:2rem; border-bottom:2px solid #2c6b2f; padding-bottom:0.25rem;">Dados do Responsável</h3>

    <div class="detalhes-bebe-field">
      <label>Nome do Responsável</label>
      <div class="detalhes-bebe-value"><?= htmlspecialchars($bebe['nome_responsavel']) ?></div>
    </div>
    <div class="detalhes-bebe-field">
      <label>Telefone do Responsável</label>
      <div class="detalhes-bebe-value"><?= htmlspecialchars($bebe['telefone_responsavel']) ?></div>
    </div>
    <div class="detalhes-bebe-field">
      <label>CPF do Responsável</label>
      <div class="detalhes-bebe-cpf-row">
        <div id="cpfRespField" class="detalhes-bebe-cpf"><?= htmlspecialchars($cpf_responsavel_masked) ?></div>
        <button id="toggleCpfRespBtn" class="detalhes-bebe-btn" type="button">Mostrar CPF</button>
        <button id="copyCpfRespBtn" class="detalhes-bebe-btn" type="button">Copiar CPF</button>
        <span id="copyRespMsg" class="detalhes-bebe-copy-msg">Copiado!</span>
      </div>
    </div>
</div>


  <div class="detalhes-bebe-actions">
    <a href="editar-bebe.php?id=<?= $bebe['id'] ?>" class="detalhes-bebe-link">Editar</a>
    <button id="btnExcluir" class="detalhes-bebe-link" style="background-color:#dc3545;">Excluir</button>
    <a href="visualizar-receptores.php" class="detalhes-bebe-link secondary">Voltar à Lista</a>
  </div>
</div>

<!-- Popups Exclusão e Sucesso (igual anterior) -->
<div class="popup-overlay" id="popupExcluir">
  <div class="popup">
    <h3>Confirmação</h3>
    <p>Deseja realmente excluir o bebê <strong><?= htmlspecialchars($bebe['nome_bebe']) ?></strong>?</p>
    <div style="display:flex; justify-content:center; gap:1rem; margin-top:1rem;">
      <button id="confirmExcluir" class="detalhes-bebe-btn" style="background-color:#dc3545;">Sim, excluir</button>
      <button id="cancelExcluir" class="detalhes-bebe-btn" style="background-color:#6c757d;">Cancelar</button>
    </div>
  </div>
</div>
<div class="popup-overlay" id="popupExcluido">
  <div class="popup">
    <h3>Sucesso!</h3>
    <p>O bebê foi excluído com sucesso.</p>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>

<script>
(function(){
  // CPF bebê toggle e copiar
  const cpfBebeField = document.getElementById('cpfBebeField');
  const toggleCpfBebeBtn = document.getElementById('toggleCpfBebeBtn');
  const copyCpfBebeBtn = document.getElementById('copyCpfBebeBtn');
  const copyBebeMsg = document.getElementById('copyBebeMsg');
  const cpfBebeFull = '<?= $cpf_bebe_full ?>';
  const cpfBebeMasked = '<?= $cpf_bebe_masked ?>';
  let bebeShown = false;

  toggleCpfBebeBtn.addEventListener('click', () => {
    if(bebeShown){
      cpfBebeField.textContent = cpfBebeMasked;
      toggleCpfBebeBtn.textContent = 'Mostrar CPF';
      bebeShown = false;
    } else {
      cpfBebeField.textContent = cpfBebeFull;
      toggleCpfBebeBtn.textContent = 'Ocultar CPF';
      bebeShown = true;
    }
  });

  copyCpfBebeBtn.addEventListener('click', () => {
    navigator.clipboard.writeText(cpfBebeFull).then(() => {
      copyBebeMsg.style.display = 'inline';
      setTimeout(() => copyBebeMsg.style.display = 'none', 2000);
    });
  });

  // CPF responsável toggle e copiar
  const cpfRespField = document.getElementById('cpfRespField');
  const toggleCpfRespBtn = document.getElementById('toggleCpfRespBtn');
  const copyCpfRespBtn = document.getElementById('copyCpfRespBtn');
  const copyRespMsg = document.getElementById('copyRespMsg');
  const cpfRespFull = '<?= $cpf_responsavel_full ?>';
  const cpfRespMasked = '<?= $cpf_responsavel_masked ?>';
  let respShown = false;

  toggleCpfRespBtn.addEventListener('click', () => {
    if(respShown){
      cpfRespField.textContent = cpfRespMasked;
      toggleCpfRespBtn.textContent = 'Mostrar CPF';
      respShown = false;
    } else {
      cpfRespField.textContent = cpfRespFull;
      toggleCpfRespBtn.textContent = 'Ocultar CPF';
      respShown = true;
    }
  });

  copyCpfRespBtn.addEventListener('click', () => {
    navigator.clipboard.writeText(cpfRespFull).then(() => {
      copyRespMsg.style.display = 'inline';
      setTimeout(() => copyRespMsg.style.display = 'none', 2000);
    });
  });

  // Exclusão
  const btnExcluir = document.getElementById('btnExcluir');
  const popupExcluir = document.getElementById('popupExcluir');
  const popupExcluido = document.getElementById('popupExcluido');
  const cancelExcluir = document.getElementById('cancelExcluir');
  const confirmExcluir = document.getElementById('confirmExcluir');

  btnExcluir.addEventListener('click', () => { popupExcluir.style.display = 'flex'; });
  cancelExcluir.addEventListener('click', () => { popupExcluir.style.display = 'none'; });

  confirmExcluir.addEventListener('click', () => {
    popupExcluir.style.display = 'none';
    fetch('processamento/excluir-bebe.php', {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:'id=<?= $bebe['id'] ?>'
    }).then(res => res.text())
    .then(data => {
      if(data.trim()==='ok'){
        popupExcluido.style.display='flex';
        setTimeout(()=>window.location.href='visualizar-receptores.php',2000);
      } else {
        alert('Erro ao excluir: ' + data);
      }
    }).catch(err => alert('Erro na requisição: ' + err));
  });
})();
</script>
</body>
</html>
