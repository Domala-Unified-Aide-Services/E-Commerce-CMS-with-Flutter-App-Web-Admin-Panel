<!DOCTYPE html>
<html>
<head>
    <title>Orders List (Admin)</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        table { border-collapse: collapse; width: 90%; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        h2 { color: #333; }
        .status-pending { color: orange; font-weight: bold; }
        .status-completed { color: green; font-weight: bold; }
        .status-cancelled { color: red; font-weight: bold; }
    </style>
</head>
<body>

    <h2>Orders List (Admin)</h2>

    <?php if (!empty($orders) && is_array($orders)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td><?= esc($o['id']) ?></td>
                        <td><?= esc($o['user_id']) ?></td>
                        <td>₹<?= esc(number_format($o['total_price'], 2)) ?></td>
                        <td class="status-<?= strtolower(esc($o['status'])) ?>">
                            <?= ucfirst(esc($o['status'])) ?>
                        </td>
                        <td><?= esc($o['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No orders found.</p>
    <?php endif; ?>

</body>
</html>
