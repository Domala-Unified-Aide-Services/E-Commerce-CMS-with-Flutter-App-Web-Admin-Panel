<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= isset($user) ? 'Edit' : 'Add' ?> User</title>

    <style>
        body { font-family: Arial, Helvetica, sans-serif; margin: 20px; background:#f7f7f8; }
        .container{ max-width:700px; margin:0 auto; }
        .card{ background:#fff; padding:18px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.06); }
        label{ font-weight:600; display:block; margin-bottom:6px; color:#333; }
        input[type="text"], input[type="email"], input[type="password"], select{
            width:100%; padding:10px; border:1px solid #dcdcdc;
            border-radius:6px; box-sizing:border-box;
        }
        .btn{ display:inline-block; padding:10px 14px; border-radius:6px; text-decoration:none; color:#fff; }
        .btn-primary{ background:#5b6cff; }
        .btn-muted{ background:#6c757d; }
        .field{ margin-bottom:12px; }
    </style>
</head>

<body>
<div class="container">

    <h2><?= isset($user) ? 'Edit User' : 'Add User' ?></h2>

    <?php if (session()->getFlashdata('error')): ?>
        <div style="background:#ffecec; color:#b71c1c; padding:10px; border-radius:6px; margin-bottom:12px;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="card">

        <?php
            $isEdit = isset($user);

            $action = $isEdit
                ? base_url('admin/users/update/'.$user['id'])
                : base_url('admin/users/store');
        ?>

        <form method="post" action="<?= $action ?>">
            <?= csrf_field() ?>

            <div class="field">
                <label>Username</label>
                <input type="text" name="username"
                    value="<?= $isEdit ? esc($user['username']) : '' ?>" required>
            </div>

            <div class="field">
                <label>Email</label>
                <input type="email" name="email"
                    value="<?= $isEdit ? esc($user['email']) : '' ?>" required>
            </div>

            <div class="field">
                <label>Password <?= $isEdit ? '(leave blank to keep old)' : '' ?></label>
                <input type="password" name="password">
            </div>

            <div class="field">
                <label>Role</label>
                <select name="role" required>
                    <option value="user" <?= $isEdit && $user['role']=='user' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= $isEdit && $user['role']=='admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>

            <div style="display:flex; gap:8px;">
                <button class="btn btn-primary" type="submit">
                    <?= $isEdit ? 'Update' : 'Create' ?>
                </button>

                <a class="btn btn-muted" href="<?= base_url('admin/users') ?>">Cancel</a>
            </div>
        </form>

    </div>
</div>
</body>
</html>
