<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin — Users</title>

    <style>
        body { font-family: Arial, Helvetica, sans-serif; margin: 20px; background:#f7f7f8; }
        .container{ max-width:1100px; margin:0 auto; }
        .card{ background:#fff; padding:16px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.06); margin-bottom:12px; }
        table{ width:100%; border-collapse: collapse; }
        th, td{ padding:10px; text-align:left; border-bottom:1px solid #eee; }
        .actions form{ display:inline-block; margin:0 4px; }
        .btn{ display:inline-block; padding:8px 12px; border-radius:6px; text-decoration:none; color:#fff; font-size:14px; }
        .btn-primary{ background:#5b6cff; }
        .btn-warning{ background:#ffb74d; }
        .btn-danger{ background:#e53935; }
        .btn-muted{ background:#6c757d; color:#fff; }
        .flash{ padding:10px; border-radius:6px; margin-bottom:12px; }
        .flash-success{ background:#e6f7ec; color:#0a7a2b; }
        .flash-error{ background:#ffecec; color:#b71c1c; }
        .top-row{ display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; gap:12px; }
    </style>
</head>

<body>
<div class="container">

    <div class="top-row">
        <h2>Users</h2>
        <div>
            <a class="btn btn-primary" href="<?= base_url('admin/users/create') ?>">+ Add User</a>
            <a class="btn btn-muted" href="<?= base_url('admin') ?>">Back to Dashboard</a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="flash flash-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="flash flash-error"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>


    <div class="card">
        <?php if (empty($users)): ?>
            <p>No users found.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width:60px">ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th style="width:120px">Role</th>
                    <th style="width:160px">Created At</th>
                    <th style="width:200px">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= esc($u['id']) ?></td>
                    <td><?= esc($u['username']) ?></td>
                    <td><?= esc($u['email']) ?></td>
                    <td><?= esc($u['role']) ?></td>
                    <td><?= esc($u['created_at']) ?></td>
                    <td class="actions">

                        <a class="btn btn-warning" href="<?= base_url('admin/users/edit/'.$u['id']) ?>">Edit</a>

                        <form method="post"
                              action="<?= base_url('admin/users/delete/'.$u['id']) ?>"
                              onsubmit="return confirm('Delete this user?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>

                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
