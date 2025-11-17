<?php
include(__DIR__ . '/processamento/conexao.php');
include "processamento/verifica-funcionario.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// Verifica se o ID foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID do bebê não informado.");
}

$id = intval($_GET['id']);

// Busca os dados do bebê
$sql = "SELECT * FROM bebes WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    die("Bebê não encontrado.");
}

$bebe = $result->fetch_assoc();

// Verifica se mensagem de sucesso da edição deve aparecer
$editado = isset($_GET['msg']) && $_GET['msg'] === 'editado';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Editar Bebê</title>
  <link rel="stylesheet" href="/SistemaBL/style/style.css">
  <link rel="stylesheet" href="/SistemaBL/style/tabela.css">
  <style>
    /* Popup overlay */
    .popup-overlay {
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 1000;
      padding: 1rem;
      background: transparent;
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
      font-family: 'Arial', sans-serif;
      animation: fadeIn 0.3s ease;
    }

    .popup h3 { margin-bottom: 1rem; color: var(--verde-escuro); font-size: 1.5rem; }
    .popup p { margin-bottom: 1rem; font-size: 1rem; }

    @keyframes fadeIn { from { opacity:0; transform: translateY(-20px); } to { opacity:1; transform: translateY(0); } }

    @media (max-width: 500px){
      .popup { padding: 1.5rem; }
      .popup h3 { font-size: 1.2rem; }
      .popup p { font-size: 0.9rem; }
    }
  </style>
</head>
<body>
<?php include "header.php"; ?>

