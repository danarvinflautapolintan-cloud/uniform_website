<?= $this->extend('layout') ?><?= $this->section('c') ?>
<div class="top"><h2><?= $it['id'] ? 'Edit listing' : 'Sell a uniform' ?></h2><a class="btn ghost" href="<?= site_url('market/my') ?>">← Back</a></div>
<form method="post" action="<?= site_url('market/save') ?>" enctype="multipart/form-data" class="panel"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $it['id'] ?>">
<div class="cols">
  <div class="photo-box">
    <img id="preview" class="preview" alt="Preview" src="<?= $it['photo'] ? base_url('uploads/' . esc($it['photo'], 'url')) : '' ?>"<?= $it['photo'] ? '' : ' hidden' ?>>
    <label>Photo<input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp" <?= $it['id'] ? '' : 'required' ?>></label>
    <p class="mu">JPG, PNG or WEBP, max 2MB</p>
  </div>
  <div class="fields">
    <label>Name<input name="name" required minlength="3" maxlength="100" placeholder="e.g. BSIT Male Uniform" value="<?= esc(old('name', $it['name'])) ?>"></label>
    <label>Description<textarea name="description" rows="3" maxlength="255" placeholder="Condition details, how long used, etc."><?= esc(old('description', $it['description'])) ?></textarea></label>
    <label>Size<select name="size"><?php foreach ($sizes as $s) echo '<option' . ($s === old('size', $it['size']) ? ' selected' : '') . '>' . esc($s) . '</option>'; ?></select></label>
    <label>Condition<select name="item_condition"><?php foreach ($conds as $c) echo '<option' . ($c === old('item_condition', $it['item_condition']) ? ' selected' : '') . '>' . esc($c) . '</option>'; ?></select></label>
    <label>Price (₱)<input type="number" name="price" min="0" max="99999" step="0.01" required value="<?= esc(old('price', $it['price'])) ?>"></label>
    <p class="mu gap">Your listing will be reviewed by the admin before it appears in the Market.</p>
    <div class="actions"><button class="btn">Submit listing</button><a class="btn ghost" href="<?= site_url('market/my') ?>">Cancel</a></div>
  </div>
</div>
</form>
<?= $this->endSection() ?>