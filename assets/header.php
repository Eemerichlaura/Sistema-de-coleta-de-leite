<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<link rel="stylesheet" href="/SistemaBL/style/header.css">

<header>
  <div class="logo-container">
    <a href="home.php">
      <img src="/SistemaBL/img/logo.png" alt="Logo" class="logo">
    </a>
  </div>

 <nav>
  <ul class="nav-list">
    <li class="dropdown">
      <button class="dropdown-btn">Doadoras ▾</button>
      <ul class="dropdown-content">
        <li><a href="cadastro-doadora.php">Cadastrar Doadora</a></li>
        <li><a href="visualizar-doadoras.php">Visualizar Doadoras</a></li>
      </ul>
    </li>

    <li class="dropdown">
      <button class="dropdown-btn">Bebês ▾</button>
      <ul class="dropdown-content">
        <li><a href="cadastro-receptor.php">Cadastrar Bebê</a></li>
        <li><a href="visualizar-receptores.php">Visualizar Bebês</a></li>
      </ul>
    </li>

    <li class="dropdown">
      <button class="dropdown-btn">Doações ▾</button>
      <ul class="dropdown-content">
        <li><a href="cadastro-doacao.php">Cadastrar Doação</a></li>
        <li><a href="visualizar-doacoes.php">Visualizar Doações</a></li>
      </ul>
    </li>

    <li class="dropdown">
      <button class="dropdown-btn">Retiradas ▾</button>
      <ul class="dropdown-content">
        <li><a href="cadastro-retirada.php">Cadastrar Retirada</a></li>
        <li><a href="visualizar-retiradas.php">Visualizar Retiradas</a></li>
      </ul>
    </li>

    <?php if (isset($_SESSION['nivel']) && $_SESSION['nivel'] === 'admin'): ?>
      <li class="dropdown">
        <button class="dropdown-btn">Funcionários ▾</button>
        <ul class="dropdown-content">
          <li><a href="cadastro-funcionarios.php">Cadastrar Funcionário</a></li>
          <li><a href="visualizar-funcionarios.php">Visualizar Funcionários</a></li>
        </ul>
      </li>
    <?php endif; ?>

    <li><a href="processamento/logout.php">Sair</a></li>
  </ul>

  <!-- Ícone do menu hambúrguer -->
  <div class="hamburger" id="hamburger">&#9776;</div>
  <div class="overlay" id="overlay"></div>

  <!-- Menu mobile -->
  <div class="mobile-menu" id="mobileMenu">
    <div class="mobile-menu-header">
      <a href="home.php" class="home-icon" title="Home">&#8962;</a>
      <button class="close-btn" id="closeMenu" aria-label="Fechar menu">&times;</button>
    </div>

    <details>
      <summary>Doadoras</summary>
      <a href="cadastro-doadora.php">Cadastrar Doadora</a>
      <a href="visualizar-doadoras.php">Visualizar Doadoras</a>
    </details>

    <details>
      <summary>Bebês</summary>
      <a href="cadastro-receptor.php">Cadastrar Bebê</a>
      <a href="visualizar-receptores.php">Visualizar Bebês</a>
    </details>

    <details>
      <summary>Doações</summary>
      <a href="cadastro-doacao.php">Cadastrar Doação</a>
      <a href="visualizar-doacoes.php">Visualizar Doações</a>
    </details>

    <details>
      <summary>Retiradas</summary>
      <a href="cadastro-retirada.php">Cadastrar Retirada</a>
      <a href="visualizar-retiradas.php">Visualizar Retiradas</a>
    </details>

    <?php if (isset($_SESSION['nivel']) && $_SESSION['nivel'] === 'admin'): ?>
      <details>
        <summary>Funcionários</summary>
        <a href="cadastro-funcionarios.php">Cadastrar Funcionário</a>
        <a href="visualizar-funcionarios.php">Visualizar Funcionários</a>
      </details>
    <?php endif; ?>

    <a href="processamento/logout.php">Sair</a>
  </div>
</nav>

</header>

<script>
  // --- MENU MOBILE ---
  const hamburger = document.getElementById('hamburger');
  const mobileMenu = document.getElementById('mobileMenu');
  const closeBtn = document.getElementById('closeMenu');
  const overlay = document.getElementById('overlay');

  function openMenu() {
    mobileMenu.classList.add('open');
    overlay.classList.add('active');
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

  // --- DROPDOWN CLICÁVEL ---
  document.querySelectorAll('.dropdown-btn').forEach(button => {
    button.addEventListener('click', () => {
      const dropdown = button.nextElementSibling;
      dropdown.classList.toggle('show');

      // Fecha outros dropdowns abertos
      document.querySelectorAll('.dropdown-content').forEach(other => {
        if (other !== dropdown) other.classList.remove('show');
      });
    });
  });

  // Fecha dropdowns ao clicar fora
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.dropdown')) {
      document.querySelectorAll('.dropdown-content').forEach(drop => drop.classList.remove('show'));
    }
  });
</script>
