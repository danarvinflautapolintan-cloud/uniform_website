<?= $this->extend('layout') ?><?= $this->section('c') ?>
<h2>Student listings</h2>
<div class="tabs"><?php foreach (['' => 'All', 'pending' => 'Pending', 'active' => 'Active', 'reserved' => 'Reserved', 'sold' => 'Sold', 'rejected' => 'Rejected'] as $k => $v): ?>
<a class="tab<?= $st === $k ? ' on' : '' ?>" href="<?= site_url('admin/listings') . ($k ? '?status=' . $k : '') ?>"><?= $v ?></a><?php endforeach ?></div>
<?php if (!$rows): ?><p class="empty">No listings.</p><?php else: ?>
<div class="tablewrap"><table><tr><th>Photo</th><th>Name</th><th>Seller</th><th>Size</th><th>Condition</th><th>Price</th><th>Status</th><th></th></tr>
<?php foreach ($rows as $l): $s = $l['status']; ?><tr>
<td><img class="thumb" src="<?= base_url('uploads/' . esc($l['photo'], 'url')) ?>" alt=""></td><td><?= esc($l['name']) ?></td>
<td><?= esc($l['seller']) ?> (<?= esc($l['student_no']) ?>)</td><td><?= esc($l['size']) ?></td><td><?= esc($l['item_condition']) ?></td>
<td>₱<?= number_format((float) $l['price'], 2) ?></td><td><span class="badge b-<?= esc($s) ?>"><?= esc($s) ?></span></td><td>
<?php if (in_array($s, ['pending', 'rejected'], true)): ?><form method="post" action="<?= site_url('admin/listings/act') ?>" class="inline" data-confirm="Approve this listing?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $l['id'] ?>"><input type="hidden" name="action" value="approve"><button class="btn sm">Approve</button></form><?php endif ?>
<?php if (in_array($s, ['pending', 'active'], true)): ?><form method="post" action="<?= site_url('admin/listings/act') ?>" class="inline" data-confirm="Reject this listing?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $l['id'] ?>"><input type="hidden" name="action" value="reject"><button class="btn sm ghost">Reject</button></form><?php endif ?>
<form method="post" action="<?= site_url('admin/listings/delete') ?>" class="inline" data-confirm="Delete this listing permanently?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $l['id'] ?>"><button class="btn sm danger">Delete</button></form></td></tr>
<?php endforeach ?></table></div><?php endif ?>
<?= $this->endSection() ?>