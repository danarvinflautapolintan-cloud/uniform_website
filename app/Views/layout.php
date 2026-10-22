<?php
$u = session('u');
$p = uri_string();
$adm = str_starts_with($p, 'admin');
$on = static fn($x) => $p === $x ? ' class="on"' : '';
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= $adm ? 'Admin | ' : '' ?>Uniform Exchange</title><link rel="stylesheet" href="<?= base_url('style.css') ?>"></head>
<body class="<?= $adm ? 'admin' : '' ?>">
<?php if ($adm): ?>
<nav class="nav"><a class="brand" href="<?= site_url('admin') ?>">🛠 Admin Panel</a>
<?php if ($u && $u['role'] === 'admin'): ?>
<a href="<?= site_url('admin') ?>"<?= $on('admin') ?>>Dashboard</a><a href="<?= site_url('admin/listings') ?>"<?= $on('admin/listings') ?>>Listings</a><a href="<?= site_url('admin/users') ?>"<?= $on('admin/users') ?>>Students</a><a href="<?= site_url('admin/requests') ?>"<?= $on('admin/requests') ?>>Requests</a><a href="<?= site_url('admin/report') ?>"<?= $on('admin/report') ?>>Report</a>
<form method="post" action="<?= site_url('admin/logout') ?>" class="inline"><?= csrf_field() ?><button>Logout</button></form>
<?php endif ?></nav>
<?php else: ?>
<nav class="nav"><a class="brand" href="<?= site_url($u ? '/' : 'login') ?>">👕 Uniform Exchange</a>
<?php if ($u && $u['role'] === 'admin'): ?>
<form method="post" action="<?= site_url('admin/logout') ?>" class="inline"><?= csrf_field() ?><button>Logout</button></form>
<?php elseif ($u): ?>
<a href="<?= site_url('/') ?>"<?= $on('') ?>>Browse</a><a href="<?= site_url('market') ?>"<?= $on('market') ?>>Market</a><a href="<?= site_url('mine') ?>"<?= $on('mine') ?>>My requests</a><a href="<?= site_url('market/my') ?>"<?= $on('market/my') ?>>My listings</a>
<span class="who">Hi, <?= esc($u['name']) ?></span>
<form method="post" action="<?= site_url('logout') ?>" class="inline"><?= csrf_field() ?><button>Logout</button></form>
<?php else: ?><a href="<?= site_url('login') ?>"<?= $on('login') ?>>Login</a><a href="<?= site_url('register') ?>"<?= $on('register') ?>>Register</a><?php endif ?></nav>
<?php endif ?>
<main><?php if ($m = session()->getFlashdata('msg')) echo '<p class="msg">' . esc($m) . '</p>'; if ($m = session()->getFlashdata('err')) echo '<p class="err">' . esc($m) . '</p>'; ?>
<?= $this->renderSection('c') ?></main><script src="<?= base_url('app.js') ?>"></script></body></html>