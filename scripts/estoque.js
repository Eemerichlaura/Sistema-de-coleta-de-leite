document.addEventListener("DOMContentLoaded", () => {
  // =============================
  // 📦 Lógica dos botões + formulários
  // =============================
  const botoesAdd = document.querySelectorAll(".btn-add");
  const botoesRemove = document.querySelectorAll(".btn-remove");

  function mostrarFormulario(botao, acao) {
    const tipo = botao.dataset.tipo;
    const form = document.querySelector(`.form-${tipo}`);

    // Oculta qualquer outro formulário aberto
    document.querySelectorAll(".form-estoque").forEach(f => f.style.display = "none");

    // Define a ação correta e mostra o formulário
    form.querySelector("input[name='acao']").value = acao;
    form.style.display = "block";
  }

  botoesAdd.forEach(botao => {
    botao.addEventListener("click", () => mostrarFormulario(botao, "entrada"));
  });

  botoesRemove.forEach(botao => {
    botao.addEventListener("click", () => mostrarFormulario(botao, "saida"));
  });

  // =============================
  // 💬 Lógica do Popup de mensagem
  // =============================
  const params = new URLSearchParams(window.location.search);
  const msg = params.get("msg");
  const popup = document.getElementById("popup");
  const popupMsg = document.getElementById("popup-msg");
  const popupClose = document.getElementById("popup-close");

  if (msg === "atualizado") {
    popupMsg.textContent = "Estoque atualizado com sucesso!";
    popup.style.display = "flex";
  } else if (msg === "erro") {
    popupMsg.textContent = "Erro ao atualizar o estoque.";
    popup.style.display = "flex";
  }

  // Fechar ao clicar no "X"
  popupClose.addEventListener("click", () => {
    popup.style.display = "none";
    removerParametroMsg();
  });

  // ✅ Fechar ao clicar fora do conteúdo do popup
  popup.addEventListener("click", (e) => {
    if (e.target === popup) {
      popup.style.display = "none";
      removerParametroMsg();
    }
  });

  // Função auxiliar para remover o parâmetro da URL
  function removerParametroMsg() {
    const url = new URL(window.location);
    url.searchParams.delete("msg");
    window.history.replaceState({}, document.title, url);
  }
});
