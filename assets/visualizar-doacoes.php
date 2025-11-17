<?php
include(__DIR__ . '/processamento/conexao.php');
if (session_status() === PHP_SESSION_NONE) session_start();
include "processamento/verifica-funcionario.php";

// Barra de pesquisa
$search = '';
if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $search = trim($_GET['q']);
    $sql = "SELECT doacoes.id, doadoras.nome AS doadora_nome, doacoes.data_doacao, doacoes.quantidade_ml, doacoes.tipo_leite
            FROM doacoes
            INNER JOIN doadoras ON doacoes.doadora_id = doadoras.id
            WHERE doadoras.nome LIKE ?
            ORDER BY doacoes.data_doacao DESC";
    $stmt = $conn->prepare($sql);
    $like = "%$search%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT doacoes.id, doadoras.nome AS doadora_nome, doacoes.data_doacao, doacoes.quantidade_ml, doacoes.tipo_leite
            FROM doacoes
            INNER JOIN doadoras ON doacoes.doadora_id = doadoras.id
            ORDER BY doacoes.data_doacao DESC";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Visualizar Doações</title>
<link rel="stylesheet" href="/SistemaBL/style/style.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<style>
:root {
  --verde-escuro: #2c6b2f;
  --verde-menta-suave: #b0e0c6;
  --preto: #000;
}

/* ================= Body e main para footer fixo ================= */
body {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  margin: 0;
}
main {
  flex: 1;
}

/* ================= Container principal ================= */
.doacoes-container {
  max-width: 1100px;
  margin: 2rem auto;
  background-color: #fff;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

/* ================= Título ================= */
.doacoes-container h2 {
  text-align: center;
  color: var(--verde-escuro);
  margin-bottom: 1.5rem;
}

/* ================= Barra de pesquisa ================= */
form.search-form {
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
}
.search-input-wrapper {
    position: relative;
    flex: 1;
}
.search-input-wrapper input[type="text"] {
    width: 100%;
    padding: 0.5rem 1rem 0.5rem 2.5rem;
    border: 1px solid #ccc;
    border-radius: 25px;
    font-size: 1rem;
}
.search-input-wrapper input[type="text"]:focus {
    outline: none;
    border-color: var(--verde-escuro);
    box-shadow: 0 0 8px rgba(44,107,47,0.3);
}
.search-input-wrapper .search-icon {
    position: absolute;
    top: 50%;
    left: 10px;
    transform: translateY(-50%);
    color: #888;
    pointer-events: none;
}

/* ================= Tabela ================= */
.doacoes-container .styled-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 1rem;
}
.doacoes-container .styled-table thead {
  background-color: var(--verde-menta-suave);
  color: var(--preto);
}
.doacoes-container .styled-table th,
.doacoes-container .styled-table td {
  border: 1px solid #ddd;
  padding: 12px 15px;
  text-align: center;
}
.doacoes-container .styled-table tbody tr:nth-child(even) {
  background-color: #f9f9f9;
}

