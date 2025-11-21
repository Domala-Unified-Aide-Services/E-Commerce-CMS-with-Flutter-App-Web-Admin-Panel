<?php include('header.php'); ?>

<style>
.dashboard-cards {
    display: flex;
    gap: 20px;
    margin-bottom: 25px;
}

.card {
    flex: 1;
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 0 10px rgb(0 0 0 / 0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card .left .title {
    font-size: 14px;
    color: #555;
}

.card .left .value {
    font-size: 26px;
    font-weight: bold;
    margin: 5px 0;
}

.card .left .link {
    font-size: 13px;
    color: #7B61FF;
    text-decoration: none;
}

.card-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display:flex;
    justify-content:center;
    align-items:center;
    font-weight:bold;
    color:white;
    font-size:20px;
}

.icon-sales { background:#7c3aed; }
.icon-cat   { background:#f59e0b; }
.icon-users { background:#10b981; }
.icon-orders{ background:#0284c7; }

.table-container {
    margin-top: 30px;
}
</style>

<?php
$formattedSales = $totalSales > 0
    ? "₹" . number_format($totalSales, 2)
    : "₹0.00";
?>

<h1>Admin Dashboard</h1>
<p style="color:#555;">Quick overview of your store</p>

<div class="dashboard-cards">

    <!-- TOTAL SALES -->
    <div class="card">
        <div class="left">
            <div class="title">Total Sales</div>
            <div class="value"><?= $formattedSales ?></div>
            <a href="<?= base_url('admin/orders') ?>" class="link">View orders</a>
        </div>
        <div class="card-icon icon-sales">₹</div>
    </div>

    <!-- CATEGORIES -->
    <div class="card">
        <div class="left">
            <div class="title">Categories</div>
            <div class="value"><?= $totalCategories ?></div>
            <a href="<?= base_url('admin/categories') ?>" class="link">Manage categories</a>
        </div>
        <div class="card-icon icon-cat">C</div>
    </div>

    <!-- USERS -->
    <div class="card">
        <div class="left">
            <div class="title">Users</div>
            <div class="value"><?= $totalUsers ?></div>
            <a href="<?= base_url('admin/users') ?>" class="link">Manage users</a>
        </div>
        <div class="card-icon icon-users">U</div>
    </div>

    <!-- ORDERS -->
    <div class="card">
        <div class="left">
            <div class="title">Orders</div>
            <div class="value"><?= $totalOrders ?></div>
            <a href="<?= base_url('admin/orders') ?>" class="link">View orders</a>
        </div>
        <div class="card-icon icon-orders">O</div>
    </div>

</div>

<!-- RECENT ORDERS TABLE -->

<div class="table-container">
    <h2>Recent Orders</h2>

    <style>
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 10px rgb(0 0 0 / 0.08);
        }

        .orders-table th {
            background: #f9fafb;
            padding: 12px;
            font-weight: bold;
            color: #444;
            text-align: left;
            font-size: 14px;
            border-bottom: 1px solid #e5e7eb;
        }

        .orders-table td {
            padding: 12px;
            font-size: 14px;
            color: #333;
            border-bottom: 1px solid #f0f0f0;
        }

        .status-pill {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }

        .status-delivered { background:#bbf7d0; color:#166534; }
        .status-pending   { background:#fef3c7; color:#92400e; }
        .status-cancelled { background:#fee2e2; color:#b91c1c; }
        .status-shipped   { background:#dbeafe; color:#1e40af; }

    </style>

    <?php if (!empty($recentOrders)): ?>
        <table class="orders-table">
            <thead>
                <tr>
                    <th style="width:70px;">ID</th>
                    <th style="width:90px;">User ID</th>
                    <th style="width:120px;">Total</th>
                    <th style="width:120px;">Status</th>
                    <th style="width:200px;">Created</th>
                    <th style="width:80px;">Action</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($recentOrders as $o): ?>
                <tr>
                    <td><?= $o['id'] ?></td>
                    <td><?= $o['user_id'] ?></td>
                    <td>₹<?= number_format($o['total_price'], 2) ?></td>

                    <td>
                        <?php 
                            $statusClass = [
                                'delivered' => 'status-delivered',
                                'pending'   => 'status-pending',
                                'cancelled' => 'status-cancelled',
                                'shipped'   => 'status-shipped',
                            ][$o['status']] ?? 'status-pending';
                        ?>
                        <span class="status-pill <?= $statusClass ?>">
                            <?= ucfirst($o['status']) ?>
                        </span>
                    </td>

                    <td><?= $o['created_at'] ?></td>

                    <td>
                        <a href="<?= base_url('admin/orders/view/'.$o['id']) ?>" 
                           style="
                                background:#6366f1;
                                color:white;
                                padding:6px 12px;
                                border-radius:6px;
                                text-decoration:none;
                                font-size:13px;">
                           View
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <p>No recent orders.</p>
    <?php endif; ?>
</div>

<?php include('footer.php'); ?>
