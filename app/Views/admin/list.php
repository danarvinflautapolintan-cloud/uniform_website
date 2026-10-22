<?= $this->extend('layout') ?><?= $this->section('c') ?>
<div class="stats">
<div class="stat"><b><?= (int) $stats['uniforms'] ?></b>Uniforms</div>
<div class="stat"><b><?= (int) $stats['stock'] ?></b>Total stock</div>
<div class="stat"><b><?= (int) $stats['students'] ?></b>Students</div>
<div class="stat"><b><?= (int) $stats['pending'] ?></b>Pending requests</div>
<div class="stat"><b><?= (int) $stats['claimed'] ?></b>Claimed</div></div>
<div class="top"><h2>Manage uniforms</h2><a class="btn" href="<?= site_url('admin/uniforms/new') ?>">+ Add uniform</a></div>
<?php if (!$items): ?><p class="empty">No uniforms yet.</p><?php else: ?>
<div class="tablewrap"><table><tr><th>Photo</th><th>Name</th><th>Price</th><?php foreach ($sizes as $s) echo '<th>' . esc($s) . '</th>'; ?><th></th></tr>
<?php foreach ($items as $i): ?><tr><td><img class="thumb" src="<?= base_url('uploads/' . esc($i['photo'], 'url')) ?>" alt=""></td><td><?= esc($i['name']) ?></td>
<td>₱<?= number_format((float) $i['price'], 2) ?></td>
<?php foreach ($sizes as $s): $n = (int) ($stock[$i['id']][$s] ?? 0); ?><td class="<?= $n === 0 ? 'zero' : ($n <= 2 ? 'low' : '') ?>"><?= $n ?></td><?php endforeach ?>
<td><a class="btn sm ghost" href="<?= site_url('admin/uniforms/' . (int) $i['id'] . '/edit') ?>">Edit</a>
<form method="post" action="<?= site_url('admin/uniforms/delete') ?>" class="inline" data-confirm="Delete this uniform?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $i['id'] ?>"><button class="btn sm ghost">Delete</button></form></td></tr>
<?php endforeach ?></table></div><p class="mu">Orange = low stock (2 or less), red = out of stock.</p><?php endif ?>
<?= $this->endSection() ?>