<?php
declare(strict_types=1);
require_once __DIR__.'/config/bootstrap.php';
if(isMember()) redirect('member_profile.php');
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $rn=strtoupper(trim($_POST['reg_number']??'')); $pw=$_POST['password']??'';
  $s=db()->prepare('SELECT id,password_hash FROM members WHERE reg_number=? LIMIT 1'); $s->execute([$rn]); $m=$s->fetch();
  if($m&&$m['password_hash']&&password_verify($pw,$m['password_hash'])){ session_regenerate_id(true); $_SESSION['member_id']=(int)$m['id']; redirect('member_profile.php'); }
  $error='Invalid registration number or password.';
}
$pageTitle='Member Sign In'; include '_head.php';
?>
<section class="member-login-page"><div class="wrap" style="max-width:430px">
  <div class="card member-login-card" style="padding:1.75rem">
    <div class="pg-label"><i class="bi bi-person-circle"></i> Member portal</div>
    <h1 style="font-family:var(--font-display);font-weight:300;font-size:2rem;margin:.4rem 0 1.5rem">Sign in to your <em>profile</em></h1>
    <?php if($error): ?><div style="color:var(--red);background:rgba(192,57,43,.06);padding:.75rem;border-radius:var(--r);font-size:.84rem;margin-bottom:1rem"><?=e($error)?></div><?php endif; ?>
    <form method="POST" style="display:flex;flex-direction:column;gap:.85rem">
      <div><label class="f-label">Registration Number</label><input class="f-input" name="reg_number" required autocomplete="username" placeholder="BCT/2021/003"></div>
      <div><label class="f-label">Password</label><input class="f-input" type="password" name="password" required autocomplete="current-password"></div>
      <button class="btn btn-forest btn-full" type="submit">Sign In</button>
    </form>
    <p style="font-size:.8rem;color:var(--muted);text-align:center;margin-top:1rem">New member? <a href="join.php" style="color:var(--forest);font-weight:700">Sign up</a></p>
  </div>
</div></section>
<?php include '_footer.php'; ?>