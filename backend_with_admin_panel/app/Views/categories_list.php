<!DOCTYPE html>
<html>
<head>
    <title>Categories List</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        table { border-collapse: collapse; width: 80%; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        h2 { color: #333; }
        a { text-decoration: none; color: #007bff; margin-right: 10px; }
        a:hover { text-decoration: underline; }
        .actions { white-space: nowrap; }
        .btn { padding: 6px 12px; border: none; cursor: pointer; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-success { background-color: #28a745; color: white; }
    </style>
</head>
<body>

    <h2>Categories List</h2>

    <a href="<?= base_url('admin/categories/create') ?>" class="btn btn-success">Add Category</a>

    <?php if (!empty($categories) && is_array($categories)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $c): ?>
                    <tr>
                        <td><?= esc($c['id']) ?></td>
                        <td><?= esc($c['name']) ?></td>
                        <td><?= esc($c['description']) ?></td>
                        <td class="actions">
                            <a href="<?= base_url('admin/categories/edit/' . $c['id']) ?>" class="btn btn-primary">Edit</a>
                            <form method="post" action="<?= base_url('admin/categories/delete/' . $c['id']) ?>" style="display:inline;" onsubmit="return confirm('Delete this category?')">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No categories found.</p>
    <?php endif; ?>

</body>
</html>