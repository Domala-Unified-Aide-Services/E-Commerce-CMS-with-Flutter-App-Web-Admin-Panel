<?php 
$isEdit = isset($product); 
$selectedCat = $isEdit ? ($product['category_id'] ?? null) : null;
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $isEdit ? "Edit Product" : "Add Product" ?></title>

    <style>
        body { font-family: Arial, Helvetica, sans-serif; background:#f7f7f8; padding:20px; }
        .container { max-width:800px; margin:auto; }
        .card { background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.06); }
        label { font-weight:600; display:block; margin-bottom:6px; }
        input[type="text"], input[type="number"], textarea, select {
            width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box;
        }
        .btn { padding:10px 16px; border-radius:6px; text-decoration:none; display:inline-block; color:#fff; }
        .btn-primary { background:#5b6cff; }
        .btn-muted { background:#6c757d; }
        .top-actions { display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; }
    </style>
</head>

<body>
<div class="container">

    <div class="top-actions">
        <h2><?= $isEdit ? "Edit Product" : "Add Product" ?></h2>

        <!-- ✅ Back to Dashboard -->
        <a class="btn btn-muted" href="<?= base_url('admin/dashboard') ?>">← Back to Dashboard</a>
    </div>

    <div class="card">

        <?php 
        $action = $isEdit 
            ? base_url('admin/products/update/'.$product['id'])
            : base_url('admin/products/store');
        ?>

        <form id="product_form" method="post" action="<?= $action ?>">
            <?= csrf_field() ?>

            <!-- NAME -->
            <div style="margin-bottom:15px;">
                <label>Name</label>
                <input type="text" name="name" required value="<?= $isEdit ? esc($product['name']) : '' ?>">
            </div>

            <!-- DESCRIPTION -->
            <div style="margin-bottom:15px;">
                <label>Description</label>
                <textarea name="description" rows="4"><?= $isEdit ? esc($product['description']) : '' ?></textarea>
            </div>

            <!-- PRICE -->
            <div style="margin-bottom:15px;">
                <label>Price</label>
                <input type="number" step="0.01" name="price" required 
                       value="<?= $isEdit ? esc($product['price']) : '' ?>">
            </div>

            <!-- STOCK -->
            <div style="margin-bottom:15px;">
                <label>Stock</label>
                <input type="number" name="stock" value="<?= $isEdit ? esc($product['stock']) : 0 ?>">
            </div>

            <!-- CATEGORY -->
            <div style="margin-bottom:15px;">
                <label>Category</label>
                <select name="category_id" required>
                    <option value="">-- Select Category --</option>

                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>"
                            <?= ($selectedCat == $c['id']) ? 'selected' : '' ?>>
                            <?= esc($c['name']) ?> (ID: <?= $c['id'] ?>)

                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- IMAGE URL -->
            <div style="margin-bottom:10px;">
                <label>Image URL</label>
                <input id="image_url" type="text" name="image_url" 
                       value="<?= $isEdit ? esc($product['image_url']) : '' ?>" 
                       placeholder="Paste image URL or upload below">
            </div>

            <!-- UPLOAD UI -->
            <div style="margin-bottom:10px;">
                <label>Upload Image</label><br>
                <input id="file_input" type="file" accept="image/*">
                <button type="button" id="upload_btn" class="btn btn-primary" style="margin-top:6px;">Upload</button>
                <span id="upload_status" style="margin-left:10px; color:#666"></span>
            </div>

            <!-- PREVIEW -->
            <div style="margin-top:10px;">
                <img id="preview_img"
                     src="<?= $isEdit && $product['image_url'] ? esc($product['image_url']) : '' ?>"
                     style="max-width:220px; border-radius:6px; display:<?= ($isEdit && $product['image_url']) ? 'block' : 'none' ?>;">
            </div>

            <!-- BUTTONS -->
            <div style="margin-top:20px; display:flex; gap:10px;">
                <button class="btn btn-primary" type="submit"><?= $isEdit ? "Update" : "Create" ?></button>
                <a class="btn btn-muted" href="<?= base_url('admin/products') ?>">Cancel</a>
            </div>

        </form>
    </div>
</div>

<script>
// ------------------------------
// IMAGE UPLOADER SCRIPT
// ------------------------------
(function(){
  const uploadBtn   = document.getElementById('upload_btn');
  const input       = document.getElementById('file_input');
  const status      = document.getElementById('upload_status');
  const preview     = document.getElementById('preview_img');
  const imageField  = document.getElementById('image_url');

  function getCsrf() {
    const tokenName = '<?= csrf_token() ?>';
    const el = document.querySelector(`#product_form input[name="${tokenName}"]`);
    return el ? { name: el.name, value: el.value } : null;
  }

  uploadBtn.onclick = function() {
    const file = input.files[0];
    if (!file) { status.textContent = "Choose a file"; return; }

    status.textContent = "Uploading...";

    const fd = new FormData();
    fd.append('file', file);

    const csrf = getCsrf();
    if (csrf) fd.append(csrf.name, csrf.value);

    fetch('<?= base_url("admin/uploads/store") ?>', {
      method: 'POST',
      body: fd,
      credentials: 'include',
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.text())
    .then(text => {
      let data;
      try { data = JSON.parse(text); } catch(e){ status.textContent = "Invalid JSON"; return; }

      if (data.url) {
        imageField.value = data.url;
        preview.src = data.url;
        preview.style.display = 'block';
        status.textContent = "Uploaded";
      } else {
        status.textContent = data.error || "Upload failed";
      }
    })
    .catch(e => status.textContent = "Upload error");
  };
})();
</script>

</body>
</html>
