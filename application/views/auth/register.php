<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Register - Forum</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/inter.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/forum.css'); ?>">
    <style>
        :root{
            --brks-red: #b81f22;
            --brks-green: #2ca348;
            --brks-orange: #f39c12;
            --card-bg: rgba(255,255,255,0.98);
        }

        body.login-page{
            background: linear-gradient(135deg, var(--brks-orange) 0%, var(--brks-green) 48%, var(--brks-red) 100%);
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
            padding: 40px 16px;
        }

        .forum-app{ width:100%; max-width:1100px; }

        .login-card{
            width:100%; max-width:560px; margin:0 auto; background:var(--card-bg); border-radius:12px; padding:28px; box-shadow:0 12px 40px rgba(2,6,23,0.12); border:1px solid rgba(2,6,23,0.06);
        }

        .login-card .brand{ text-align:center; margin-bottom:12px }
        .login-card .brand img{
            width:180px;
            max-width:100%;
            height:auto;
            display:inline-block;
            border-radius:6px;
            background:transparent;
            padding:0;
            object-fit:contain;
        }

        .login-card h2{ margin:2px 0 8px; text-align:center; color:rgba(2,6,23,0.9); font-size:20px; font-weight:700; line-height:1.1 }

        .error-msg{ color:var(--brks-red); margin-bottom:12px; font-weight:600 }

        .form-field{ margin-bottom:14px }
        .form-field label{ display:block; font-size:12px; color:#475569; margin-bottom:6px; font-weight:600; letter-spacing:0.2px }
        .form-field input[type="text"], .form-field input[type="password"]{ width:100%; padding:12px 14px; border-radius:10px; border:1px solid #e6e9ee; outline:none; transition:box-shadow .12s ease, border-color .12s ease; font-size:14px; background:#fff }
        .form-field input:focus{ border-color:var(--brks-green); box-shadow:0 8px 30px rgba(44,163,72,0.12) }

        .actions{ display:flex; gap:10px; justify-content:space-between; align-items:center; margin-top:10px }

        .btn.primary{ background: linear-gradient(90deg, var(--brks-orange), var(--brks-red)); color:#fff; border:none; padding:10px 16px; border-radius:10px; cursor:pointer; font-weight:700 }
        .btn{ background:transparent; border:1px solid rgba(2,6,23,0.08); padding:8px 12px; border-radius:10px; color:#0f172a; text-decoration:none; display:inline-block }

        .footer-small{ margin-top:14px; font-size:13px; color:#64748b; text-align:center }

        @media(max-width:520px){ body{ padding:20px } .login-card{ padding:18px } .login-card .brand img{ width:140px } }
    </style>
</head>
<body class="login-page">
<div class="forum-app">
    <div class="sidebar-card login-card">
        <div class="brand">
            <img src="<?php echo base_url('assets/images/brks-logo.png'); ?>" alt="BRKS">
        </div>
        <h2>Daftar Akun</h2>
        <?php if (!empty($error)): ?>
            <div class="error-msg"><?php echo htmlentities($error); ?></div>
        <?php endif; ?>
        <form method="post" action="<?php echo site_url('auth/register_submit'); ?>">
            <div class="form-field">
                <label for="fullname">Nama Lengkap</label>
                <input id="fullname" name="fullname" type="text" placeholder="Nama lengkap">
            </div>

            <div class="form-field">
                <label for="username">Username</label>
                <input id="username" name="username" type="text" placeholder="Username">
            </div>

            <div class="form-field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" placeholder="Password">
            </div>

            <div class="form-field">
                <label for="repassword">Ulangi Password</label>
                <input id="repassword" name="repassword" type="password" placeholder="Ulangi password">
            </div>

            <div class="actions">
                <a href="<?php echo site_url('auth/login'); ?>" class="btn">Masuk</a>
                <button type="submit" class="btn primary">Daftar</button>
            </div>
        </form>

        <div class="footer-small">Silahkan Login setelah mendaftar</div>
    </div>
</div>
</body>
</html>
