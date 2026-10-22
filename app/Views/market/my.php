<?= $this->extend('layout') ?><?= $this->section('c') ?>
<div class="top"><h2>My listings</h2><a class="btn" href="<?= site_url('market/sell') ?>">+ Sell a uniform</a></div>
<?php if (!$mine): ?><p class="empty">You have no listings yet.</p><?php else: ?>
<div class="tablewrap"><table><tr><th>Photo</th><th>Name</th><th>Size</th><th>Price</th><th>Status</th><th></th></tr>
<?php foreach ($mine as $l): $s = $l['status']; ?><tr>
<td><img class="thumb" src="<?= base_url('uploads/' . esc($l['photo'], 'url')) ?>" alt=""></td><td><?= esc($l['name']) ?></td><td><?= esc($l['size']) ?></td>
<td><?= (float) $l['price'] > 0 ? '₱' . number_format((float) $l['price'], 2) : 'Free' ?></td>
<td><span class="badge b-<?= esc($s) ?>"><?= $s === 'pending' ? 'waiting for admin' : esc($s) ?></span></td><td>
<?php if (!in_array($s, ['reserved', 'sold'], true)): ?><a class="btn sm ghost" href="<?= site_url('market/sell?id=' . (int) $l['id']) ?>">Edit</a><?php endif ?>
<?php if ($s === 'reserved'): ?>
<form method="post" action="<?= site_url('market/sold') ?>" class="inline" data-confirm="Mark as sold? Do this after the meet-up."><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $l['id'] ?>"><button class="btn sm">Mark sold</button></form>
<form method="post" action="<?= site_url('market/release') ?>" class="inline" data-confirm="Release this listing? It will be available again."><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $l['id'] ?>"><button class="btn sm ghost">Release</button></form>
<?php else: ?>
<form method="post" action="<?= site_url('market/delete') ?>" class="inline" data-confirm="Delete this listing?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $l['id'] ?>"><button class="btn sm danger">Delete</button></form>
<?php endif ?></td></tr><?php endforeach ?></table></div><?php endif ?>

<h2 class="sec">Requests to buy my items</h2>
<?php if (!$in): ?><p class="empty">No requests yet.</p><?php else: ?>
<div class="tablewrap"><table><tr><th>Listing</th><th>Size</th><th>Buyer</th><th>Status</th><th>Date</th><th></th></tr>
<?php foreach ($in as $r): ?><tr><td><?= esc($r['listing']) ?></td><td><?= esc($r['size']) ?></td>
<td><?= esc($r['buyer']) ?><?= $r['status'] === 'approved' ? ' (' . esc($r['student_no']) . ')' : '' ?></td>
<td><span class="badge b-<?= esc($r['status']) ?>"><?= esc($r['status']) ?></span></td><td><?= esc($r['created_at']) ?></td><td>
<?php if ($r['status'] === 'pending'): foreach (['approve', 'reject'] as $a): ?>
<form method="post" action="<?= site_url('market/decide') ?>" class="inline" data-confirm="<?= esc(ucfirst($a)) ?> this request?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $r['id'] ?>"><input type="hidden" name="action" value="<?= $a ?>"><button class="btn sm<?= $a === 'reject' ? ' ghost' : '' ?>"><?= esc(ucfirst($a)) ?></button></form>
<?php endforeach; endif ?></td></tr><?php endforeach ?></table></div><?php endif ?>

<h2 class="sec">My purchase requests</h2>
<?php if (!$out): ?><p class="empty">You have not requested anything. <a href="<?= site_url('market') ?>">Visit the Market</a></p><?php else: ?>
<div class="tablewrap"><table><tr><th>Uniform</th><th>Size</th><th>Price</th><th>Seller</th><th>Status</th><th></th></tr>
<?php foreach ($out as $r): ?><tr><td><?= esc($r['name']) ?></td><td><?= esc($r['size']) ?></td>
<td><?= (float) $r['price'] > 0 ? '₱' . number_format((float) $r['price'], 2) : 'Free' ?></td>
<td><?= esc($r['seller']) ?><?= $r['status'] === 'approved' ? ' (' . esc($r['seller_no']) . ')' : '' ?></td>
<td><span class="badge b-<?= esc($r['status']) ?>"><?= esc($r['status']) ?></span></td><td>
<?php if ($r['status'] === 'pending'): ?><form method="post" action="<?= site_url('market/cancel') ?>" class="inline" data-confirm="Cancel this request?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $r['id'] ?>"><button class="btn sm ghost">Cancel</button></form><?php endif ?></td></tr><?php endforeach ?></table></div>
<p class="mu">Approved means the seller accepted. Message the seller and meet on campus to pay and get the uniform.</p><?php endif ?>
<?= $this->endSection() ?>
