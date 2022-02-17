<?php
$menu_active = $fun['menu_active'] ?? current_path();
$menu = array_filter(
  array_map(static fn (array $item) => ($item[2] ?? true) ? array_replace($item, array(2 => $item[0] === $menu_active)) : null, array(
    'Dashboard' => array('/dashboard', 'house'),
    'Users' => array('/user', 'people', has_role('admin')),
  )),
);
$account = array_filter(
  array_map(static fn (array $item) => ($item[1] ?? true) ? array_replace($item, array(1 => $item[0] === $menu_active)) : null, array(
    'Profile' => array('/profile'),
  )),
);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?= asset('css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/bootstrap-icons.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
    <?php foreach (styles() as $style): ?>
      <link href="<?= $style ?>" rel="stylesheet">
    <?php endforeach ?>
    <title><?= $fun['page_title'] ?? ($fun['title'] ?? 'Home') . ' - ' . $fun['app.name'] ?></title>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container-fluid">
        <a class="navbar-brand" href="#"><?= $fun['app.alias'] ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <!--
          <ul class="navbar-nav mb-2 mb-lg-0">
            <?php foreach ($menu as $label => list($path, $icon, $active)): ?>
              <li class="nav-item">
                <a class="nav-link<?= $active ? ' active' : null ?>" <?= $active ? 'aria-current="page"' : null ?> href="<?= path($path) ?>">
                  <i class="bi-<?= $icon ?>"></i> <?= $label ?>
                </a>
              </li>
            <?php endforeach ?>
          </ul>
            -->
          <?= nav(storage()->menu['main'], array('class' => 'mb-2 mb-lg-0')) ?>
          <?= nav(storage()->menu['profile'], array('class' => 'ms-auto mb-2 mb-lg-0'), array('end' => true)) ?>
          <!--
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Account
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <?php foreach ($account as $label => list($path, $active)): ?>
                  <li><a class="dropdown-item<?= $active ? ' active' : null ?>" <?= $active ? 'aria-current="page"' : null ?> href="<?= path($path) ?>"><?= $label ?></a></li>
                <?php endforeach ?>
                <li><a data-confirm="logout" class="dropdown-item" href="<?= path('logout') ?>">Logout</a></li>
              </ul>
            </li>
          </ul>
            -->
        </div>
      </div>
    </nav>
    <div class="container-fluid">
      <div class="row">
        <div class="col"><?= $output ?></div>
      </div>
    </div>

    <script src="<?= asset('js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= asset('js/sweetalert2.js') ?>"></script>
    <script src="<?= asset('js/script.js') ?>"></script>
    <?php foreach (scripts() as $script): ?>
      <script src="<?= $script ?>"></script>
    <?php endforeach ?>
    <?php foreach (modules() as $script): ?>
      <script src="<?= $script ?>" type="module"></script>
    <?php endforeach ?>
  </body>
</html>
