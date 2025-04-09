<header class="mb-auto">
  <div>
    <h3 class="float-md-start mb-0">CMS</h3>
    <nav class="nav nav-masthead d-flex justify-content-evenly float-md-center">
      <div class="d-flex flex-row">
        <a class="nav-link <?= ($page == 'index.php') ? 'active' : ''; ?>" aria-current="page" href="index.php">Home</a>
        <a class="nav-link  <?= ($page == 'farmaci.php' || $page == 'farmaco.php') ? 'active' : ''; ?>" href="farmaci.php">Farmaci</a>
        <a class="nav-link <?= ($page == 'principiattivi.php') ? 'active' : ''; ?>" href="principiattivi.php">Principi attivi</a>
        <a class="nav-link  <?= ($page == 'aziende.php') ? 'active' : ''; ?>" href="aziende.php">Aziende produttrici</a>
      </div>
      <form class="d-flex" role="search">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-warning" type="submit">Search</button>
      </form>
      <div class="d-flex flex-row">
        <a class="btn btn-outline-light me-2" href="login.php" style="<?php echo isset($_SESSION['session_id']) ? 'display:none;' : 'display:block;'; ?>">Login</a>
        <a class="btn btn-warning" href="signup.php" style="<?php echo isset($_SESSION['session_id']) ? 'display:none;' : 'display:block;'; ?>">Sign-up</a>
        <a class="btn btn-outline-light me-2" href="logout.php" style="<?php echo isset($_SESSION['session_id']) ? 'display:block;' : 'display:none;'; ?>">Logout</a>
      </div>
    </nav>
  </div>

</header>