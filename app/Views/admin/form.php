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
    <label>Price (₱)<input type="number" name="price" min="0" max="99999" step="0.01" required placeholder="0.00" value="<?= esc(old('price', $it['price'])) ?>"></label>
    <h3>Stock per size</h3>
    <div class="sizes">
    <?php foreach ($sizes as $s): ?>
      <label class="sz-box"><span><?= esc($s) ?></span><input type="number" name="stock[<?= esc($s) ?>]" min="0" max="999" required value="<?= (int) ($stock[$s] ?? 0) ?>"></label>
    <?php endforeach ?>
    </div>
    <div class="actions"><button class="btn">Save uniform</button><a class="btn ghost" href="<?= site_url('admin') ?>">Cancel</a></div>
  </div>
</div>
</form>
<?= $this->endSection() ?>