<h2>Editar Bebê</h2>
<form action="processamento/processa-edicao-bebe.php" method="post">
  <input type="hidden" name="id" value="<?= $bebe['id'] ?>">

  <div class="form-grid">
    <div class="form-group">
      <label class="obrigatorio" for="nomebebe">Nome completo:</label>
      <input type="text" id="nomebebe" name="nomebebe" value="<?= htmlspecialchars($bebe['nome_bebe']) ?>" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="sexobebe">Sexo:</label>
      <select id="sexobebe" name="sexobebe" required>
        <option value="">Selecione o sexo do bebê</option>
        <option value="masculino" <?= $bebe['sexo_bebe']=='masculino'?'selected':'' ?>>Masculino</option>
        <option value="feminino" <?= $bebe['sexo_bebe']=='feminino'?'selected':'' ?>>Feminino</option>
      </select>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="cpfbebe">CPF:</label>
      <input type="text" id="cpfbebe" name="cpfbebe" value="<?= htmlspecialchars($bebe['cpf_bebe']) ?>" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="data_nascimentobebe">Data de nascimento:</label>
      <input type="date" id="data_nascimentobebe" name="data_nascimentobebe" value="<?= $bebe['data_nascimento_bebe'] ?>" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="unidsaude">Unidade de Saúde:</label>
      <select id="unidsaude" name="unidsaude" required>
        <option value="">Selecione a unidade</option>
        <option value="ubs1" <?= $bebe['unidade_saude']=='ubs1'?'selected':'' ?>>UBS Central</option>
        <option value="ubs2" <?= $bebe['unidade_saude']=='ubs2'?'selected':'' ?>>UBS Leste</option>
        <option value="ubs3" <?= $bebe['unidade_saude']=='ubs3'?'selected':'' ?>>UBS Oeste</option>
      </select>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="sitclinica">Situação Clínica:</label>
      <select id="sitclinica" name="sitclinica" required>
        <option value="">Selecione a situação</option>
        <option value="prematuridade_extrema" <?= $bebe['situacao_clinica']=='prematuridade_extrema'?'selected':'' ?>>Prematuridade extrema</option>
        <option value="baixo_peso" <?= $bebe['situacao_clinica']=='baixo_peso'?'selected':'' ?>>Baixo peso ao nascer</option>
        <option value="dificuldade_succao" <?= $bebe['situacao_clinica']=='dificuldade_succao'?'selected':'' ?>>Dificuldade de sucção</option>
        <option value="mae_temporariamente_impossibilitada" <?= $bebe['situacao_clinica']=='mae_temporariamente_impossibilitada'?'selected':'' ?>>Impossibilidade temporária da mãe amamentar</option>
        <option value="doencas_congenitas" <?= $bebe['situacao_clinica']=='doencas_congenitas'?'selected':'' ?>>Doenças congênitas</option>
        <option value="reposicao_pos_diarréia" <?= $bebe['situacao_clinica']=='reposicao_pos_diarréia'?'selected':'' ?>>Reposição após vômitos ou diarreias severas</option>
        <option value="uti_neonatal" <?= $bebe['situacao_clinica']=='uti_neonatal'?'selected':'' ?>>Internação em UTI neonatal</option>
        <option value="bebe_orfao" <?= $bebe['situacao_clinica']=='bebe_orfao'?'selected':'' ?>>Bebê órfão ou abandonado</option>
      </select>
    </div>
  </div>

  <div class="form-group full-width">
    <label for="observacoesbebe">Observações médicas:</label>
    <textarea id="observacoesbebe" name="observacoesbebe" rows="4"><?= htmlspecialchars($bebe['observacoes_bebe']) ?></textarea>
  </div>

  <h2>Informações do Responsável</h2>
  <div class="form-grid">
    <div class="form-group">
      <label class="obrigatorio" for="nomerespbebe">Nome completo:</label>
      <input type="text" id="nomerespbebe" name="nomerespbebe" value="<?= htmlspecialchars($bebe['nome_responsavel']) ?>" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="sexoresp">Sexo:</label>
      <select id="sexoresp" name="sexoresp" required>
        <option value="">Selecione o sexo do responsável</option>
        <option value="masculino" <?= $bebe['sexo_responsavel']=='masculino'?'selected':'' ?>>Masculino</option>
        <option value="feminino" <?= $bebe['sexo_responsavel']=='feminino'?'selected':'' ?>>Feminino</option>
        <option value="outro" <?= $bebe['sexo_responsavel']=='outro'?'selected':'' ?>>Outro</option>
      </select>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="pronomesresp">Pronomes:</label>
      <select id="pronomesresp" name="pronomesresp" required>
        <option value="">Selecione os pronomes do responsável</option>
        <option value="ele-dele" <?= $bebe['pronomes_responsavel']=='ele-dele'?'selected':'' ?>>Ele/Dele</option>
        <option value="ela-dela" <?= $bebe['pronomes_responsavel']=='ela-dela'?'selected':'' ?>>Ela/Dela</option>
        <option value="outro" <?= $bebe['pronomes_responsavel']=='outro'?'selected':'' ?>>Outro</option>
      </select>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="cpfrespbebe">CPF do responsável:</label>
      <input type="text" id="cpfrespbebe" name="cpfrespbebe" value="<?= htmlspecialchars($bebe['cpf_responsavel']) ?>" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="data_nascimentorespbebe">Data de nascimento:</label>
      <input type="date" id="data_nascimentorespbebe" name="data_nascimentorespbebe" value="<?= $bebe['data_nascimento_responsavel'] ?>" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="telrespbebe">Telefone:</label>
      <input type="text" id="telrespbebe" name="telrespbebe" value="<?= htmlspecialchars($bebe['telefone_responsavel']) ?>" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="ceprespbebe">CEP:</label>
      <input type="text" id="ceprespbebe" name="ceprespbebe" value="<?= htmlspecialchars($bebe['cep_responsavel']) ?>" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="endrespbebe">Endereço:</label>
      <input type="text" id="endrespbebe" name="endrespbebe" value="<?= htmlspecialchars($bebe['endereco_responsavel']) ?>" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="bairrorespbebe">Bairro:</label>
      <input type="text" id="bairrorespbebe" name="bairrorespbebe" value="<?= htmlspecialchars($bebe['bairro_responsavel']) ?>" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="numrespbebe">Número:</label>
      <input type="text" id="numrespbebe" name="numrespbebe" value="<?= htmlspecialchars($bebe['numero_responsavel']) ?>" required>
    </div>
  </div>

  <input type="submit" value="Atualizar">
</form>

<?php if ($editado): ?>
<div class="popup-overlay" id="popupEdited">
  <div class="popup">
    <h3>Sucesso!</h3>
    <p>O bebê foi atualizado com sucesso.</p>
  </div>
</div>
<?php endif; ?>

<?php include "footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const popupEdited = document.getElementById('popupEdited');
  if(popupEdited){
    popupEdited.style.display = 'flex';
    setTimeout(() => { window.location.href = 'visualizar-receptores.php'; }, 2000);
  }
});
</script>
</body>
</html>
