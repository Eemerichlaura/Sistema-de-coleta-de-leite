document.addEventListener("DOMContentLoaded", function () {
  // === CPF - BEBÊ ===
  const cpfBebeInput = document.getElementById("cpfbebe");
  const cpfErroBebe = document.getElementById("cpf-erro-bebe");

  cpfBebeInput.addEventListener("blur", function () {
    const cpfLimpo = cpfBebeInput.value.replace(/\D/g, "");
    if (!validarCPF(cpfLimpo)) {
      cpfErroBebe.style.display = "block";
      cpfBebeInput.classList.add("erro");
    } else {
      cpfErroBebe.style.display = "none";
      cpfBebeInput.classList.remove("erro");
    }
  });

  // === CPF - RESPONSÁVEL ===
  const cpfRespInput = document.getElementById("cpfrespbebe");
  const cpfErroResp = document.getElementById("cpf-erro-resp");

  cpfRespInput.addEventListener("blur", function () {
    const cpfLimpo = cpfRespInput.value.replace(/\D/g, "");
    if (!validarCPF(cpfLimpo)) {
      cpfErroResp.style.display = "block";
      cpfRespInput.classList.add("erro");
    } else {
      cpfErroResp.style.display = "none";
      cpfRespInput.classList.remove("erro");
    }
  });

  // === CEP - RESPONSÁVEL ===
  const cepInput = document.getElementById("ceprespbebe");
  const enderecoInput = document.getElementById("endrespbebe");
  const bairroInput = document.getElementById("bairrorespbebe");
  const cepErro = document.getElementById("cep-erro");

  cepInput.addEventListener("blur", function () {
    const cep = cepInput.value.replace(/\D/g, "");
    if (cep.length !== 8) {
      mostrarErroCEP("CEP inválido");
      return;
    }

    fetch(`https://viacep.com.br/ws/${cep}/json/`)
      .then((res) => res.json())
      .then((data) => {
        if (data.erro) {
          mostrarErroCEP("CEP não encontrado");
        } else {
          enderecoInput.value = data.logradouro;
          bairroInput.value = data.bairro;
          cepErro.style.display = "none";
          cepInput.classList.remove("erro");
        }
      })
      .catch(() => {
        mostrarErroCEP("Erro ao buscar CEP");
      });
  });

  function mostrarErroCEP(msg) {
    cepErro.textContent = msg;
    cepErro.style.display = "block";
    cepInput.classList.add("erro");
    enderecoInput.value = "";
    bairroInput.value = "";
  }

  // === Validação CPF ===
  function validarCPF(cpf) {
    if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;
    let soma = 0;
    for (let i = 0; i < 9; i++) soma += parseInt(cpf.charAt(i)) * (10 - i);
    let resto = 11 - (soma % 11);
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.charAt(9))) return false;
    soma = 0;
    for (let i = 0; i < 10; i++) soma += parseInt(cpf.charAt(i)) * (11 - i);
    resto = 11 - (soma % 11);
    if (resto === 10 || resto === 11) resto = 0;
    return resto === parseInt(cpf.charAt(10));
  }

  // === MÁSCARAS ===
  function aplicarMascara(input, pattern) {
    input.addEventListener("input", () => {
      let value = input.value.replace(/\D/g, "");
      let newValue = "";
      let j = 0;
      for (let i = 0; i < pattern.length && j < value.length; i++) {
        if (pattern[i] === "#") {
          newValue += value[j++];
        } else {
          newValue += pattern[i];
        }
      }
      input.value = newValue;
    });
  }

  aplicarMascara(cpfBebeInput, "###.###.###-##");
  aplicarMascara(cpfRespInput, "###.###.###-##");
  aplicarMascara(cepInput, "#####-###");

  const telRespInput = document.getElementById("telrespbebe");
  if (telRespInput) {
    aplicarMascara(telRespInput, "(##) #####-####");
  }
});
