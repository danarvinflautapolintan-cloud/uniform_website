<?= $this->extend('layout') ?><?= $this->section('c') ?>
<h2>My requests</h2>
<?php if (!$rows): ?><p class="empty">No requests yet. <a href="<?= site_url('/') ?>">Browse uniforms</a></p><?php else: ?>
<div class="tablewrap"><table><tr><th>Uniform</th><th>Size</th><th>Price</th><th>Status</th><th>Date</th><th></th></tr>
<?php foreach ($rows as $r): ?><tr><td><?= esc($r['name']) ?></td><td><?= esc($r['size']) ?></td>
<td><?= (float) $r['price'] > 0 ? '₱' . number_format((float) $r['price'], 2) : 'Free' ?></td>
<td><span class="badge b-<?= esc($r['status']) ?>"><?= esc($r['status']) ?></span></td><td><?= esc($r['created_at']) ?></td>
<td><?php if ($r['status'] === 'pending'): ?><form method="post" action="<?= site_url('cancel') ?>" class="inline" data-confirm="Cancel this request?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $r['id'] ?>"><button class="btn sm ghost">Cancel</button></form><?php endif ?></td></tr>
<?php endforeach ?></table></div>
<p class="mu gap">Pending = waiting for admin. Approved = ready to claim. Claimed = done.</p><?php endif ?>
<?= $this->endSection() ?>