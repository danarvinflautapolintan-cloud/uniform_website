<?= $this->extend('layout') ?><?= $this->section('c') ?>
<div class="hero"><h2>Student marketplace 🛍</h2><p>Buy uniforms from other students. Send a request and the seller will review it. Meet up on campus to pay. There is no online payment.</p></div>
<div class="top"><h2>For sale</h2><a class="btn" href="<?= site_url('market/sell') ?>">+ Sell a uniform</a></div>
<form method="get" action="<?= site_url('market') ?>" class="search"><input name="q" placeholder="Search uniform..." maxlength="50" value="<?= esc($q) ?>">
<select name="size"><option value="">Any size</option><?php foreach ($all as $s) echo '<option' . ($s === $size ? ' selected' : '') . '>' . esc($s) . '</option>'; ?></select>
<button class="btn">Search</button></form>
<?php if (!$items): ?><p class="empty">No listings yet. Be the first to sell!</p><?php endif ?>
<div class="grid"><?php foreach ($items as $i): $price = (float) $i['price'];
$ask = 'Buy "' . $i['name'] . '" (size ' . $i['size'] . ') for ' . ($price > 0 ? '₱' . number_format($price, 2) : 'free') . '? The seller will review your request.'; ?>
<div class="card"><img src="<?= base_url('uploads/' . esc($i['photo'], 'url')) ?>" alt="<?= esc($i['name']) ?>">
<div class="body"><h3><?= esc($i['name']) ?></h3>
<p class="price"><?= $price > 0 ? '₱' . number_format($price, 2) : 'Free' ?></p>
<p><span class="tag">Size <?= esc($i['size']) ?></span><span class="tag"><?= esc($i['item_condition']) ?></span></p>
<p class="mu"><?= esc($i['description']) ?></p><p class="mu">Seller: <?= esc($i['seller']) ?></p>
<?php if ((int) $i['seller_id'] === $me): ?><span class="badge b-reserved">Your listing</span>
<?php elseif (in_array((int) $i['id'], $mine, true)): ?><span class="badge b-pending">Requested</span>
<?php else: ?><form method="post" action="<?= site_url('market/buy') ?>" class="inline" data-confirm="<?= esc($ask, 'attr') ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $i['id'] ?>"><button class="btn sm">Request to buy</button></form><?php endif ?>
</div></div><?php endforeach ?></div>
<?= $this->endSection() ?>