<h1>Uploads</h1>

<form action="<?= base_url('admin/uploads/store') ?>" method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <input type="file" name="file" required>
  <button type="submit">Upload</button>
</form>

<table border="1" cellpadding="6" style="margin-top:12px; border-collapse:collapse;">
  <tr><th>ID</th><th>Filename</th><th>Filepath</th><th>Uploaded At</th><th>Actions</th></tr>
  <?php if (!empty($uploads)): foreach ($uploads as $u): ?>
    <tr>
      <td><?= esc($u['id']) ?></td>
      <td><?= esc($u['filename']) ?></td>
      <td>
        <?php
          $link = $u['filepath'];
          // if filepath is relative to writable/uploads, create a download link via controller or expose public path
        ?>
        <?= esc($link) ?>
      </td>
      <td><?= esc($u['uploaded_at']) ?></td>
      <td>
        <form method="post" action="<?= base_url('admin/uploads/delete/'.$u['id']) ?>" style="display:inline">
          <?= csrf_field() ?>
          <button type="submit" onclick="return confirm('Delete?')">Delete</button>
        </form>
      </td>
    </tr>
  <?php endforeach; else: ?>
    <tr><td colspan="5">No uploads yet.</td></tr>
  <?php endif; ?>
</table>
