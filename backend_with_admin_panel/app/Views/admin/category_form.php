<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $category ? 'Edit' : 'Add' ?> Category</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
    <style>
        body { font-family: Arial, Helvetica, sans-serif; margin: 20px; background:#f7f7f8; }
        .container{ max-width:700px; margin:0 auto; }
        .card{ background:#fff; padding:18px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.06); }
        label{ font-weight:600; display:block; margin-bottom:6px; color:#333; }
        input[type="text"], textarea{ width:100%; padding:10px; border:1px solid #dcdcdc; border-radius:6px; box-sizing:border-box; }
        .btn{ display:inline-block; padding:10px 14px; border-radius:6px; text-decoration:none; color:#fff; }
        .btn-primary{ background:#5b6cff; }
        .btn-muted{ background:#6c757d; }
        .field{ margin-bottom:12px; }
        .img-preview { max-width:160px; border-radius:6px; box-shadow:0 2px 6px rgba(0,0,0,0.06); }
    </style>
</head>
<body>
<div class="container">
    <h2><?= $category ? 'Edit' : 'Add' ?> Category</h2>

    <?php if (session()->getFlashdata('error')): ?>
        <div style="background:#ffecec; color:#b71c1c; padding:10px; border-radius:6px; margin-bottom:12px;"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="card">
        <?php
            $action = $category ? base_url('admin/categories/update/' . $category['id']) : base_url('admin/categories/store');
            $nameVal = $category ? esc($category['name']) : '';
            $descVal = $category ? esc($category['description']) : '';
            $imgVal = $category && !empty($category['image']) ? esc($category['image']) : '';
        ?>
        <form method="post" action="<?= $action ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="field">
                <label for="name">Name</label>
                <input id="name" name="name" type="text" value="<?= $nameVal ?>" required />
            </div>

            <div class="field">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"><?= $descVal ?></textarea>
            </div>

            <div class="field">
                <label for="image">Category Image (optional)</label>
                <input id="image" name="image" type="file" accept="image/*" />
                <?php if ($imgVal): ?>
                    <div style="margin-top:10px;">
                        <div style="font-size:12px;color:#666;margin-bottom:6px;">Current image:</div>
                        <img class="img-preview" src="<?= base_url($imgVal) ?>" alt="category image" />
                    </div>
                <?php endif; ?>
            </div>

            <div style="display:flex; gap:8px;">
                <button class="btn btn-primary" type="submit"><?= $category ? 'Update' : 'Create' ?></button>
                <a class="btn btn-muted" href="<?= base_url('admin/categories') ?>">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
