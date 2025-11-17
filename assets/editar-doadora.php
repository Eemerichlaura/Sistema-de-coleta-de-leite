<?php
include(__DIR__ . '/processamento/conexao.php');
include "processamento/verifica-funcionario.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// Verifica se o ID foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID da doadora não informado.");
}

$id = intval($_GET['id']);

// Busca os dados da doadora
$sql = "SELECT * FROM doadoras WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    die("Doadora não encontrada.");
}

$doadora = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Editar Doadora</title>
  <link rel="stylesheet" href="/SistemaBL/style/style.css">
  <link rel="stylesheet" href="/SistemaBL/style/tabela.css">
  <style>
/* ================= POPUPS ================= */
.popup-overlay {
  position: fixed;
  top:0; left:0; right:0; bottom:0;
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 9999;
  background-color: rgba(0,0,0,0.5);
  padding: 1rem;
}

.popup-edited {
  background-color: #fff;
  color: #333;
  padding: 2rem;
  border-radius: 12px;
  max-width: 400px;
  width: 100%;
  text-align: center;
  box-shadow: 0 4px 20px rgba(0,0,0,0.2);
  font-family: 'Arial', sans-serif;
  animation: fadeInPopup 0.3s ease;
}

.popup-edited h3 { margin-bottom: 1rem; font-size: 1.5rem; color: #28a745; }
.popup-edited p { margin-bottom: 1rem; font-size: 1rem; }

@keyframes fadeInPopup {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}

@media (max-width:500px){
  .popup-edited { padding: 1.5rem; }
  .popup-edited h3 { font-size: 1.2rem; }
  .popup-edited p { font-size: 0.9rem; }
}
  </style>
</head>
<body>
<?php include "header.php"; ?>

<h2>Editar doadora</h2>

<form id="formEditDoadora" action="processamento/processa-edicao-doadora.php" method="post">
  <input type="hidden" name="id" value="<?= $doadora['id'] ?>">

  <div class="form-grid">
    <div class="form-group">
      <label class="obrigatorio" for="nome">Nome completo:</label>
      <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($doadora['nome']) ?>" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="cpf">CPF:</label>
      <input type="text" id="cpf" name="cpf" value="<?= htmlspecialchars($doadora['cpf']) ?>" required>
      <small id="cpf-erro" style="color:red; display:none;">CPF inválido</small>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="data_nascimento">Data de nascimento:</label>
      <input type="date" id="data_nascimento" name="data_nascimento" value="<?= $doadora['data_nascimento'] ?>" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="telefone">Contato/Telefone:</label>
      <input type="tel" id="telefone" name="telefone" value="<?= htmlspecialchars($doadora['telefone']) ?>" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="cep">CEP:</label>
      <input type="text" id="cep" name="cep" value="<?= htmlspecialchars($doadora['cep']) ?>" required>
      <small id="cep-erro" style="color:red; display:none;">CEP inválido</small>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="endereco">Endereço:</label>
      <input type="text" id="endereco" name="endereco" value="<?= htmlspecialchars($doadora['endereco']) ?>" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="numero">Número:</label>
      <input type="text" id="numero" name="numero" value="<?= htmlspecialchars($doadora['numero']) ?>" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="bairro">Bairro:</label>
      <input type="text" id="bairro" name="bairro" value="<?= htmlspecialchars($doadora['bairro']) ?>" required>
    </div>
  </div>

  <div class="form-group full-width">
    <label for="observacoes">Observações médicas:</label>
    <textarea id="observacoes" name="observacoes" rows="4"><?= htmlspecialchars($doadora['observacoes']) ?></textarea>
  </div>

  <input type="submit" value="Atualizar">
</form>

<!-- POPUPS -->
<div class="popup-overlay" id="popupEditedOverlay">
  <div class="popup-edited">
    <h3>Sucesso!</h3>
    <p>A doadora foi atualizada com sucesso.</p>
  </div>
</div>

<div class="popup-overlay" id="popupEditErrorOverlay">
  <div class="popup-edited">
    <h3>Erro!</h3>
    <p>Não foi possível atualizar a doadora.</p>
  </div>
</div>

<?php include "footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('formEditDoadora');
  const popupSuccess = document.getElementById('popupEditedOverlay');
  const popupError = document.getElementById('popupEditErrorOverlay');
  const cpfInput = document.getElementById('cpf');
  const cpfErro = document.getElementById('cpf-erro');
  const telefoneInput = document.getElementById('telefone');
  const cepInput = document.getElementById('cep');

  // ============== VALIDAÇÃO CPF ==============
  function isValidCPF(cpf){
    cpf = cpf.replace(/\D/g,'');
    if(cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;
    let sum = 0, rest;
    for(let i=1;i<=9;i++) sum += parseInt(cpf.substring(i-1,i)) * (11-i);
    rest = (sum*10)%11; if(rest===10||rest===11) rest=0;
    if(rest!==parseInt(cpf.substring(9,10))) return false;
    sum=0;
    for(let i=1;i<=10;i++) sum += parseInt(cpf.substring(i-1,i))*(12-i);
    rest=(sum*10)%11; if(rest===10||rest===11) rest=0;
    if(rest!==parseInt(cpf.substring(10,11))) return false;
    return true;
  }

  cpfInput.addEventListener('blur', ()=> {
    cpfErro.style.display = cpfInput.value.trim()==='' ? 'none' : (!isValidCPF(cpfInput.value)?'inline':'none');
  });

  cpfInput.addEventListener('input', ()=>{
    let v = cpfInput.value.replace(/\D/g,'');
    v=v.replace(/(\d{3})(\d)/,'$1.$2');
    v=v.replace(/(\d{3})(\d)/,'$1.$2');
    v=v.replace(/(\d{3})(\d{1,2})$/,'$1-$2');
    cpfInput.value=v;
  });

  // ============== MÁSCARA TELEFONE ==============
  telefoneInput.addEventListener('input', ()=>{
    let v=telefoneInput.value.replace(/\D/g,'');
    v=v.replace(/^(\d{2})(\d)/,'($1) $2');
    v=v.replace(/(\d{5})(\d{4})$/,'$1-$2');
    telefoneInput.value=v;
  });

  // ============== MÁSCARA CEP ==============
  cepInput.addEventListener('input', ()=>{
    let v=cepInput.value.replace(/\D/g,'');
    if(v.length>5) v=v.replace(/^(\d{5})(\d)/,'$1-$2');
    cepInput.value=v;
  });

  // ============== AUTOCOMPLETE CEP ==============
  cepInput.addEventListener('blur', ()=>{
    let cep=cepInput.value.replace(/\D/g,'');
    if(cep.length!==8){ 
      document.getElementById('cep-erro').style.display='inline'; 
      return; 
    }
    document.getElementById('cep-erro').style.display='none';
    fetch(`https://viacep.com.br/ws/${cep}/json/`)
      .then(res=>res.json())
      .then(data=>{
        if(data.erro){ 
          document.getElementById('cep-erro').style.display='inline'; 
          document.getElementById('endereco').value=''; 
          document.getElementById('bairro').value=''; 
        } else { 
          document.getElementById('endereco').value=data.logradouro; 
          document.getElementById('bairro').value=data.bairro; 
        }
      }).catch(err=>console.error(err));
  });

  // ============== ENVIO VIA FETCH ==============
  form.addEventListener('submit', e=>{
    e.preventDefault();
    if(!isValidCPF(cpfInput.value)){ 
      cpfErro.style.display='inline'; 
      cpfInput.focus(); 
      return; 
    }

    const formData=new FormData(form);
    fetch(form.action,{method:'POST',body:formData})
      .then(res=>res.text())
      .then(data=>{
        data=data.trim();
        if(data==='ok'){
          popupSuccess.style.display='flex';
          setTimeout(()=>{
            popupSuccess.style.display='none'; 
            window.location.href='visualizar-doadoras.php';
          },2000);
        } else {
          popupError.style.display='flex';
          setTimeout(()=>popupError.style.display='none',3000);
        }
      }).catch(err=>{
        console.error('Erro no fetch:',err);
        popupError.style.display='flex';
        setTimeout(()=>popupError.style.display='none',3000);
      });
  });

  // Fechar popups clicando fora
  [popupSuccess,popupError].forEach(popup=>{
    popup.addEventListener('click', e=>{
      if(e.target===popup) popup.style.display='none';
    });
  });

});
</script>

</body>
</html>
