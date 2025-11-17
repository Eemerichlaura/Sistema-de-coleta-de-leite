<link rel="stylesheet" href="/SistemaBL/style/headeradm.css">
<link rel="stylesheet" href="/SistemaBL/style/style.css">

<header>
  <div class="logo-container">
    <a href="home.php">
      <img src="/SistemaBL/img/logo.png" alt="Logo" class="logo">
    </a>
  </div>

  <nav>
    <ul class="nav-list">
      <li><a href="cadastro-doadora.php">Cadastrar Doadora</a></li>
      <li><a href="cadastro-receptor.php">Cadastrar Bebê</a></li>
      <li><a href="cadastro-doacao.php">Cadastrar Doação</a></li>
      <li><a href="cadastro-retirada.php">Cadastrar Retirada</a></li>
      <li><a href="../cadastro-funcionarios.php">Cadastrar Funcionários</a></li>
      <li><a href="processamento/logout.php">Sair</a></li>
    </ul>

    <div class="hamburger" id="hamburger">&#9776;</div>

    <!-- Overlay para fundo escuro -->
    <div class="overlay" id="overlay"></div>

    <div class="mobile-menu" id="mobileMenu">
      <div class="mobile-menu-header">
        <a href="home.php" class="home-icon" title="Home">&#8962;</a>
        <button class="close-btn" id="closeMenu" aria-label="Fechar menu">&times;</button>
      </div>

      <a href="cadastro-doadora.php">Cadastrar Doadora</a>
      <a href="cadastro-receptor.php">Cadastrar Bebê</a>
      <a href="cadastro-doacao.php">Cadastrar Doação</a>
      <a href="cadastro-retirada.php">Cadastrar Retirada</a>
      <a href="index.php">Sair</a>
    </div>
  </nav>
</header>

<script>
  const hamburger = document.getElementById('hamburger');
  const mobileMenu = document.getElementById('mobileMenu');
  const closeBtn = document.getElementById('closeMenu');
  const overlay = document.getElementById('overlay');

  function openMenu() {
    mobileMenu.classList.add('open');
    overlay.classList.add('active');
    // Opcional: trava scroll do body ao abrir menu
    document.body.style.overflow = 'hidden';
  }

  function closeMenu() {
    mobileMenu.classList.remove('open');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
  }

  hamburger.addEventListener('click', openMenu);
  closeBtn.addEventListener('click', closeMenu);
  overlay.addEventListener('click', closeMenu);
</script>
