<?= $this->extend('layout') ?><?= $this->section('c') ?>
<div class="top"><h2><?= $it['id'] ? 'Edit' : 'Add' ?> uniform</h2><a class="btn ghost" href="<?= site_url('admin') ?>">← Back</a></div>
<form method="post" action="<?= site_url('admin/uniforms/save') ?>" enctype="multipart/form-data" class="panel"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $it['id'] ?>">
<div class="cols">
  <div class="photo-box">
    <img id="preview" class="preview" alt="Preview" src="<?= $it['photo'] ? base_url('uploads/' . esc($it['photo'], 'url')) : '' ?>"<?= $it['photo'] ? '' : ' hidden' ?>>
    <label>Photo<input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp" <?= $it['id'] ? '' : 'required' ?>></label>
    <p class="mu">JPG, PNG or WEBP, max 2MB</p>
  </div>
  <div class="fields">
    <label>Name<input name="name" required minlength="3" maxlength="100" placeholder="e.g. TAU Polo Shirt" value="<?= esc(old('name', $it['name'])) ?>"></label>
    <label>Description<textarea name="description" rows="3" maxlength="255" placeholder="Short details (color, fabric, who it's for)"><?= esc(old('description', $it['description'])) ?></textarea></label>
    <label>Date acquired<input type="date" name="date_acquired" required value="<?= esc(old('date_acquired', $it['date_acquired'] ?? '')) ?>"></label>
    <h3>Price and stock per size</h3>
    <?php $oldPrices = old('price'); $oldStock = old('stock'); $oldPrices = is_array($oldPrices) ? $oldPrices : []; $oldStock = is_array($oldStock) ? $oldStock : []; ?>
    <div class="sizes">
    <?php foreach ($sizes as $s): ?>
      <div class="sz-box"><span><?= esc($s) ?></span>
        <label>Price (₱)<input type="number" name="price[<?= esc($s) ?>]" min="0" max="99999" step="0.01" required value="<?= esc($oldPrices[$s] ?? $sizeData[$s]['price'] ?? '0.00') ?>"></label>
        <label>Stock<input type="number" name="stock[<?= esc($s) ?>]" min="0" max="999" required value="<?= esc($oldStock[$s] ?? $sizeData[$s]['stock'] ?? 0) ?>"></label>
      </div>
    <?php endforeach ?>
    </div>
    <div class="actions"><button class="btn">Save uniform</button><a class="btn ghost" href="<?= site_url('admin') ?>">Cancel</a></div>
  </div>
</div>
</form>
<?= $this->endSection() ?>