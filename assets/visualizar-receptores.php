<?php
include(__DIR__ . '/processamento/conexao.php');
if (session_status() === PHP_SESSION_NONE) session_start();
include "processamento/verifica-funcionario.php";

// Verifica se existe termo de pesquisa
$search = '';
if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $search = trim($_GET['q']);
    $sql = "SELECT id, nome_bebe FROM bebes WHERE nome_bebe LIKE ? ORDER BY nome_bebe ASC";
    $stmt = $conn->prepare($sql);
    $like = "%$search%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT id, nome_bebe FROM bebes ORDER BY nome_bebe ASC";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Visualizar Bebês</title>
<link rel="stylesheet" href="/SistemaBL/style/header.css">
<link rel="stylesheet" href="/SistemaBL/style/style.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<style>
/* ================= Container principal ================= */
.bebes-container {
  max-width: 1100px;
  margin: 2rem auto;
  background-color: #fff;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
  flex: 1;
  display: flex;
  flex-direction: column;
}

/* ================= Título ================= */
.bebes-container h2 {
  text-align: center;
  color: #2c6b2f;
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

/* ================= Tabela ================= */
.bebes-container .styled-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 1rem;
}

.bebes-container .styled-table thead {
  background-color: #b0e0c6;
  color: #000;
}

.bebes-container .styled-table th,
.bebes-container .styled-table td {
  border: 1px solid #ddd;
  padding: 12px 15px;
  text-align: center;
}

.bebes-container .styled-table tbody tr:nth-child(even) {
  background-color: #f9f9f9;
}

.bebes-container .styled-table tbody tr:hover {
  background-color: #f1f1f1;
}

/* ================= Ações ================= */
.bebes-container .acoes {
  display: flex;
  justify-content: center;
  gap: 10px;
}

.bebes-container .acoes a {
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

.bebes-container .acoes a.visualizar { background-color: #17a2b8; } /* azul claro */
.bebes-container .acoes a.editar { background-color: #007bff; }      /* azul escuro */
.bebes-container .acoes a.excluir { background-color: #dc3545; }     /* vermelho */

.bebes-container .acoes a:hover {
  opacity: 0.9;
  transform: scale(1.05);
}

/* ================= Popup ================= */
.bebes-container .popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.5);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.bebes-container .popup {
  background-color: #fff;
  padding: 2rem;
  border-radius: 10px;
  text-align: center;
  max-width: 400px;
  width: 90%;
  box-shadow: 0 0 10px rgba(0,0,0,0.3);
}

.bebes-container .popup h3 {
  margin-bottom: 1rem;
  color: #2c6b2f;
}

.bebes-container .popup p {
  margin-bottom: 2rem;
  font-size: 1rem;
}

.bebes-container .popup p span.nome-bebe {
  font-weight: bold;
  color: #dc3545;
}

.bebes-container .popup-buttons {
  display: flex;
  justify-content: center;
  gap: 15px;
}

.bebes-container .popup-buttons button {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.2s ease;
}

.bebes-container .popup-buttons .confirm {
  background-color: #dc3545;
  color: #fff;
}

.bebes-container .popup-buttons .cancel {
  background-color: #6c757d;
  color: #fff;
}

.bebes-container .popup-buttons button:hover {
  opacity: 0.9;
  transform: scale(1.05);
}

/* ================= Popup de sucesso ================= */
.bebes-container .popup-success h3 {
  color: #28a745; /* verde sucesso */
}

/* ================= Footer fixo ================= */
body {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

footer {
  margin-top: auto;
}

/* ================= Responsivo ================= */
@media (max-width:500px){
  .popup { padding:1.5rem; }
}
</style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="bebes-container">
  <h2>Lista de Bebês</h2>

  <!-- Barra de pesquisa -->
  <form method="GET" class="search-form">
    <div class="search-input-wrapper">
      <span class="material-icons search-icon">search</span>
      <input type="text" name="q" placeholder="Pesquisar por nome..." value="<?= htmlspecialchars($search) ?>">
    </div>
  </form>

  <table class="styled-table">
    <thead>
      <tr><th>Nome do Bebê</th><th>Ações</th></tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['nome_bebe']) ?></td>
        <td class="acoes">
          <a class="visualizar" href="detalhes-bebe.php?id=<?= $row['id'] ?>" title="Visualizar">
            <span class="material-icons">visibility</span>
          </a>
          <a class="editar" href="editar-bebe.php?id=<?= $row['id'] ?>" title="Editar">
            <span class="material-icons">edit</span>
          </a>
          <a class="excluir" href="#" title="Excluir" data-id="<?= $row['id'] ?>" data-nome="<?= htmlspecialchars($row['nome_bebe']) ?>">
            <span class="material-icons">delete</span>
          </a>
        </td>
      </tr>
      <?php endwhile; ?>
      <?php else: ?>
      <tr><td colspan="2">Nenhum bebê encontrado.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- POPUP CONFIRMAÇÃO -->
  <div class="popup-overlay" id="popupOverlay">
    <div class="popup">
      <h3>Confirmação</h3>
      <p>Deseja realmente excluir o bebê <span class="nome-bebe"></span>?</p>
      <div class="popup-buttons">
        <button class="confirm" id="popupConfirm">Excluir</button>
        <button class="cancel" id="popupCancel">Cancelar</button>
      </div>
    </div>
  </div>

  <!-- POPUP SUCESSO -->
  <div class="popup-overlay" id="popupSuccess">
    <div class="popup">
      <h3>Sucesso!</h3>
      <p>O bebê foi excluído com sucesso.</p>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const popupOverlay = document.getElementById('popupOverlay');
  const popupConfirm = document.getElementById('popupConfirm');
  const popupCancel = document.getElementById('popupCancel');
  const popupNome = popupOverlay.querySelector('.nome-bebe');
  const popupSuccess = document.getElementById('popupSuccess');

  let deleteId = null;
  let deleteRow = null;

  document.querySelectorAll('.bebes-container .acoes a.excluir').forEach(link => {
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
    fetch('processamento/excluir-bebe.php', {
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

  // Atualiza a página a cada 10s
  setInterval(() => { window.location.reload(); }, 10000);
});
</script>

<?php include 'footer.php'; ?>
</body>
</html>
