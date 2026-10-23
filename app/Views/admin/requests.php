<?= $this->extend('layout') ?><?= $this->section('c') ?>
<div class="top"><h2>Requests</h2>
<form method="post" action="<?= site_url('admin/requests/clear') ?>" class="inline" data-confirm="Delete ALL claimed and rejected requests? Pending and approved will stay."><?= csrf_field() ?><button class="btn sm ghost">🧹 Clear finished</button></form></div>
<div class="tabs"><?php foreach (['' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'claimed' => 'Claimed', 'rejected' => 'Rejected'] as $k => $v): ?>
<a class="tab<?= $st === $k ? ' on' : '' ?>" href="<?= site_url('admin/requests') . ($k ? '?status=' . $k : '') ?>"><?= $v ?></a><?php endforeach ?></div>
<?php if (!$rows): ?><p class="empty">No requests.</p><?php else: ?>
<div class="tablewrap"><table><tr><th>Student</th><th>Uniform</th><th>Size</th><th>Price</th><th>Status</th><th>Date</th><th></th></tr>
<?php foreach ($rows as $r): ?><tr><td><?= esc($r['student']) ?> (<?= esc($r['student_no']) ?>)</td><td><?= esc($r['uniform']) ?></td><td><?= esc($r['size']) ?></td>
<td><?= (float) $r['price'] > 0 ? '₱' . number_format((float) $r['price'], 2) : 'Free' ?></td>
<td><span class="badge b-<?= esc($r['status']) ?>"><?= esc($r['status']) ?></span></td><td><?= esc($r['created_at']) ?></td><td>
<?php $acts = $r['status'] === 'pending' ? ['approve', 'reject'] : ($r['status'] === 'approved' ? ['claim'] : []);
foreach ($acts as $a): ?><form method="post" action="<?= site_url('admin/requests/act') ?>" class="inline" data-confirm="<?= esc(ucfirst($a)) ?> this request?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $r['id'] ?>"><input type="hidden" name="action" value="<?= $a ?>"><button class="btn sm<?= $a === 'reject' ? ' ghost' : '' ?>"><?= esc(ucfirst($a)) ?></button></form> <?php endforeach ?>
<form method="post" action="<?= site_url('admin/requests/delete') ?>" class="inline" data-confirm="<?= $r['status'] === 'approved' ? 'Delete this request? The stock will be returned.' : 'Delete this request?' ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $r['id'] ?>"><button class="btn sm danger">Delete</button></form></td></tr>
<?php endforeach ?></table></div><?php endif ?>
<?= $this->endSection() ?>