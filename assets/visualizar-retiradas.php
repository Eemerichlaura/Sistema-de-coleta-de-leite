<?php
include(__DIR__ . '/processamento/conexao.php');
if (session_status() === PHP_SESSION_NONE) session_start();
include "processamento/verifica-funcionario.php";

// Verifica se existe termo de pesquisa
$search = '';
if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $search = trim($_GET['q']);
    $sql = "SELECT r.id, b.nome_bebe, r.data_retirada, r.quantidade_ml, r.tipo_leite, u.nome AS funcionario_nome
            FROM retiradas r
            INNER JOIN bebes b ON r.bebe_id = b.id
            INNER JOIN usuarios u ON r.id_funcionario = u.id
            WHERE b.nome_bebe LIKE ?
            ORDER BY r.data_retirada DESC";
    $stmt = $conn->prepare($sql);
    $like = "%$search%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT r.id, b.nome_bebe, r.data_retirada, r.quantidade_ml, r.tipo_leite, u.nome AS funcionario_nome
            FROM retiradas r
            INNER JOIN bebes b ON r.bebe_id = b.id
            INNER JOIN usuarios u ON r.id_funcionario = u.id
            ORDER BY r.data_retirada DESC";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Visualizar Retiradas</title>
<link rel="stylesheet" href="/SistemaBL/style/header.css">
<link rel="stylesheet" href="/SistemaBL/style/tabelar.css">
<link rel="stylesheet" href="/SistemaBL/style/style.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<style>
body { display: flex; flex-direction: column; min-height: 100vh; }
.retiradas-container { flex: 1; padding:2rem; }

/* Barra de pesquisa estilizada */
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
    transition: all 0.2s ease-in-out;
}
.search-input-wrapper input[type="text"]:focus {
    outline: none;
    border-color: #2c6b2f;
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

/* Popups */
.visualizar-popup-overlay {
  position: fixed; top:0; left:0; right:0; bottom:0;
  background: rgba(0,0,0,0.5); display: none;
  justify-content: center; align-items: center;
  z-index: 1000; padding:1rem;
}
.visualizar-popup {
  background-color: #fff; color: #000;
  padding:2rem; border-radius:10px;
  max-width:400px; width:100%; text-align:center;
  box-shadow:0 0 15px rgba(0,0,0,0.3);
}
.visualizar-popup h3 { margin-bottom:1rem; }
.visualizar-popup .popup-buttons { display:flex; justify-content:center; gap:1rem; margin-top:1rem; }
.visualizar-popup .popup-buttons button { padding:8px 16px; border:none; border-radius:6px; font-weight:bold; cursor:pointer; transition: all 0.2s ease; }
.visualizar-popup .popup-buttons .confirm { background-color:#dc3545; color:#fff; }
.visualizar-popup .popup-buttons .cancel { background-color:#6c757d; color:#fff; }
.visualizar-popup .popup-buttons button:hover { opacity:0.9; transform:scale(1.05); }
@media (max-width:500px){ .visualizar-popup { padding:1.5rem; } }
</style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="retiradas-container">
  <h2>Lista de Retiradas</h2>

  <!-- Barra de pesquisa -->
  <form method="GET" class="search-form">
    <div class="search-input-wrapper">
      <span class="material-icons search-icon">search</span>
      <input type="text" name="q" placeholder="Pesquisar por bebê..." value="<?= htmlspecialchars($search) ?>">
    </div>
  </form>

  <div style="overflow-x:auto;">
    <table class="styled-table">
      <thead>
        <tr>
          <th>Bebê</th>
          <th>Data</th>
          <th>Quantidade (ml)</th>
          <th>Tipo de Leite</th>
          <th>Funcionário</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['nome_bebe']) ?></td>
            <td><?= date('d/m/Y', strtotime($row['data_retirada'])) ?></td>
            <td><?= htmlspecialchars($row['quantidade_ml']) ?></td>
            <td><?= str_replace('_', ' ', ucfirst($row['tipo_leite'])) ?></td>
            <td><?= htmlspecialchars($row['funcionario_nome']) ?></td>
            <td class="acoes">
              <a class="editar" href="editar-retirada.php?id=<?= $row['id'] ?>" title="Editar">
                <span class="material-icons">edit</span>
              </a>
              <a class="excluir" href="#" title="Excluir" data-id="<?= $row['id'] ?>" data-nome="<?= htmlspecialchars($row['nome_bebe']) ?>">
                <span class="material-icons">delete</span>
              </a>
            </td>
          </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="6">Nenhuma retirada encontrada.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Popup confirmação -->
  <div class="visualizar-popup-overlay" id="popupOverlay">
    <div class="visualizar-popup">
      <h3>Confirmação</h3>
      <p>Deseja realmente excluir a retirada do bebê <span class="nome-doadora"></span>?</p>
      <div class="popup-buttons">
        <button class="confirm" id="popupConfirm">Excluir</button>
        <button class="cancel" id="popupCancel">Cancelar</button>
      </div>
    </div>
  </div>

  <!-- Popup sucesso -->
  <div class="visualizar-popup-overlay" id="popupSuccess">
    <div class="visualizar-popup">
      <h3>Sucesso!</h3>
      <p>A retirada foi excluída com sucesso.</p>
    </div>
  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const popupOverlay = document.getElementById('popupOverlay');
  const popupConfirm = document.getElementById('popupConfirm');
  const popupCancel = document.getElementById('popupCancel');
  const popupNome = popupOverlay.querySelector('.nome-doadora');
  const popupSuccess = document.getElementById('popupSuccess');

  let deleteId = null;
  let deleteRow = null;

  document.querySelectorAll('.retiradas-container .acoes a.excluir').forEach(link => {
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
    fetch('processamento/excluir-retirada.php', {
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

  popupOverlay.addEventListener('click', e=>{ if(e.target===popupOverlay){popupOverlay.style.display='none';} });
  popupSuccess.addEventListener('click', e=>{ if(e.target===popupSuccess){popupSuccess.style.display='none';} });
});
</script>

<?php include 'footer.php'; ?>
</body>
</html>
