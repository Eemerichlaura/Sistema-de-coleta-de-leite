document.addEventListener("DOMContentLoaded", function () {
  // Elementos do formulário
  const cpfInput = document.getElementById("cpf");
  const telefoneInput = document.getElementById("telefone");
  const cepInput = document.getElementById("cep");
  const enderecoInput = document.getElementById("endereco");
  const bairroInput = document.getElementById("bairro");

  // Mensagens de erro
  const cepErro = document.getElementById("cep-erro");
  const cpfErro = document.getElementById("cpf-erro");

  // ----------------------------- //
  // Máscara e Validação do CPF   //
  // ----------------------------- //
  cpfInput.addEventListener("input", function () {
    let cpf = this.value.replace(/\D/g, "");
    cpf = cpf.slice(0, 11);
    cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2");
    cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2");
    cpf = cpf.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
    this.value = cpf;

    // Oculta erro ao digitar novamente
    cpfInput.classList.remove("erro");
    cpfErro.style.display = "none";
  });

  cpfInput.addEventListener("blur", function () {
    const cpfLimpo = cpfInput.value.replace(/\D/g, "");
    if (!validarCPF(cpfLimpo)) {
      cpfErro.style.display = "block";
      cpfInput.classList.add("erro");
      cpfInput.focus();
    } else {
      cpfErro.style.display = "none";
      cpfInput.classList.remove("erro");
    }
  });

  function validarCPF(cpf) {
    if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;

    let soma = 0;
    for (let i = 0; i < 9; i++) {
      soma += parseInt(cpf.charAt(i)) * (10 - i);
    }
    let resto = soma % 11;
    let digito1 = resto < 2 ? 0 : 11 - resto;

    if (parseInt(cpf.charAt(9)) !== digito1) return false;

    soma = 0;
    for (let i = 0; i < 10; i++) {
      soma += parseInt(cpf.charAt(i)) * (11 - i);
    }
    resto = soma % 11;
    let digito2 = resto < 2 ? 0 : 11 - resto;

    return parseInt(cpf.charAt(10)) === digito2;
  }

  // ----------------------------- //
  // Máscara do Telefone          //
  // ----------------------------- //
  telefoneInput.addEventListener("input", function () {
    let tel = this.value.replace(/\D/g, "");
    tel = tel.slice(0, 11);
    tel = tel.replace(/^(\d{2})(\d)/g, "($1) $2");
    tel = tel.replace(/(\d{5})(\d{4})$/, "$1-$2");
    this.value = tel;
  });

  // ----------------------------- //
  // Máscara e Validação do CEP   //
  // ----------------------------- //
  cepInput.addEventListener("input", function () {
    let cep = this.value.replace(/\D/g, "");
    cep = cep.slice(0, 8);
    cep = cep.replace(/(\d{5})(\d)/, "$1-$2");
    this.value = cep;

    // Oculta erro ao digitar novamente
    cepInput.classList.remove("erro");
    cepErro.style.display = "none";
  });

  cepInput.addEventListener("blur", function () {
    const cepSemMascara = cepInput.value.replace(/\D/g, "");
    if (cepSemMascara.length === 8) {
      fetch(`https://viacep.com.br/ws/${cepSemMascara}/json/`)
        .then((response) => response.json())
        .then((data) => {
          if (!data.erro) {
            enderecoInput.value = data.logradouro;
            bairroInput.value = data.bairro;
            cepInput.classList.remove("erro");
            cepErro.style.display = "none";
          } else {
            mostrarErroCep();
          }
        })
        .catch(() => {
          mostrarErroCep();
        });
    } else if (cepSemMascara.length > 0) {
      mostrarErroCep();
    }
  });

  function mostrarErroCep() {
    cepErro.style.display = "block";
    cepInput.classList.add("erro");
    cepInput.focus();
  }
});
