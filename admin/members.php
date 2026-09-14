<?php
declare(strict_types=1);
$pageTitle='Members'; $activePage='mem';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $a=$_POST['action']??''; $id=(int)($_POST['id']??0);
  try{
    $ms=db()->prepare('SELECT id,full_name FROM members WHERE id=?'); $ms->execute([$id]); $member=$ms->fetch();
    $expiry=$_POST['expires_at']??''; $expiryDate=DateTime::createFromFormat('Y-m-d',$expiry);
    if(($a==='activate'||$a==='renew')&&(!$expiryDate||$expiryDate->format('Y-m-d')!==$expiry)) throw new RuntimeException('Invalid expiry date.');
    if($a==='activate'||$a==='renew') { $until=$expiry.' 23:59:59'; db()->prepare("UPDATE members SET is_active=1,expires_at=?,deactivated_at=NULL,deactivation_reason=NULL,activated_at=NOW() WHERE id=?")->execute([$until,$id]); if($member) memberNotification($id,$a==='renew'?'Membership renewed':'Membership activated','Your membership is active until '.date('F j, Y',strtotime($until)).'.'); }
    if($a==='deactivate') { db()->prepare("UPDATE members SET is_active=0,deactivated_at=NOW(),deactivation_reason=? WHERE id=?")->execute([trim($_POST['reason']??'Admin action'),$id]); if($member) memberNotification($id,'Membership deactivated','Your membership has been deactivated by the administrator. Please contact the club for more information.'); }
    if($a==='delete')     db()->prepare("DELETE FROM members WHERE id=?")->execute([$id]);
  }catch(Throwable $e){ error_log($e->getMessage()); }
  header('Location: members.php?'.http_build_query(array_filter(['q'=>$_POST['q']??'','f'=>$_POST['f']??'']))); exit;
}
include '_layout.php';
$q=trim($_GET['q']??''); $f=$_GET['f']??'all'; $page=max(1,(int)($_GET['page']??1)); $per=15;
$w='WHERE 1=1'; $pa=[];
if($q){ $w.=' AND (full_name LIKE ? OR reg_number LIKE ? OR phone LIKE ?)'; $pa=array_fill(0,3,"%$q%"); }
if($f==='active')  $w.=' AND is_active=1 AND (expires_at IS NULL OR expires_at>=NOW())';
if($f==='pending') $w.=' AND is_active=0';
if($f==='expired') $w.=' AND is_active=1 AND expires_at<NOW()';
try{
  $cs=db()->prepare("SELECT COUNT(*) FROM members $w"); $cs->execute($pa); $total=(int)$cs->fetchColumn();
  $pages=max(1,(int)ceil($total/$per)); $off=($page-1)*$per;
  $st=db()->prepare("SELECT * FROM members $w ORDER BY registered_at DESC LIMIT $per OFFSET $off"); $st->execute($pa); $mems=$st->fetchAll();
  $cnt=db()->query("SELECT SUM(1) t,SUM(is_active) a,SUM(1-is_active) p FROM members")->fetch();
}catch(Throwable){ $mems=[]; $total=0; $pages=1; $cnt=['t'=>0,'a'=>0,'p'=>0]; }
?>
<div style="display:flex;flex-wrap:wrap;gap:.55rem;justify-content:space-between;align-items:center;margin-bottom:1rem">
  <div style="display:flex;gap:.4rem;flex-wrap:wrap">
    <?php foreach(['all'=>'All ('.($cnt['t']??0).')','active'=>'Active','pending'=>'Pending','expired'=>'Expired'] as $fv=>$fl): ?>
    <a href="?f=<?=$fv?>&q=<?=urlencode($q)?>" class="btn btn-sm <?=$f===$fv?'btn-forest':'btn-outline'?>"><?=$fl?></a>
    <?php endforeach; ?>
  </div>
  <form method="GET" style="display:flex;gap:.4rem">
    <input type="hidden" name="f" value="<?=e($f)?>">
    <input type="text" name="q" class="ai" placeholder="Search…" value="<?=e($q)?>" style="width:160px">
    <button type="submit" class="btn btn-forest btn-sm"><i class="bi bi-search"></i></button>
    <?php if($q): ?><a href="?f=<?=e($f)?>" class="btn btn-outline btn-sm">✕</a><?php endif; ?>
  </form>
</div>
<div style="display:flex;justify-content:flex-end;gap:.45rem;flex-wrap:wrap;margin-bottom:1rem">
  <a href="export.php?type=members_excel" class="btn btn-outline btn-sm"><i class="bi bi-file-earmark-spreadsheet"></i> Export Excel</a>
  <a href="report.php" target="_blank" class="btn btn-forest btn-sm"><i class="bi bi-file-earmark-pdf"></i> Print / Save PDF</a>
