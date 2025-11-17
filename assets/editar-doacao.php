<?php
include(__DIR__ . '/processamento/conexao.php');
include "processamento/verifica-funcionario.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// Verifica se o ID foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID da doação não informado.");
}

$id = intval($_GET['id']);

// Busca os dados da doação
$sql = "SELECT doacoes.*, doadoras.nome AS doadora_nome 
        FROM doacoes 
        INNER JOIN doadoras ON doacoes.doadora_id = doadoras.id 
        WHERE doacoes.id = $id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    die("Doação não encontrada.");
}

$doacao = $result->fetch_assoc();

// Verifica se mensagem de sucesso deve aparecer
$editado = isset($_GET['msg']) && $_GET['msg'] === 'editado';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Editar Doação</title>
<link rel="stylesheet" href="/SistemaBL/style/style.css">
<link rel="stylesheet" href="/SistemaBL/style/doacao.css">
<style>
/* ================= Popup ================= */
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

.popup h3 {
  margin-bottom: 1rem;
  color: var(--verde-escuro);
  font-size: 1.5rem;
}

.popup p {
  margin-bottom: 1rem;
  font-size: 1rem;
}

@keyframes fadeIn {
  from { opacity:0; transform:translateY(-20px);}
  to { opacity:1; transform:translateY(0);}
}

/* Responsivo */
@media (max-width: 500px){
  .popup { padding: 1.5rem; }
  .popup h3 { font-size: 1.2rem; }
  .popup p { font-size: 0.9rem; }
}
</style>
</head>
<body>
<?php include "header.php"; ?>

<h2>Editar Doação</h2>

<form action="processamento/processa-doacao.php" method="post">
  <input type="hidden" name="id" value="<?= $doacao['id'] ?>">

  <div class="form-column">
    <div class="form-group">
      <label class="obrigatorio" for="doadora">Doadora:</label>
      <select id="doadora" name="doadora" required style="width: 100%;">
        <option value="<?= $doacao['doadora_id'] ?>"><?= htmlspecialchars($doacao['doadora_nome']) ?></option>
        <?php
        $sql2 = "SELECT id, nome FROM doadoras ORDER BY nome ASC";
        $res2 = $conn->query($sql2);
        while($row = $res2->fetch_assoc()){
          if($row['id'] != $doacao['doadora_id']){
            echo "<option value='{$row['id']}'>{$row['nome']}</option>";
          }
        }
        ?>
      </select>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="datadoacao">Data da doação:</label>
      <input type="date" id="datadoacao" name="datadoacao" value="<?= $doacao['data_doacao'] ?>" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="qtdleite">Quantidade de leite (ml):</label>
      <input type="number" id="qtdleite" name="qtdleite" value="<?= $doacao['quantidade_ml'] ?>" required min="1" step="1">
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="tipo_leite">Tipo de leite:</label>
      <select id="tipo_leite" name="tipo_leite" required>
        <?php
          $tipos = [
            'leite_maduro'=>'Leite Maduro', 
            'leite_transicao'=>'Leite de Transição', 
            'leite_colostro'=>'Colostro', 
            'leite_humano_pasteurizado'=>'Leite Humano Pasteurizado', 
            'leite_cru'=>'Leite Cru'
          ];
          foreach($tipos as $key => $label){
            $selected = $doacao['tipo_leite'] == $key ? 'selected' : '';
            echo "<option value='$key' $selected>$label</option>";
          }
        ?>
      </select>
    </div>
  </div>

  <input type="submit" value="Atualizar">
</form>

<!-- Popup de sucesso -->
<?php if($editado): ?>
<div class="popup-overlay" id="popupEdited">
  <div class="popup">
    <h3>Sucesso!</h3>
    <p>A doação foi atualizada com sucesso.</p>
  </div>
</div>
<?php endif; ?>

<?php include "footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const popupEdited = document.getElementById('popupEdited');
  if(popupEdited){
    popupEdited.style.display = 'flex';
    setTimeout(() => {
      window.location.href = 'visualizar-doacoes.php';
    }, 2000);
  }
});
</script>

</body>
</html>
