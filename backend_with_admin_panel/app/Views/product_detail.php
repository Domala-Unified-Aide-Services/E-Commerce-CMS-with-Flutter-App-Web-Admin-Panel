<!DOCTYPE html>
<html>
<head>
    <title>Product Details</title>
</head>
<body>

<h2>Product Details</h2>

<p><strong>ID:</strong> <?= $product['id'] ?></p>
<p><strong>Name:</strong> <?= $product['name'] ?></p>
<p><strong>Price:</strong> <?= $product['price'] ?></p>
<p><strong>Description:</strong> <?= $product['description'] ?></p>
<p><strong>Stock:</strong> <?= $product['stock'] ?></p>

<?php if (!empty($product['image_url'])): ?>
    <p><strong>Image:</strong></p>
    <img src="<?= $product['image_url'] ?>" width="200">
<?php endif; ?>

</body>
</html>
