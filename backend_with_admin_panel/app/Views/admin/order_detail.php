<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order #<?= esc($order['id']) ?></title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; margin: 20px; background:#f7f7f8; }
        .container{ max-width:1000px; margin:0 auto; }
        .card{ background:#fff; padding:16px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.06); margin-bottom:12px; }
        table{ width:100%; border-collapse: collapse; }
        th, td{ padding:10px; text-align:left; border-bottom:1px solid #eee; vertical-align:middle; }
        .btn{ display:inline-block; padding:8px 12px; border-radius:6px; text-decoration:none; color:#fff; font-size:14px; }
        .btn-primary{ background:#5b6cff; }
        .btn-muted{ background:#6c757d; }
        .section{ margin-bottom:12px; }
        .status-pill{ padding:6px 10px; border-radius:12px; color:#fff; font-weight:600; font-size:13px; }
        .status-pending{ background:#ff9800; }
        .status-shipped{ background:#1976d2; }
        .status-delivered{ background:#2e7d32; }
        .status-cancelled{ background:#b71c1c; }
        .small-muted{ color:#666; font-size:13px; }
    </style>
</head>
<body>
<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Order #<?= esc($order['id']) ?></h2>
        <div>
            <a class="btn btn-muted" href="<?= base_url('admin/orders') ?>">Back to Orders</a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div style="background:#e6f7ec; color:#0a7a2b; padding:10px; border-radius:6px; margin-bottom:8px;"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div style="background:#ffecec; color:#b71c1c; padding:10px; border-radius:6px; margin-bottom:8px;"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="card section">
        <strong>Status:</strong>
        <?php $s = $order['status']; ?>
        <span class="status-pill <?= 'status-'.$s ?>" style="margin-left:8px;"><?= ucfirst($s) ?></span>

        <div style="float:right;">
            <form method="post" action="<?= base_url('admin/orders/changeStatus/'.$order['id']) ?>">
                <?= csrf_field() ?>
                <select name="status" required style="padding:8px;border-radius:6px;border:1px solid #ddd;">
                    <option value="pending" <?= $s==='pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="shipped" <?= $s==='shipped' ? 'selected' : '' ?>>Shipped</option>
                    <option value="delivered" <?= $s==='delivered' ? 'selected' : '' ?>>Delivered</option>
                    <option value="cancelled" <?= $s==='cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
                <button type="submit" class="btn btn-primary" style="margin-left:8px;">Update</button>
            </form>
        </div>

        <div style="clear:both;"></div>
        <div class="small-muted" style="margin-top:6px;">
  Placed at: <?= esc($order['created_at'] ?? '—') ?> — User ID: <?= esc($order['user_id'] ?? '—') ?>
</div>


    <div class="card section">
        <h3>Items</h3>
        <?php if (empty($items)): ?>
            <p>No items found for this order.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th style="width:80px">#</th>
                        <th>Product</th>
                        <th style="width:110px">Unit Price</th>
                        <th style="width:100px">Quantity</th>
                        <th style="width:140px">Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $i => $it): ?>
                        <tr>
                            <td><?= $i+1 ?></td>
                            <td>
                                <div style="display:flex; gap:8px; align-items:center;">
                                    <?php if (!empty($it['product_image'])): ?>
                                        <img src="<?= esc($it['product_image']) ?>" alt="" style="width:56px;height:56px;object-fit:cover;border-radius:6px;">
                                    <?php endif; ?>
                                    <div>
                                        <div style="font-weight:600;"><?= esc($it['product_name'] ?? ('#'.$it['product_id'])) ?></div>
                                        <div class="small-muted"><?= esc($it['product_id']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>₹<?= number_format($it['price'],2) ?></td>
                            <td><?= (int)$it['quantity'] ?></td>
                            <td>₹<?= number_format($it['price'] * $it['quantity'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div style="text-align:right; margin-top:12px; font-weight:700;">
                Grand total: ₹<?= number_format($order['total_price'], 2) ?>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
