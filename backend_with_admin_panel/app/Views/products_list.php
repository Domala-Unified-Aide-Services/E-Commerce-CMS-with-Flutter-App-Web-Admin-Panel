<!DOCTYPE html>
<html>
<head>
    <title>Products List</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        table { border-collapse: collapse; width: 80%; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        h2 { color: #333; }
        a { text-decoration: none; color: #007bff; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h2>Products List</h2>

    <?php if (!empty($products) && is_array($products)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td><?= esc($p['id']) ?></td>
                        <td><a href="<?= base_url('browser/products/view/' . $p['id']) ?>"><?= esc($p['name']) ?></a></td>
                        <td><?= esc($p['price']) ?></td>
                        <td><?= esc($p['category_id']) ?></td>
                        <td><?= esc($p['stock']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No products found.</p>
    <?php endif; ?>

</body>
</html>
