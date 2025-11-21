<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
</head>

<body style="background:#f2f2f2; margin:0; padding:0; font-family:Arial, sans-serif;">

<div style="
    width:360px;
    margin:80px auto;
    background:white;
    padding:30px;
    border-radius:10px;
    box-shadow:0 0 12px rgba(0,0,0,0.15);
    text-align:center;
">

    <h1 style="margin-top:0; font-size:24px; color:#333;">Admin Login</h1>

    <?php if (session()->getFlashdata('error')): ?>
        <div style="
            background:#ffdddd;
            color:#a30000;
            padding:10px;
            border-radius:5px;
            margin-bottom:15px;
            font-size:14px;
        ">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('admin/auth/attempt') ?>">
        <?= csrf_field() ?>

        <div style="text-align:left; margin-bottom:12px;">
            <label style="font-weight:bold; color:#444;">Email</label><br>
            <input 
                type="email" 
                name="email" 
                required 
                style="
                    width:100%;
                    padding:10px;
                    border:1px solid #bbb;
                    border-radius:5px;
                    font-size:15px;
                    margin-top:5px;
                "
            >
        </div>

        <div style="text-align:left; margin-bottom:18px;">
            <label style="font-weight:bold; color:#444;">Password</label><br>
            <input 
                type="password" 
                name="password" 
                required 
                style="
                    width:100%;
                    padding:10px;
                    border:1px solid #bbb;
                    border-radius:5px;
                    font-size:15px;
                    margin-top:5px;
                "
            >
        </div>

        <button 
            type="submit"
            style="
                width:100%;
                padding:12px;
                background:#007bff;
                color:white;
                border:none;
                border-radius:5px;
                font-size:16px;
                cursor:pointer;
            "
        >
            Login
        </button>
    </form>

</div>

</body>
</html>