<?php
session_start();
include "processamento/verifica-funcionario.php"; 
include "processamento/conexao.php"; 
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Visualizar Funcionários | Amamenta+</title>
<link rel="stylesheet" href="/SistemaBL/style/style.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<style>
/* ================= Container principal ================= */
html, body {
  height: 100%;
  margin: 0;
  display: flex;
  flex-direction: column;
}

/* Container principal empurra o footer para baixo */
.funcionarios-container {
  flex: 1; /* Faz o container crescer e ocupar o espaço disponível */
}

.funcionarios-container {
  max-width: 1100px;
  margin: 2rem auto;
  background-color: #fff;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
  overflow-x: auto;
}

/* ================= Título ================= */
.funcionarios-container h2 {
  text-align: center;
  color: var(--verde-escuro);
  margin-bottom: 1.5rem;
}

/* ================= Tabela ================= */
.funcionarios-container .styled-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 1rem;
  min-width: 600px;
}

.funcionarios-container .styled-table thead {
  background-color: var(--verde-menta-suave);
  color: var(--preto);
}

.funcionarios-container .styled-table th,
.funcionarios-container .styled-table td {
  border: 1px solid #ddd;
  padding: 12px 15px;
  text-align: center;
}

.funcionarios-container .styled-table tbody tr:nth-child(even) {
  background-color: #f9f9f9;
}

/* ================= Ações ================= */
.funcionarios-container .acoes {
  display: flex;
  justify-content: center;
  gap: 10px;
}

.funcionarios-container .acoes a {
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

.funcionarios-container .acoes a.editar { background-color: #007bff; }
.funcionarios-container .acoes a.excluir { background-color: #dc3545; }

.funcionarios-container .acoes a:hover {
  opacity: 0.9;
  transform: scale(1.05);
}

/* ================= Popup ================= */
.visualizar-popup-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5);
  display: none; /* escondido inicialmente */
  justify-content: center;
  align-items: center;
  z-index: 1000;
  padding: 1rem;
}

.visualizar-popup {
  background-color: #fff;
  padding: 2rem;
  border-radius: 10px;
  text-align: center;
  max-width: 400px;
  width: 100%;
  box-shadow: 0 0 15px rgba(0,0,0,0.3);
}

.visualizar-popup h3 { margin-bottom: 1rem; color: var(--verde-escuro); }
.visualizar-popup p { margin-bottom: 1rem; }
.visualizar-popup p span.nome-funcionario { font-weight: bold; color: #dc3545; }

.visualizar-popup .popup-buttons {
  display: flex;
  justify-content: center;
  gap: 15px;
}

.visualizar-popup .popup-buttons button {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.2s ease;
}

.visualizar-popup .popup-buttons .confirm { background-color: #dc3545; color: #fff; }
.visualizar-popup .popup-buttons .cancel { background-color: #6c757d; color: #fff; }

.visualizar-popup .popup-buttons button:hover { opacity: 0.9; transform: scale(1.05); }

/* ================= Popup sucesso ================= */
#popupSuccess h3 { color: #28a745; }
#popupSuccess p { margin:0; font-weight:bold; }

/* ================= RESPONSIVIDADE ================= */
@media (max-width: 768px) {
  .funcionarios-container .styled-table th,
  .funcionarios-container .styled-table td {
    padding: 8px 10px;
    font-size: 0.9rem;
  }
  .funcionarios-container .acoes a {
    padding: 5px 10px;
    font-size: 0.8rem;
  }
}

@media (max-width: 500px) {
  .funcionarios-container .styled-table {
    font-size: 0.85rem;
  }
  .funcionarios-container .acoes {
    flex-direction: column;
    gap: 5px;
  }
  .funcionarios-container .acoes a { width: 100%; }
}
</style>
</head>
<body>
<?php include "header.php"; ?>

<div class="funcionarios-container">
  <h2>Lista de Funcionários</h2>

  <div style="overflow-x:auto;">
    <table class="styled-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>CPF</th>
          <th>E-mail</th>
          <th>Nível</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $sql = "SELECT * FROM usuarios ORDER BY nome ASC";
      $resultado = $conn->query($sql);
      if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
          echo "<tr>";
          echo "<td>".htmlspecialchars($row['id'])."</td>";
          echo "<td>".htmlspecialchars($row['nome'])."</td>";
          echo "<td>".htmlspecialchars($row['cpf'])."</td>";
          echo "<td>".htmlspecialchars($row['email'])."</td>";
          echo "<td>".htmlspecialchars($row['nivel'])."</td>";
          echo "<td class='acoes'>
                  <a href='editar-funcionario.php?id=".$row['id']."' class='editar' title='Editar'>
                    <span class='material-icons'>edit</span>
                  </a>
                  <a href='#' class='excluir' data-id='".$row['id']."' data-nome='".htmlspecialchars($row['nome'])."' title='Excluir'>
                    <span class='material-icons'>delete</span>
                  </a>
                </td>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='6'>Nenhum funcionário encontrado.</td></tr>";
      }
      ?>
      </tbody>
    </table>
  </div>

<!-- Popup confirmação -->
<div class="visualizar-popup-overlay" id="popupOverlay">
  <div class="visualizar-popup">
    <h3>Confirmação</h3>
    <p>Deseja realmente excluir o funcionário <span class="nome-funcionario"></span>?</p>
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
      <p>O funcionário foi excluído com sucesso.</p>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const popupOverlay = document.getElementById('popupOverlay');
  const popupConfirm = document.getElementById('popupConfirm');
  const popupCancel = document.getElementById('popupCancel');
  const popupNome = popupOverlay.querySelector('.nome-funcionario');
  const popupSuccess = document.getElementById('popupSuccess');

  let deleteId = null;
  let deleteRow = null;

  document.querySelectorAll('.funcionarios-container .acoes a.excluir').forEach(link => {
    link.addEventListener('click', e => {
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
    fetch('processamento/excluir-funcionario.php', {
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
  popupOverlay.addEventListener('click', e => { if(e.target===popupOverlay){popupOverlay.style.display='none';} });
  popupSuccess.addEventListener('click', e => { if(e.target===popupSuccess){popupSuccess.style.display='none';} });
});
</script>

<?php include "footer.php"; ?>
</body>
</html>
