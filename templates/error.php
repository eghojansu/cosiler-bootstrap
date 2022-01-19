<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?= asset('css/bootstrap.min.css') ?>" rel="stylesheet">
    <title><?= $code ?> - <?= $text ?></title>
  </head>
  <body class="vh-100">
    <div class="vh-100 vs-auto d-flex justify-content-center">
      <div class="w-75 p-5">
        <h1 class="fs-2 pb-3 mb-3 text-danger border-bottom">[<?= $code ?>] <?= $text ?></h1>
        <p><?= $message ?></p>
        <p><a href="<?= path('/') ?>" class="btn btn-primary">Home</a></p>
        <?php if ($dev): ?>
          <pre><?= implode("\n", $trace) ?></pre>
        <?php endif ?>
      </div>
    </div>
  </body>
</html>
