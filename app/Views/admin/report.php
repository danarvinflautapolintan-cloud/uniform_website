<?= $this->extend('layout') ?><?= $this->section('c') ?>
<div class="top"><h2>Uniform Exchange Report</h2><button class="btn noprint" data-print>Print</button></div>
<p class="mu">Printed <?= esc(date('Y-m-d H:i')) ?></p>
<h3>Stock</h3>
<div class="tablewrap"><table><tr><th>Uniform</th><th>Price</th><?php foreach ($sizes as $s) echo '<th>' . esc($s) . '</th>'; ?></tr>
<?php foreach ($items as $i): ?><tr><td><?= esc($i['name']) ?></td><td>₱<?= number_format((float) $i['price'], 2) ?></td><?php foreach ($sizes as $s) echo '<td>' . (int) ($stock[$i['id']][$s] ?? 0) . '</td>'; ?></tr><?php endforeach ?></table></div>
<h3 class="gap">Requests</h3>
<?php if (!$rows): ?><p class="empty">No requests.</p><?php else: ?>
<div class="tablewrap"><table><tr><th>Student</th><th>Uniform</th><th>Size</th><th>Status</th><th>Date</th><th class="noprint"></th></tr>
<?php foreach ($rows as $r): ?><tr><td><?= esc($r['student']) ?></td><td><?= esc($r['uniform']) ?></td><td><?= esc($r['size']) ?></td><td><?= esc($r['status']) ?></td><td><?= esc($r['created_at']) ?></td>
<td class="noprint"><form method="post" action="<?= site_url('admin/requests/delete') ?>" class="inline" data-confirm="<?= $r['status'] === 'approved' ? 'Delete this request? The stock will be returned.' : 'Delete this request?' ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $r['id'] ?>"><input type="hidden" name="back" value="report"><button class="btn sm danger">Delete</button></form></td></tr>
<?php endforeach ?></table></div><?php endif ?>
<?= $this->endSection() ?>