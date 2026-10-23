<?= $this->extend('layout') ?><?= $this->section('c') ?>
<div class="hero"><h2>Hi, <?= esc(session('u')['name']) ?>! 👋</h2><p>Pick your uniform and size, then send a request. Once the admin approves it, you can claim and pay for it.</p></div>
<form method="get" action="<?= site_url('/') ?>" class="search"><input name="q" placeholder="Search uniform..." maxlength="50" value="<?= esc($q) ?>">
<select name="size"><option value="">Any size</option><?php foreach ($all as $s) echo '<option' . ($s === $size ? ' selected' : '') . '>' . esc($s) . '</option>'; ?></select>
<button class="btn">Search</button></form>
<?php if (!$items): ?><p class="empty">No matching uniforms found.</p><?php endif ?>
<div class="grid"><?php foreach ($items as $i): ?><div class="card">
<img src="<?= base_url('uploads/' . esc($i['photo'], 'url')) ?>" alt="<?= esc($i['name']) ?>">
<div class="body"><h3><?= esc($i['name']) ?></h3>
<p class="mu"><?= esc($i['description']) ?></p><div class="chips">
<?php foreach ($sizes[$i['id']] ?? [] as $s):
  $price = (float) $s['price'];
  $ask = 'Buy "' . $i['name'] . '" (size ' . $s['size'] . ') for ' . ($price > 0 ? '₱' . number_format($price, 2) : 'free') . '? Your request will be sent to the admin for approval.'; ?>
<form method="post" action="<?= site_url('reserve') ?>" class="chip<?= $s['stock'] > 0 ? '' : ' out' ?>"<?= $s['stock'] > 0 ? ' data-confirm="' . esc($ask, 'attr') . '"' : '' ?>><?= csrf_field() ?><input type="hidden" name="size_id" value="<?= (int) $s['id'] ?>">
<span class="sz"><?= esc($s['size']) ?></span><span class="st"><?= $price > 0 ? '₱' . number_format($price, 2) : 'Free' ?> · <?= $s['stock'] > 0 ? (int) $s['stock'] . ' left' : 'Out of stock' ?></span>
<?php if ($s['stock'] > 0): ?><button class="btn sm">Request to buy</button><?php endif ?></form>
<?php endforeach ?></div></div></div><?php endforeach ?></div>
<?= $this->endSection() ?>