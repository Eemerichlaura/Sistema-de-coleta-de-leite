<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Formulário de Doação de Leite Materno</title>
  <link rel="stylesheet" href="/SistemaBL/style/style.css">
  <?php include "processamento/verifica-funcionario.php"; ?>
  <style>
    /* ================= POPUP ================= */
    .popup-overlay {
      position: fixed;
      top:0; left:0; right:0; bottom:0;
      background-color: rgba(0,0,0,0.5);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }
    .popup {
      background-color: #fff;
      padding: 2rem;
      border-radius: 10px;
      text-align: center;
      max-width: 400px;
      width: 90%;
      box-shadow: 0 0 15px rgba(0,0,0,0.3);
    }
    .popup h3 { margin-bottom: 1rem; }
    .popup p { font-size: 1rem; margin-bottom: 1rem; }
    .popup-success h3 { color: #28a745; }
    .popup-error h3 { color: #dc3545; }
    @media (max-width:500px){ .popup { padding:1.5rem; } }
  </style>
</head>
<body>
<?php include "header.php"; ?>

<h2>Cadastrar Bebê</h2>
<form id="formBebe" action="processamento/processa-receptor.php" method="post">
  <div class="form-grid">
    <!-- TODOS OS CAMPOS DO BEBÊ -->
    <div class="form-group">
      <label class="obrigatorio" for="nomebebe">Nome completo:</label>
      <input type="text" id="nomebebe" name="nomebebe" placeholder="Digite o nome do bebê" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="sexobebe">Sexo:</label>
      <select id="sexobebe" name="sexobebe" required>
        <option value="">Selecione o sexo do bebê</option>
        <option value="masculino">Masculino</option>
        <option value="feminino">Feminino</option>
      </select>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="cpfbebe">CPF:</label>
      <input type="text" id="cpfbebe" name="cpfbebe" placeholder="Apenas números" required>
      <small id="cpf-erro-bebe" style="color: red; display: none;">CPF inválido</small>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="data_nascimentobebe">Data de nascimento:</label>
      <input type="date" id="data_nascimentobebe" name="data_nascimentobebe" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="unidsaude">Unidade de Saúde:</label>
      <select id="unidsaude" name="unidsaude" required>
        <option value="">Selecione a unidade</option>
        <option value="UBS Central">UBS Central</option>
        <option value="UBS Leste">UBS Leste</option>
        <option value="UBS Oeste">UBS Oeste</option>
      </select>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="sitclinica">Situação Clínica:</label>
      <select id="sitclinica" name="sitclinica" required>
        <option value="">Selecione a situação</option>
        <option value="Prematuridade extrema">Prematuridade extrema</option>
        <option value="Baixo peso ao nascer">Baixo peso ao nascer</option>
        <option value="Dificuldade de sucção">Dificuldade de sucção</option>
        <option value="Impossibilidade temporária da mãe amamentar">Impossibilidade temporária da mãe amamentar</option>
        <option value="Doenças congênitas">Doenças congênitas</option>
        <option value="Reposição após vômitos ou diarreias severas">Reposição após vômitos ou diarreias severas</option>
        <option value="Internação em UTI neonatal">Internação em UTI neonatal</option>
        <option value="Bebê órfão ou abandonado">Bebê órfão ou abandonado</option>
      </select>
    </div>
  </div>

  <div class="form-group full-width">
    <label for="observacoesbebe">Observações médicas:</label>
    <textarea id="observacoesbebe" name="observacoesbebe" placeholder="Alergias, medicamentos, etc..." rows="4"></textarea>
  </div>

  <h2>Informações do Responsável</h2>
  <div class="form-grid">
    <!-- TODOS OS CAMPOS DO RESPONSÁVEL -->
    <div class="form-group">
      <label class="obrigatorio" for="nomerespbebe">Nome completo:</label>
      <input type="text" id="nomerespbebe" name="nomerespbebe" placeholder="Digite o nome do responsável" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="sexoresp">Sexo:</label>
      <select id="sexoresp" name="sexoresp" required>
        <option value="">Selecione o sexo do responsável</option>
        <option value="masculino">Masculino</option>
        <option value="feminino">Feminino</option>
        <option value="outro">Outro</option>
      </select>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="pronomesresp">Pronomes:</label>
      <select id="pronomesresp" name="pronomesresp" required>
        <option value="">Selecione os pronomes do responsável</option>
        <option value="ele-dele">Ele/Dele</option>
        <option value="ela-dela">Ela/Dela</option>
        <option value="outro">Outro</option>
      </select>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="cpfrespbebe">CPF do responsável:</label>
      <input type="text" id="cpfrespbebe" name="cpfrespbebe" placeholder="Apenas números" required>
      <small id="cpf-erro-resp" style="color: red; display: none;">CPF inválido</small>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="data_nascimentorespbebe">Data de nascimento:</label>
      <input type="date" id="data_nascimentorespbebe" name="data_nascimentorespbebe" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="telrespbebe">Telefone:</label>
      <input type="text" id="telrespbebe" name="telrespbebe" placeholder="Apenas números" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="ceprespbebe">CEP:</label>
      <input type="text" id="ceprespbebe" name="ceprespbebe" placeholder="Apenas números" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="endrespbebe">Endereço:</label>
      <input type="text" id="endrespbebe" name="endrespbebe" placeholder="Digite o endereço" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="bairrorespbebe">Bairro:</label>
      <input type="text" id="bairrorespbebe" name="bairrorespbebe" placeholder="Digite o bairro" required>
    </div>

    <div class="form-group">
      <label class="obrigatorio" for="numrespbebe">Número:</label>
      <input type="text" id="numrespbebe" name="numrespbebe" placeholder="Digite o número" required>
    </div>
  </div>

  <input type="submit" value="Enviar">
</form>

<!-- POPUPS -->
<div class="popup-overlay" id="popupSuccess">
  <div class="popup popup-success"><h3>Cadastro efetuado com sucesso!</h3></div>
</div>

<div class="popup-overlay" id="popupError">
  <div class="popup popup-error"><h3>Erro!</h3><p>CPF duplicado!</p></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

  const form = document.getElementById('formBebe');
  const popupSuccess = document.getElementById('popupSuccess');
  const popupError = document.getElementById('popupError');

  const cpfBebeInput = document.getElementById('cpfbebe');
  const cpfRespInput = document.getElementById('cpfrespbebe');
  const cepRespInput = document.getElementById('ceprespbebe');

  const cpfErroBebe = document.getElementById('cpf-erro-bebe');
  const cpfErroResp = document.getElementById('cpf-erro-resp');

  // ================= MÁSCARAS =================
  function mask(input, type){
    let v = input.value.replace(/\D/g,'');
    if(type === 'cpf'){
      v = v.replace(/(\d{3})(\d)/,'$1.$2');
      v = v.replace(/(\d{3})(\d)/,'$1.$2');
      v = v.replace(/(\d{3})(\d{1,2})$/,'$1-$2');
    } else if(type === 'tel'){
      v = v.replace(/^(\d{2})(\d)/g,'($1) $2');
      v = v.replace(/(\d{5})(\d{4})$/,'$1-$2');
    } else if(type === 'cep'){
      v = v.replace(/(\d{5})(\d)/,'$1-$2');
    }
    input.value = v;
  }

  cpfBebeInput.addEventListener('input', ()=> mask(cpfBebeInput,'cpf'));
  cpfRespInput.addEventListener('input', ()=> mask(cpfRespInput,'cpf'));
  cepRespInput.addEventListener('input', ()=> mask(cepRespInput,'cep'));
  document.getElementById('telrespbebe').addEventListener('input', e => mask(e.target,'tel'));

  // ================= VALIDAÇÃO DE CPF =================
  function validarCPF(cpf){
    cpf = cpf.replace(/\D/g,'');
    if(cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;
    let soma = 0, resto;

    for(let i=1;i<=9;i++) soma += parseInt(cpf.substring(i-1,i))*(11-i);
    resto = (soma*10)%11; if(resto===10 || resto===11) resto=0;
    if(resto!==parseInt(cpf.substring(9,10))) return false;

    soma=0;
    for(let i=1;i<=10;i++) soma+=parseInt(cpf.substring(i-1,i))*(12-i);
    resto=(soma*10)%11; if(resto===10||resto===11) resto=0;
    if(resto!==parseInt(cpf.substring(10,11))) return false;

    return true;
  }

  cpfBebeInput.addEventListener('blur', ()=> {
    if(!validarCPF(cpfBebeInput.value)) cpfErroBebe.style.display='inline';
    else cpfErroBebe.style.display='none';
  });

  cpfRespInput.addEventListener('blur', ()=> {
    if(!validarCPF(cpfRespInput.value)) cpfErroResp.style.display='inline';
    else cpfErroResp.style.display='none';
  });

  // ================= BUSCA CEP =================
  cepRespInput.addEventListener('blur', ()=>{
    let cep = cepRespInput.value.replace(/\D/g,'');
    if(cep.length===8){
      fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(res=>res.json())
        .then(data=>{
          if(!data.erro){
            document.getElementById('endrespbebe').value = data.logradouro;
            document.getElementById('bairrorespbebe').value = data.bairro;
          } else {
            alert('CEP não encontrado!');
          }
        })
        .catch(err=> console.error('Erro ao buscar CEP: ', err));
    }
  });

  // ================= ENVIO COM AJAX =================
  form.addEventListener('submit', function(e){
    e.preventDefault();

    // Valida CPFs antes do envio
    if(!validarCPF(cpfBebeInput.value)){
      cpfErroBebe.style.display='inline'; cpfBebeInput.focus(); return;
    }
    if(!validarCPF(cpfRespInput.value)){
      cpfErroResp.style.display='inline'; cpfRespInput.focus(); return;
    }

    const formData = new FormData(form);
    fetch(form.action,{ method:'POST', body: formData })
      .then(res=>res.text())
      .then(data=>{
        if(data==='ok'){
          popupSuccess.style.display='flex';
          setTimeout(()=>{ popupSuccess.style.display='none'; window.location.href='visualizar-receptores.php'; },2000);
        } else if(data==='duplicado'){
          popupError.style.display='flex';
          setTimeout(()=> popupError.style.display='none',3000);
        } else {
          console.error('Erro inesperado: '+data);
        }
      })
      .catch(err=> console.error('Erro: '+err));
  });

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
