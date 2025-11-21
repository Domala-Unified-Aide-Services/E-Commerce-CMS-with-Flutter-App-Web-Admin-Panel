<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin</title>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; padding: 12px;">

<!-- NAVIGATION -->
 
<nav style="
    background:#2d2d2d;
    padding:14px 20px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    color:white;
">
    <div>
        <a href="<?= base_url('admin') ?>" style="color:white; margin-right:20px; text-decoration:none;">Dashboard</a>
        <a href="<?= base_url('admin/products') ?>" style="color:white; margin-right:20px; text-decoration:none;">Products</a>
        <a href="<?= base_url('admin/uploads') ?>" style="color:white; margin-right:20px; text-decoration:none;">Uploads</a>
    </div>

    <div>
        <span style="margin-right:20px; color:#ccc;">
            <?= esc(session()->get('user_name')) ?>
        </span>

        <a href="<?= base_url('admin/logout') ?>" 
           style="color:#ff6666; font-weight:bold; text-decoration:none;">
            Logout
        </a>
    </div>
</nav>




<!-- FLASH SUCCESS MESSAGE -->
<?php if (session()->getFlashdata('success')): ?>
<div 
  style="
    padding:8px;
    margin:8px 0;
    border-radius:4px;
    background:#e6ffe6;
    border:1px solid #6fd36f;
    color:#226622;
  "
>
  <?= session()->getFlashdata('success') ?>
</div>
<?php endif; ?>


<!-- FLASH ERROR MESSAGE -->
<?php if (session()->getFlashdata('error')): ?>
<div 
  style="
    padding:8px;
    margin:8px 0;
    border-radius:4px;
    background:#ffe6e6;
    border:1px solid #d36f6f;
    color:#880000;
  "
>
  <?= session()->getFlashdata('error') ?>
</div>
<?php endif; ?>


<hr style="margin-top:20px;">