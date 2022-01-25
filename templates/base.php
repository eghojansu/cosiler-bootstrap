<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?= asset('css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/bootstrap-icons.css') ?>" rel="stylesheet">
    <title><?= $fun['page_title'] ?? ($fun['title'] ?? 'Home') . ' - ' . $fun['app.name'] ?></title>
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col"><?= $output ?></div>
      </div>
    </div>
  </body>
</html>
