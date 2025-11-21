<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Categories (Admin)</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
    <style>
        body { font-family: Arial, Helvetica, sans-serif; margin: 20px; background:#f7f7f8; }
        .container{ max-width:1000px; margin:0 auto; }
        table { border-collapse: collapse; width: 100%; background: #fff; border-radius: 8px; overflow: hidden; }
        th, td { padding: 12px 10px; text-align: left; border-bottom: 1px solid #eee; vertical-align: middle; }
        th { background: #fafafa; font-weight: 700; color: #333; }
        .actions a { margin-right: 8px; text-decoration: none; color: #5b6cff; }
        .thumb { width: 64px; height: 48px; object-fit: cover; border-radius: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); }
        .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
        .btn { padding:8px 12px; border-radius:6px; text-decoration:none; color:#fff; background:#5b6cff; }
        .btn-muted { background:#6c757d; color:#fff; text-decoration:none; padding:8px 12px; border-radius:6px; }
        .small-muted { color:#666; font-size:13px; }
    </style>
</head>
<body>
<div class="container">

    <!-- TOP BAR -->
    <div class="topbar">
        <a class="btn-muted" href="<?= base_url('admin/dashboard') ?>">← Back to Dashboard</a>
        <h2>Categories</h2>
        <a class="btn" href="<?= base_url('admin/categories/create') ?>">Create Category</a>
    </div>

    <!-- ALERTS -->
    <?php if (session()->getFlashdata('success')): ?>
        <div style="background:#e6f7ec; color:#0a7a2b; padding:10px; border-radius:6px; margin-bottom:12px;">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div style="background:#ffecec; color:#b71c1c; padding:10px; border-radius:6px; margin-bottom:12px;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- TABLE -->
    <?php if (empty($categories)): ?>
        <div style="background:#fff; padding:20px; border-radius:8px;">No categories found.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width:60px">#</th>
                    <th style="width:90px">Image</th>
                    <th>Name</th>
                    <th style="width:40%">Description</th>
                    <th style="width:170px">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $c): ?>
                    <?php
                        $img = !empty($c['image'])
                            ? base_url($c['image'])
                            : base_url('assets/img/placeholder.png');
                    ?>
                    <tr>
                        <td><?= esc($c['id']) ?></td>
                        <td><img class="thumb" src="<?= esc($img) ?>" alt=""></td>
                        <td><?= esc($c['name']) ?></td>
                        <td class="small-muted"><?= esc($c['description'] ?: '—') ?></td>
                        <td class="actions">
                            <a href="<?= base_url('admin/categories/edit/'.esc($c['id'])) ?>">Edit</a>
                            <form method="post" action="<?= base_url('admin/categories/delete/'.esc($c['id'])) ?>"
                                  style="display:inline;" onsubmit="return confirm('Delete this category?');">
                                <?= csrf_field() ?>
                                <button type="submit"
                                        style="background:none;border:none;padding:0;color:#d32f2f;cursor:pointer;">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>
</body>
</html>
