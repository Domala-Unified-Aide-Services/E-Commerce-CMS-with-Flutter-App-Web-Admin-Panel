<h1>Products</h1>
<p><button type="button" onclick="location.href='<?= base_url('admin/products/create') ?>'">Add product</button></p>

<table border="1" cellpadding="6" style="border-collapse:collapse;">
  <tr><th>ID</th><th>Name</th><th>Price</th><th>Stock</th><th>Actions</th></tr>
  <?php if (!empty($products)): foreach ($products as $p): ?>
    <tr>
      <td><?= esc($p['id']) ?></td>
      <td><?= esc($p['name']) ?></td>
      <td><?= esc($p['price']) ?></td>
      <td><?= esc($p['stock']) ?></td>
      <td>
        <a href="<?= base_url('admin/products/edit/'.$p['id']) ?>">Edit</a>
        <form method="post" action="<?= base_url('admin/products/delete/'.$p['id']) ?>" style="display:inline">
          <?= csrf_field() ?>
          <button type="submit" onclick="return confirm('Delete product?')">Delete</button>
        </form>
      </td>
    </tr>
  <?php endforeach; else: ?>
    <tr><td colspan="5">No products found.</td></tr>
  <?php endif; ?>
</table>
