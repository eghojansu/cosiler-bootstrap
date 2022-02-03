<?php

guard('admin');

$page = paginate('user', array('userid <> ?', user_id()));
$message = message();
?>
<h4 class="border-bottom p-3">User</h4>

<div class="row mb-3">
  <div class="col d-flex justify-content-end">
    <div class="btn-group">
      <a href="<?= path('/user/create') ?>" class="btn btn-primary">
        <i class="bi-plus-circle"></i> New
      </a>
    </div>
  </div>
</div>

<?= alert($message ?? null, 'info') ?>

<table class="table table-bordered">
  <thead>
    <tr>
      <th class="w50">#</th>
      <th>Name</th>
      <th>Role</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($page['subset'] as $pos => $item): ?>
      <tr>
        <td><?= $page['first'] + $pos ?></td>
        <td><?= $item['name'] ?></td>
        <td><?= $item['roles'] ?></td>
        <td>
          <div class="btn-group btn-group-sm">
            <a href="<?= path('/user/edit/' . $item['userid']) ?>" class="btn btn-success">
              <i class="bi-pencil"></i>
            </a>
            <a data-confirm="delete" href="<?= path('/user/delete/' . $item['userid']) ?>" class="btn btn-danger">
              <i class="bi-trash"></i>
            </a>
          </div>
        </td>
      </tr>
    <?php endforeach ?>
    <?php if ($page['empty']): ?>
      <tr><td colspan="4">No data</td></tr>
    <?php endif ?>
  </tbody>
</table>

<?= pagination_footer($page) ?>