/* ================= Ações ================= */
.doacoes-container .acoes {
  display: flex;
  justify-content: center;
  gap: 10px;
}
.doacoes-container .acoes a {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 6px 12px;
  border-radius: 6px;
  color: white;
  text-decoration: none;
  font-weight: bold;
  transition: all 0.3s ease;
}
.doacoes-container .acoes a.editar { background-color: #007bff; }
.doacoes-container .acoes a.excluir { background-color: #dc3545; }
.doacoes-container .acoes a:hover {
  opacity: 0.9;
  transform: scale(1.05);
}

/* ================= Popup ================= */
.doacoes-container .popup-overlay {
  position: fixed;
  top:0; left:0; right:0; bottom:0;
  background-color: rgba(0,0,0,0.5);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}
.doacoes-container .popup {
  background-color: #fff;
  padding: 2rem;
  border-radius: 10px;
  text-align: center;
  max-width: 400px;
  width: 90%;
  box-shadow: 0 0 10px rgba(0,0,0,0.3);
}
.doacoes-container .popup h3 { margin-bottom: 1rem; color: var(--verde-escuro);}
.doacoes-container .popup p { margin-bottom: 2rem; font-size: 1rem;}
.doacoes-container .popup p span.nome-doacao { font-weight: bold; color: #dc3545; }
.doacoes-container .popup-buttons { display:flex; justify-content:center; gap:15px; }
.doacoes-container .popup-buttons button { padding: 8px 16px; border:none; border-radius:6px; font-weight:bold; cursor:pointer; transition: all 0.2s ease; }
.doacoes-container .popup-buttons .confirm { background-color: #dc3545; color:#fff; }
.doacoes-container .popup-buttons .cancel { background-color: #6c757d; color:#fff; }
.doacoes-container .popup-buttons button:hover { opacity:0.9; transform: scale(1.05); }

/* ================= Popup de sucesso ================= */
.doacoes-container .popup-success h3 { color: #28a745; }
.doacoes-container .popup-success .confirm { background-color:#28a745; color:#fff; }

/* ================= Responsividade ================= */
@media (max-width: 768px){
  .doacoes-container { padding: 1rem; margin: 1rem; }
  .doacoes-container .styled-table th, .doacoes-container .styled-table td { font-size: 0.85rem; padding:8px; }
  .doacoes-container .acoes a { padding: 4px 8px; font-size:0.8rem; }
}
</style>
</head>
<body>
<?php include 'header.php'; ?>

<main>
  <div class="doacoes-container">
    <h2>Lista de Doações</h2>

    <!-- Barra de pesquisa -->
    <form method="GET" class="search-form">
      <div class="search-input-wrapper">
        <span class="material-icons search-icon">search</span>
        <input type="text" name="q" placeholder="Pesquisar por doadora..." value="<?= htmlspecialchars($search) ?>">
      </div>
    </form>

    <table class="styled-table">
      <thead>
        <tr>
          <th>Doadora</th>
          <th>Data</th>
          <th>Quantidade (ml)</th>
          <th>Tipo de leite</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
<?php
// Array de tipos de leite amigáveis
$tiposLeite = [
    'leite_maduro' => 'Leite Maduro',
    'leite_transicao' => 'Leite de Transição',
    'leite_colostro' => 'Colostro',
    'leite_humano_pasteurizado' => 'Leite Humano Pasteurizado',
    'leite_cru' => 'Leite Cru'
];
?>

<<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['doadora_nome']) ?></td>
            <td><?= date('d/m/Y', strtotime($row['data_doacao'])) ?></td>
            <td><?= htmlspecialchars($row['quantidade_ml']) ?></td>
            <td><?= isset($tiposLeite[$row['tipo_leite']]) ? $tiposLeite[$row['tipo_leite']] : htmlspecialchars($row['tipo_leite']) ?></td>
            <td class="acoes">
                <a class="editar" href="editar-doacao.php?id=<?= $row['id'] ?>" title="Editar">
                    <span class="material-icons">edit</span>
                </a>
                <a class="excluir" href="#" title="Excluir" data-id="<?= $row['id'] ?>" data-nome="<?= htmlspecialchars($row['doadora_nome']) ?>">
                    <span class="material-icons">delete</span>
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr><td colspan="5">Nenhuma doação encontrada.</td></tr>
<?php endif; ?>

</tbody>
    </table>

    <!-- Popup confirmação -->
    <div class="popup-overlay" id="popupOverlay">
      <div class="popup">
        <h3>Confirmação</h3>
        <p>Deseja realmente excluir a doação de <span class="nome-doacao"></span>?</p>
        <div class="popup-buttons">
          <button class="confirm" id="popupConfirm">Excluir</button>
          <button class="cancel" id="popupCancel">Cancelar</button>
        </div>
      </div>
    </div>

    <!-- Popup sucesso -->
    <div class="popup-overlay popup-success" id="popupSuccess">
      <div class="popup">
        <h3>Sucesso!</h3>
        <p>A doação foi excluída com sucesso.</p>
      </div>
    </div>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const popupOverlay = document.getElementById('popupOverlay');
  const popupConfirm = document.getElementById('popupConfirm');
  const popupCancel = document.getElementById('popupCancel');
  const popupNome = popupOverlay.querySelector('.nome-doacao');
  const popupSuccess = document.getElementById('popupSuccess');

  let deleteId = null;
  let deleteRow = null;

  document.querySelectorAll('.doacoes-container .acoes a.excluir').forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      deleteId = link.dataset.id;
      deleteRow = link.closest('tr');
      popupNome.textContent = link.dataset.nome;
      popupOverlay.style.display = 'flex';
    });
  });

  popupCancel.addEventListener('click', () => {
    popupOverlay.style.display = 'none';
    deleteId = null;
    deleteRow = null;
  });

  popupConfirm.addEventListener('click', () => {
    if(!deleteId) return;
    fetch('processamento/excluir-doacao.php', {
      method:'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded'},
      body:'id=' + encodeURIComponent(deleteId)
    })
    .then(res => res.text())
    .then(data => {
      if(data==='ok'){
        deleteRow.remove();
        popupOverlay.style.display='none';
        popupSuccess.style.display='flex';
        setTimeout(()=> popupSuccess.style.display='none',2000);
      } else {
        alert('Erro ao excluir: '+data);
      }
    })
    .catch(err => alert('Erro: '+err));
  });

  popupOverlay.addEventListener('click', e => { if(e.target===popupOverlay){popupOverlay.style.display='none';} });
  popupSuccess.addEventListener('click', e => { if(e.target===popupSuccess){popupSuccess.style.display='none';} });
});
</script>

<?php include 'footer.php'; ?>
</body>
</html>