</div>
<div class="ac">
  <div class="ach"><div class="act">Members<?=$q?' — "'.e($q).'"':''?></div><div style="font-size:.72rem;color:var(--muted)"><?=$total?> records</div></div>
  <div style="overflow-x:auto">
    <table class="at">
      <thead><tr><th>Name</th><th>Reg No.</th><th>Phone</th><th>Course/Year</th><th>Type</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
      <tbody>
        <?php if(empty($mems)): ?><tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--muted)"><?=$q?'No results.':'No members yet.'?></td></tr>
        <?php else: foreach($mems as $m): ?>
        <tr>
          <td><div style="font-weight:600;color:var(--ink)"><?=e($m['full_name'])?></div><div style="font-size:.7rem;color:var(--dim)"><?=e($m['email'])?></div></td>
          <td><code style="background:var(--warm-grey);padding:.18rem .42rem;border-radius:5px;font-size:.74rem;font-weight:700"><?=e($m['reg_number'])?></code></td>
          <td style="font-size:.8rem"><?=e($m['phone'])?></td>
          <td style="font-size:.78rem;color:var(--muted)"><?=e($m['course_year'])?></td>
          <td><span class="badge b-stone"><?=$m['member_type']==='new'?'New':'Renewal'?></span></td>
          <?php $expired=$m['is_active']&&!empty($m['expires_at'])&&strtotime($m['expires_at'])<time(); $status=$expired?'Expired':($m['is_active']?'Active':'Pending'); ?>
          <td><span class="badge <?=$status==='Active'?'b-green':($status==='Expired'?'b-stone':'b-gold')?>"><?=$status?></span><?php if(!empty($m['expires_at'])): ?><div style="font-size:.66rem;color:var(--dim);margin-top:.2rem">Until <?=date('M j Y',strtotime($m['expires_at']))?></div><?php endif; ?></td>
          <td style="font-size:.72rem;color:var(--dim);white-space:nowrap"><?=date('M j Y',strtotime($m['registered_at']))?></td>
          <td>
            <div style="display:flex;gap:.28rem;flex-wrap:wrap">
              <?php if(!$m['is_active']||$expired): ?>
              <form method="POST" style="display:flex;gap:.25rem;align-items:center"><input type="hidden" name="action" value="<?=$expired?'renew':'activate'?>"><input type="hidden" name="id" value="<?=$m['id']?>"><input type="hidden" name="q" value="<?=e($q)?>"><input type="hidden" name="f" value="<?=e($f)?>"><input type="date" name="expires_at" value="<?=date('Y-m-d',strtotime('+1 year'))?>" class="ai" style="font-size:.7rem;padding:.3rem" required>
                <button type="submit" class="btn btn-forest btn-sm" style="font-size:.72rem;padding:.34rem .75rem"><?=$expired?'Renew':'Activate'?></button>
              </form>
              <?php else: ?>
              <form method="POST" style="display:flex;gap:.25rem;align-items:center"><input type="hidden" name="action" value="renew"><input type="hidden" name="id" value="<?=$m['id']?>"><input type="hidden" name="q" value="<?=e($q)?>"><input type="hidden" name="f" value="<?=e($f)?>"><input type="date" name="expires_at" value="<?=date('Y-m-d',strtotime('+1 year'))?>" class="ai" style="font-size:.7rem;padding:.3rem" required><button type="submit" class="btn btn-forest btn-sm" style="font-size:.72rem;padding:.34rem .75rem">Renew</button></form>
              <form method="POST" style="display:inline"><input type="hidden" name="action" value="deactivate"><input type="hidden" name="id" value="<?=$m['id']?>"><input type="hidden" name="q" value="<?=e($q)?>"><input type="hidden" name="f" value="<?=e($f)?>">
                <button type="submit" class="btn btn-outline btn-sm" style="font-size:.72rem;padding:.34rem .75rem">Deactivate</button>
              </form>
              <?php endif; ?>
              <form method="POST" style="display:inline" onsubmit="return confirm('Delete permanently?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$m['id']?>"><input type="hidden" name="q" value="<?=e($q)?>"><input type="hidden" name="f" value="<?=e($f)?>">
                <button type="submit" class="btn btn-sm" style="background:rgba(192,57,43,.07);color:var(--red);border:1px solid rgba(192,57,43,.15);font-size:.72rem;padding:.34rem .7rem"><i class="bi bi-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
  <?php if($pages>1): ?>
  <div style="padding:.75rem 1.1rem;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border);flex-wrap:wrap;gap:.4rem">
    <div style="font-size:.72rem;color:var(--muted)">Page <?=$page?> of <?=$pages?></div>
    <div style="display:flex;gap:.28rem">
      <?php for($p=1;$p<=$pages;$p++): ?><a href="?page=<?=$p?>&f=<?=e($f)?>&q=<?=urlencode($q)?>" class="btn btn-sm <?=$p===$page?'btn-forest':'btn-outline'?>"><?=$p?></a><?php endfor; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
<?php include '_layout_end.php'; ?>
