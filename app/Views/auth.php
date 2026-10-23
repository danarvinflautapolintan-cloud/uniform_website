<?= $this->extend('layout') ?><?= $this->section('c') ?>
<?php $admin = $admin ?? false; ?>
<div class="panel narrow"><h2><?= $admin ? '🛠 ' : '' ?><?= esc($title) ?></h2>
<form method="post" action="<?= $action ?>"><?= csrf_field() ?>
<?php if ($reg): ?>
<label>Student no. (11 digits, 20220000000 or higher)<input type="text" name="student_no" required minlength="11" maxlength="11" pattern="[0-9]{11}" inputmode="numeric" title="Enter exactly 11 digits, starting at 20220000000 or higher." oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)" value="<?= esc(old('student_no')) ?>" autofocus></label>
<?php else: ?>
<label><?= $admin ? 'Username' : 'Student no.' ?><input type="text" name="student_no" required maxlength="20" value="<?= esc(old('student_no')) ?>" autofocus></label>
<?php endif ?>
<?php if ($reg): ?><label>Full name<input name="name" required maxlength="100" value="<?= esc(old('name')) ?>"></label><?php endif ?>
<label>Password<input type="password" name="password" required <?= $reg ? 'minlength="8" autocomplete="new-password"' : 'autocomplete="current-password"' ?>></label>
<p><button class="btn"><?= esc($title) ?></button></p></form></div>
<?= $this->endSection() ?>