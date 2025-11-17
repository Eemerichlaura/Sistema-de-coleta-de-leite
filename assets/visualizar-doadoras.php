<?php
include(__DIR__ . '/processamento/conexao.php');
if (session_status() === PHP_SESSION_NONE) session_start();
include "processamento/verifica-funcionario.php";

// Verifica se existe termo de pesquisa
$search = '';
if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $search = trim($_GET['q']);
    $sql = "SELECT id, nome FROM doadoras WHERE nome LIKE ? ORDER BY nome ASC";
    $stmt = $conn->prepare($sql);
    $like = "%$search%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT id, nome FROM doadoras ORDER BY nome ASC";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Visualizar Doadoras</title>
<link rel="stylesheet" href="/SistemaBL/style/header.css">
<link rel="stylesheet" href="/SistemaBL/style/tabela.css">
<link rel="stylesheet" href="/SistemaBL/style/style.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<style>
body { display: flex; flex-direction: column; min-height: 100vh; }
.doadoras-container { flex: 1; }

/* Popup de exclusão */
.visualizar-popup-overlay {
  position: fixed; top:0; left:0; right:0; bottom:0;
  display: none;
  justify-content: center; align-items: center;
  background: rgba(0,0,0,0.5);
  z-index: 2000;
}

.visualizar-popup {
  background-color: #fff; color: #000;
  padding: 2rem; border-radius: 12px;
  max-width: 400px; width: 100%;
  text-align: center;
  box-shadow: 0 0 15px rgba(0,0,0,0.3);
  animation: fadeIn 0.3s ease;
}

.visualizar-popup h3 { margin-bottom: 1rem; color: #2c6b2f; }
.visualizar-popup .popup-buttons { display: flex; justify-content: center; gap: 1rem; margin-top: 1rem; }
@keyframes fadeIn { from {opacity:0; transform:translateY(-20px);} to {opacity:1; transform:translateY(0);} }

/* Barra de pesquisa */
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
</style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="doadoras-container">
  <h2>Lista de Doadoras</h2>

  <!-- Barra de pesquisa -->
  <form method="GET" class="search-form">
    <div class="search-input-wrapper">
      <span class="material-icons search-icon">search</span>
      <input type="text" name="q" placeholder="Pesquisar por nome..." value="<?= htmlspecialchars($search) ?>">
    </div>
  </form>

  <table class="styled-table">
    <thead>
      <tr><th>Nome</th><th>Ações</th></tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['nome']) ?></td>
          <td class="acoes">
            <a class="visualizar" href="detalhes-doadora.php?id=<?= $row['id'] ?>" title="Visualizar">
              <span class="material-icons">visibility</span>
            </a>
            <a class="editar" href="editar-doadora.php?id=<?= $row['id'] ?>" title="Editar">
              <span class="material-icons">edit</span>
            </a>
            <a class="excluir" href="#" title="Excluir" data-id="<?= $row['id'] ?>" data-nome="<?= htmlspecialchars($row['nome']) ?>">
              <span class="material-icons">delete</span>
            </a>
          </td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="2">Nenhuma doadora encontrada.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- Popup confirmação -->
  <div class="visualizar-popup-overlay" id="popupOverlay">
    <div class="visualizar-popup">
      <h3>Confirmação</h3>
      <p>Deseja realmente excluir a doadora <span class="nome-doadora"></span>?</p>
      <div class="popup-buttons">
        <button class="confirm" id="popupConfirm" type="button" style="background-color:#dc3545;">Excluir</button>
        <button class="cancel" id="popupCancel" type="button" style="background-color:#6c757d;">Cancelar</button>
      </div>
    </div>
  </div>

  <!-- Popup sucesso -->
  <div class="visualizar-popup-overlay" id="popupSuccess">
    <div class="visualizar-popup">
      <h3>Sucesso!</h3>
      <p>A doadora foi excluída com sucesso.</p>
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

  document.querySelectorAll('.doadoras-container .acoes a.excluir').forEach(link => {
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
    fetch('processamento/excluir-doadora.php', {
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

  // Fechar popup clicando fora
  popupOverlay.addEventListener('click', e => { if(e.target===popupOverlay) popupOverlay.style.display='none'; });
  popupSuccess.addEventListener('click', e => { if(e.target===popupSuccess) popupSuccess.style.display='none'; });
});
</script>

<?php include 'footer.php'; ?>
</body>
</html>
