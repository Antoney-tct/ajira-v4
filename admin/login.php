<?php
declare(strict_types=1);
require_once __DIR__.'/../config/bootstrap.php';
if(isAdmin()) redirect('index.php');
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $u=trim($_POST['username']??''); $p=$_POST['password']??'';
  if(!$u||!$p){ $error='Enter username and password.'; }
  else{
    $s=db()->prepare('SELECT id,username,password_hash FROM admin_users WHERE username=? LIMIT 1');
    $s->execute([$u]); $a=$s->fetch();
    if($a&&password_verify($p,$a['password_hash'])){
      $_SESSION['admin_id']=$a['id']; $_SESSION['admin_user']=$a['username'];
      db()->prepare('UPDATE admin_users SET last_login=NOW() WHERE id=?')->execute([$a['id']]);
      redirect('index.php');
    } else { $error='Invalid username or password.'; }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="theme-color" content="#073B2E">
<title>Admin — AJira Digital Club</title>
<link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="icon" type="image/svg+xml" href="../assets/images/favicon.svg">
<link rel="stylesheet" href="../assets/css/main.css">
<style>
body{background:var(--forest);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1.5rem}
body::before,body::after{display:none}
</style>
</head>
<body>
<div style="width:100%;max-width:380px">
  <div style="text-align:center;margin-bottom:2rem">
    <div style="width:48px;height:48px;background:rgba(212,168,67,.15);border:1px solid rgba(212,168,67,.25);border-radius:12px;display:flex;align-items:center;justify-content:center;font-family:var(--font-display);font-size:1rem;color:var(--gold);margin:0 auto 1rem;position:relative;overflow:hidden">
      <div style="position:absolute;top:0;left:0;right:0;height:2px;background:var(--gold)"></div>AD
    </div>
    <div style="font-family:var(--font-display);font-size:1.05rem;font-weight:300;color:white">AJira Digital Club</div>
    <div style="font-size:.62rem;color:rgba(255,255,255,.3);font-weight:600;text-transform:uppercase;letter-spacing:.1em;margin-top:.25rem">Admin · Zetech University</div>
  </div>

  <div style="background:var(--cream);border-radius:var(--r-xl);padding:2rem 1.75rem;box-shadow:var(--sh-xl)">
    <div style="font-family:var(--font-display);font-size:1.3rem;font-weight:300;color:var(--ink);margin-bottom:1.5rem">Sign in</div>

    <?php if($error): ?>
    <div style="background:rgba(192,57,43,.06);border:1px solid rgba(192,57,43,.15);border-radius:var(--r);padding:.75rem .95rem;color:var(--red);font-size:.84rem;font-weight:600;margin-bottom:1.1rem">
      <?=e($error)?>
    </div>
    <?php endif; ?>

    <form method="POST" style="display:flex;flex-direction:column;gap:.85rem">
      <div>
        <label class="f-label">Username</label>
        <input type="text" name="username" class="f-input" value="<?=e($_POST['username']??'')?>" autocomplete="username" required autofocus>
      </div>
      <div>
        <label class="f-label">Password</label>
        <div style="position:relative">
          <input type="password" name="password" class="f-input" autocomplete="current-password" required id="pwf" style="padding-right:2.5rem">
          <button type="button" onclick="const f=document.getElementById('pwf');f.type=f.type==='password'?'text':'password'" style="position:absolute;right:.8rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--dim);cursor:pointer;font-size:.9rem;padding:0">
            <i class="bi bi-eye-fill"></i>
          </button>
        </div>
      </div>
      <button type="submit" class="btn btn-forest btn-full" style="margin-top:.25rem;padding:.82rem">Sign in to Admin</button>
    </form>
  </div>

  <p style="text-align:center;margin-top:1.25rem">
    <a href="../index.php" style="font-size:.8rem;color:rgba(255,255,255,.3);text-decoration:none">← Back to website</a>
  </p>
</div>
</body>
</html>
