<?php declare(strict_types=1); $pageTitle='Settings'; $activePage='set'; include '_layout.php';
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $cur=$_POST['cp']??''; $new=$_POST['np']??''; $cnf=$_POST['cnf']??'';
  if(strlen($new)<8){ $msg='err:Password must be at least 8 characters.'; }
  elseif($new!==$cnf){ $msg='err:Passwords do not match.'; }
  else{
    try{
      $s=db()->prepare("SELECT password_hash FROM admin_users WHERE id=?"); $s->execute([$_SESSION['admin_id']]); $r=$s->fetch();
      if($r&&password_verify($cur,$r['password_hash'])){ db()->prepare("UPDATE admin_users SET password_hash=? WHERE id=?")->execute([password_hash($new,PASSWORD_BCRYPT),$_SESSION['admin_id']]); $msg='ok:Password updated.'; }
      else { $msg='err:Current password is incorrect.'; }
    }catch(Throwable){ $msg='err:Update failed.'; }
  }
}
try{ $cnt=db()->query("SELECT(SELECT COUNT(*)FROM members)m,(SELECT COUNT(*)FROM members WHERE is_active=1)a,(SELECT COUNT(*)FROM events)e,(SELECT COUNT(*)FROM contact_messages)c")->fetch(); }catch(Throwable){ $cnt=[]; }
[$mt,$mv]=explode(':',$msg?:'ok:',2);
?>
<div style="display:grid;grid-template-columns:1fr;gap:1.1rem">
  <div class="ac">
    <div class="ach"><div class="act">Change Password</div></div>
    <div style="padding:1rem 1.1rem">
      <?php if($msg): ?>
      <div style="background:<?=$mt==='ok'?'var(--warm-grey)':'rgba(192,57,43,.06)'?>;border:1px solid <?=$mt==='ok'?'var(--border)':'rgba(192,57,43,.15)'?>;border-radius:var(--r);padding:.75rem .95rem;color:<?=$mt==='ok'?'var(--ink)':'var(--red)'?>;font-size:.84rem;font-weight:600;margin-bottom:1rem"><?=e($mv)?></div>
      <?php endif; ?>
      <form method="POST" style="display:flex;flex-direction:column;gap:.82rem">
        <div><label class="f-label">Current Password</label><input type="password" name="cp" class="ai" style="width:100%" required></div>
        <div><label class="f-label">New Password (min 8)</label><input type="password" name="np" class="ai" style="width:100%" required minlength="8"></div>
        <div><label class="f-label">Confirm New Password</label><input type="password" name="cnf" class="ai" style="width:100%" required></div>
        <button type="submit" class="btn btn-forest btn-sm" style="width:fit-content"><i class="bi bi-shield-lock"></i> Update Password</button>
      </form>
    </div>
  </div>
  <div class="ac">
    <div class="ach"><div class="act">Database Stats</div></div>
    <div style="padding:1rem 1.1rem;display:flex;flex-direction:column;gap:.6rem">
      <?php foreach([['Members',$cnt['m']??0],['Active Members',$cnt['a']??0],['Events',$cnt['e']??0],['Messages',$cnt['c']??0]] as [$l,$v]): ?>
      <div style="display:flex;justify-content:space-between;align-items:center;padding:.65rem .9rem;background:var(--warm-grey);border-radius:var(--r);border:1px solid var(--border)">
        <span style="font-size:.84rem;font-weight:600;color:var(--ink)"><?=$l?></span>
        <span style="font-family:var(--font-display);font-size:1.2rem;font-weight:300;color:var(--ink)"><?=$v?></span>
      </div>
      <?php endforeach; ?>
      <div style="display:flex;gap:.5rem;margin-top:.4rem;flex-wrap:wrap">
        <a href="export.php?type=members" class="btn btn-outline btn-sm"><i class="bi bi-download"></i> Members CSV</a>
        <a href="export.php?type=messages" class="btn btn-outline btn-sm"><i class="bi bi-download"></i> Messages CSV</a>
      </div>
    </div>
  </div>
</div>
<?php include '_layout_end.php'; ?>
