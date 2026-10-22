<?= $this->extend('layout') ?><?= $this->section('c') ?>
<?php $admin = $admin ?? false; ?>
<div class="panel narrow"><h2><?= $admin ? '🛠 ' : '' ?><?= esc($title) ?></h2>
<form method="post" action="<?= $action ?>"><?= csrf_field() ?>
<label><?= $admin ? 'Username' : 'Student no.' ?><input name="student_no" required maxlength="20" value="<?= esc(old('student_no')) ?>" autofocus></label>
<?php if ($reg): ?><label>Full name<input name="name" required maxlength="100" value="<?= esc(old('name')) ?>"></label><?php endif ?>
<label>Password<input type="password" name="password" required <?= $reg ? 'minlength="8" autocomplete="new-password"' : 'autocomplete="current-password"' ?>></label>
<p><button class="btn"><?= esc($title) ?></button></p></form></div>
<?= $this->endSection() ?>