<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin — Orders</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; margin: 20px; background:#f7f7f8; }
        .container{ max-width:1100px; margin:0 auto; }
        .card{ background:#fff; padding:16px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.06); margin-bottom:12px; }
        table{ width:100%; border-collapse: collapse; }
        th, td{ padding:10px; text-align:left; border-bottom:1px solid #eee; vertical-align:middle; }
        .btn{ display:inline-block; padding:8px 12px; border-radius:6px; text-decoration:none; color:#fff; font-size:14px; }
        .btn-primary{ background:#5b6cff; }
        .btn-info{ background:#17a2b8; }
        .flash{ padding:10px; border-radius:6px; margin-bottom:12px; }
        .flash-success{ background:#e6f7ec; color:#0a7a2b; }
        .flash-error{ background:#ffecec; color:#b71c1c; }
        .filters{ display:flex; gap:12px; align-items:center; margin-bottom:12px; }
        .status-pill{ padding:6px 10px; border-radius:12px; color:#fff; font-weight:600; font-size:13px; }
        .status-pending{ background:#ff9800; }
        .status-shipped{ background:#1976d2; }
        .status-delivered{ background:#2e7d32; }
        .status-cancelled{ background:#b71c1c; }
    </style>
</head>
<body>
<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <h2>Orders</h2>
        <a class="btn btn-primary" href="<?= base_url('admin') ?>">Back to Dashboard</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="flash flash-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="flash flash-error"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="get" class="filters" style="margin-bottom:8px;">
            <label for="status">Filter by status:</label>
            <select name="status" id="status" onchange="this.form.submit()" style="padding:8px;border-radius:6px;border:1px solid #ddd;">
                <option value="">All</option>
                <option value="pending" <?= isset($status) && $status==='pending' ? 'selected' : '' ?>>Pending</option>
                <option value="shipped" <?= isset($status) && $status==='shipped' ? 'selected' : '' ?>>Shipped</option>
                <option value="delivered" <?= isset($status) && $status==='delivered' ? 'selected' : '' ?>>Delivered</option>
                <option value="cancelled" <?= isset($status) && $status==='cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </form>

        <?php if (!empty($orders)): ?>
            <table>
                <thead>
                    <tr>
                        <th style="width:64px">ID</th>
                        <th>User</th>
                        <th style="width:140px">Total</th>
                        <th style="width:120px">Status</th>
                        <th style="width:170px">Created</th>
                        <th style="width:180px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td><?= esc($o['id']) ?></td>
                            <td><?= esc($o['user_id'] ?? '—') ?></td>

                            <td>₹<?= number_format($o['total_price'], 2) ?></td>
                            <td>
                                <?php $s = $o['status']; ?>
                                <span class="status-pill <?= 'status-'.$s ?>"><?= ucfirst($s) ?></span>
                            </td>
                            <td><?= esc($o['created_at']) ?></td>
                            <td>
                                <a class="btn btn-info" href="<?= base_url('admin/orders/view/'.$o['id']) ?>">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No orders found.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
