<?= $this->extend('layout') ?><?= $this->section('c') ?>
<div class="top"><h2>Registered students</h2><span class="mu"><?= count($rows) ?> shown</span></div>
<form method="get" action="<?= site_url('admin/users') ?>" class="search"><input name="q" placeholder="Search username or name..." maxlength="50" value="<?= esc($q) ?>"><button class="btn">Search</button></form>
<?php if (!$rows): ?><p class="empty">No students found.</p><?php else: ?>
<div class="tablewrap"><table><tr><th>Name</th><th>Student no.</th><th>Requests</th><th>Registered</th><th>Actions</th></tr>
<?php foreach ($rows as $r): ?><tr>
<td><span class="avatar"><?= esc(mb_strtoupper(mb_substr($r['name'], 0, 1))) ?></span><?= esc($r['name']) ?></td>
<td><?= esc($r['student_no']) ?></td><td><?= (int) $r['reqs'] ?></td><td><?= esc($r['created_at']) ?></td>
<td><form method="post" action="<?= site_url('admin/users/delete') ?>" class="inline" onsubmit="return confirm('Remove this student? Their uniform requests, listings, and related marketplace requests will also be deleted.');"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $r['id'] ?>"><input type="hidden" name="q" value="<?= esc($q) ?>"><button class="btn danger sm">Remove</button></form></td></tr>
<?php endforeach ?></table></div><?php endif ?>
<?= $this->endSection() ?>