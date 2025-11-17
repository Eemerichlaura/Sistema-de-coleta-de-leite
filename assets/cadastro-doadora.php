<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Formulário de Doação de Leite Materno</title>
<link rel="stylesheet" href="/SistemaBL/style/style.css">
<style>
/* Popup overlay específico para este projeto */
#popupSuccess.popup-overlay-custom,
#popupError.popup-overlay-custom {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: rgba(0, 0, 0, 0.6);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 99999 !important; /* muito alto para sobrepor tudo */
}

/* Conteúdo do popup com borda, fundo e sombra */
#popupSuccess.popup-overlay-custom > .popup-custom,
#popupError.popup-overlay-custom > .popup-custom {
  background-color: #fff;
  padding: 2rem 2.5rem;
  border-radius: 12px;
  max-width: 420px;
  width: 90%;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.25);
  text-align: center;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Título do popup */
#popupSuccess.popup-overlay-custom > .popup-custom h3,
#popupError.popup-overlay-custom > .popup-custom h3 {
  margin-bottom: 1rem;
  font-weight: 700;
  font-size: 1.3rem;
}

/* Mensagem do popup */
#popupSuccess.popup-overlay-custom > .popup-custom p,
#popupError.popup-overlay-custom > .popup-custom p {
  font-size: 1rem;
  margin-bottom: 0.75rem;
  color: #333;
}

/* Estilos específicos para sucesso e erro */
#popupSuccess.popup-overlay-custom > .popup-custom h3 {
  color: #28a745; /* verde */
}

#popupError.popup-overlay-custom > .popup-custom h3 {
  color: #dc3545; /* vermelho */
}

/* Responsivo */
@media (max-width: 480px) {
  #popupSuccess.popup-overlay-custom > .popup-custom,
  #popupError.popup-overlay-custom > .popup-custom {
    padding: 1.5rem 1.8rem;
  }
}
</style>
</head>
<body>
<?php include "header.php"; ?>

<h2>Cadastrar Doadora</h2>
<form id="formDoadora" action="processamento/processa-doadora.php" method="post">
  <div class="form-grid">
    <div class="form-group">
      <label class="obrigatorio" for="nome">Nome completo:</label>
      <input type="text" id="nome" name="nome" placeholder="Digite o nome completo" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="cpf">CPF:</label>
      <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" required>
      <small id="cpf-erro" style="color:red; display:none;">CPF inválido</small>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="data_nascimento">Data de nascimento:</label>
      <input type="date" id="data_nascimento" name="data_nascimento" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="telefone">Contato/Telefone:</label>
      <input type="tel" id="telefone" name="telefone" placeholder="(00) 00000-0000" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="cep">CEP:</label>
      <input type="text" id="cep" name="cep" placeholder="00000-000" required>
      <small id="cep-erro" style="color:red; display:none;">CEP inválido</small>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="endereco">Endereço:</label>
      <input type="text" id="endereco" name="endereco" placeholder="Digite o endereço" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="numero">Número:</label>
      <input type="text" id="numero" name="numero" placeholder="Digite o número" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="bairro">Bairro:</label>
      <input type="text" id="bairro" name="bairro" placeholder="Digite o bairro" required>
    </div>

    <div class="form-group full-width">
      <label for="observacoes">Observações médicas:</label>
      <textarea id="observacoes" name="observacoes" placeholder="Alergias, medicamentos, etc..." rows="4"></textarea>
    </div>
  </div>
  <input type="submit" value="Enviar">
</form>

<!-- Popups com classes específicas para evitar conflito -->
<div class="popup-overlay popup-overlay-custom" id="popupSuccess">
  <div class="popup-custom">
    <h3>Cadastro efetuado com sucesso!</h3>
    <p>Redirecionando para a lista de doadoras...</p>
  </div>
</div>

<div class="popup-overlay popup-overlay-custom" id="popupError">
  <div class="popup-custom">
    <h3>⚠️ Erro!</h3>
    <p>CPF já cadastrado.</p>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('formDoadora');
  const popupSuccess = document.getElementById('popupSuccess');
  const popupError = document.getElementById('popupError');
  const cpfInput = document.getElementById('cpf');
  const cpfErro = document.getElementById('cpf-erro');
  const telefoneInput = document.getElementById('telefone');
  const cepInput = document.getElementById('cep');

  // ================= VALIDAÇÃO CPF =================
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

  cpfInput.addEventListener('input', ()=> {
    let v = cpfInput.value.replace(/\D/g,'');
    v=v.replace(/(\d{3})(\d)/,'$1.$2');
    v=v.replace(/(\d{3})(\d)/,'$1.$2');
    v=v.replace(/(\d{3})(\d{1,2})$/,'$1-$2');
    cpfInput.value=v;
  });

  // ================= MÁSCARA TELEFONE =================
  telefoneInput.addEventListener('input', ()=>{
    let v=telefoneInput.value.replace(/\D/g,'');
    v=v.replace(/^(\d{2})(\d)/,'($1) $2');
    v=v.replace(/(\d{5})(\d{4})$/,'$1-$2');
    telefoneInput.value=v;
  });

  // ================= MÁSCARA CEP =================
  cepInput.addEventListener('input', ()=>{
    let v=cepInput.value.replace(/\D/g,'');
    if(v.length>5) v=v.replace(/^(\d{5})(\d)/,'$1-$2');
    cepInput.value=v;
  });

  // ================= AUTOCOMPLETE CEP =================
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
        }
        else { 
          document.getElementById('endereco').value=data.logradouro; 
          document.getElementById('bairro').value=data.bairro; 
        }
      }).catch(err=>console.error(err));
  });

  // ================= ENVIO FORM =================
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
        } else if(data==='duplicado'){
          popupError.style.display='flex';
          setTimeout(()=>popupError.style.display='none',3000);
        } else { 
          console.error('Erro inesperado: '+data); 
        }
      }).catch(err=>console.error('Erro no fetch:',err));
  });

  // Fechar popups clicando fora
  [popupSuccess,popupError].forEach(popup=>{
    popup.addEventListener('click', e=>{
      if(e.target===popup) popup.style.display='none';
    });
  });
});
</script>

<?php include "footer.php"; ?>
</body>
</html>
