<?php

not_found_if(is_installed($location));
layout('base');

$writable = is_writable(dirname($location));
$fun['title'] = 'SETUP';
?>
<div class="mx-auto my-5 text-center" style="max-width: 280px">
  <form method="post" autocomplete="off">
    <h1 class="h3 fw-normal">Run application setup</h1>
    <p class="py-3">Current environment: <?= $fun['env'] ?? 'prod' ?></p>
    <button <?= $writable ? null : 'disabled' ?> class="w-100 btn btn-lg btn-primary mt-3" type="submit">RUN SETUP NOW</button>
  </form>
</div>
