<?php
declare(strict_types=1);
$pageTitle='Dashboard'; $activePage='dash';
include '_layout.php';

$flash='';
if(isset($_GET['activate'])){
  $reg=strtoupper(trim($_GET['activate']));
  try{
    $find=db()->prepare('SELECT id FROM members WHERE reg_number=? LIMIT 1'); $find->execute([$reg]); $memberId=(int)$find->fetchColumn();
    $u=db()->prepare("UPDATE members SET is_active=1,expires_at=DATE_ADD(NOW(),INTERVAL 1 YEAR),deactivated_at=NULL,deactivation_reason=NULL,activated_at=NOW() WHERE reg_number=?"); $u->execute([$reg]);
    if($u->rowCount()&&$memberId) memberNotification($memberId,'Membership activated','Your registration was approved and your AJira Digital Club membership is now active.');
    $flash=$u->rowCount()?"✓ {$reg} activated.":"Member not found.";
  }
  catch(Throwable){ $flash='Error activating member.'; }
}

try{
  $total  =(int)db()->query("SELECT COUNT(*) FROM members")->fetchColumn();
  $active =(int)db()->query("SELECT COUNT(*) FROM members WHERE is_active=1")->fetchColumn();
  $pending=(int)db()->query("SELECT COUNT(*) FROM members WHERE is_active=0")->fetchColumn();
  $msgs   =(int)db()->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn();
  $recent =db()->query("SELECT * FROM members ORDER BY registered_at DESC LIMIT 8")->fetchAll();
  $upev   =db()->query("SELECT * FROM events WHERE event_date>=NOW() AND is_published=1 ORDER BY event_date ASC LIMIT 4")->fetchAll();
}catch(Throwable){ $total=$active=$pending=$msgs=0; $recent=[]; $upev=[]; }
?>

<?php if($flash): ?>
<div style="background:<?=str_starts_with($flash,'✓')?'var(--warm-grey)':'#FEF3C7'?>;border:1px solid <?=str_starts_with($flash,'✓')?'var(--border)':'#FCD34D'?>;border-radius:var(--r);padding:.8rem 1.1rem;margin-bottom:1.1rem;font-weight:600;font-size:.86rem;color:var(--ink)">
  <?=e($flash)?>
</div>
<?php endif; ?>

<!-- Stat chips -->
<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:.75rem;margin-bottom:1.1rem">
  <?php foreach([
    ['Total Members',$total,'var(--forest)'],
    ['Active',       $active,'var(--sage)'],
    ['Pending',      $pending,'#B45309'],
    ['Unread Msgs',  $msgs,'#1877F2'],
  ] as [$l,$v,$c]): ?>
  <div class="sc">
    <div class="sc-bar" style="background:<?=$c?>"></div>
    <div style="font-family:var(--font-display);font-size:2rem;font-weight:300;color:var(--ink);line-height:1;letter-spacing:-.04em"><?=number_format((int)$v)?></div>
    <div style="font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.09em;color:var(--dim);margin-top:.3rem"><?=$l?></div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Quick activate -->
<div class="ac" style="margin-bottom:1.1rem">
  <div class="ach"><div class="act">Quick Activate</div></div>
  <div style="padding:1rem 1.1rem">
    <p style="font-size:.8rem;color:var(--muted);margin-bottom:.85rem">Enter a registration number to activate the member for one year.</p>
    <form method="GET" style="display:flex;gap:.5rem">
      <input type="text" name="activate" class="ai" placeholder="Reg number e.g. BCT/2021/003" style="flex:1;text-transform:uppercase" required>
      <button type="submit" class="btn btn-forest btn-sm">Activate</button>
    </form>
    <p style="font-size:.72rem;color:var(--dim);margin-top:.55rem">Or go to <a href="members.php" style="color:var(--sage);font-weight:600">Members</a> for full management.</p>
  </div>
</div>

<!-- Upcoming events -->
<div class="ac" style="margin-bottom:1.1rem">
  <div class="ach"><div class="act">Upcoming Events</div><a href="events.php" class="btn btn-outline btn-sm">Manage</a></div>
  <?php if(empty($upev)): ?><div style="padding:1.25rem;text-align:center;color:var(--muted);font-size:.82rem">No upcoming events.</div>
  <?php else: foreach($upev as $i=>$ev): ?>
  <div style="padding:.72rem 1.1rem;display:flex;align-items:center;gap:.85rem;<?=$i?'border-top:1px solid var(--border)':''?>">
    <div style="width:38px;height:38px;background:var(--warm-grey);border-radius:var(--r);text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0">
      <div style="font-family:var(--font-display);font-size:1rem;font-weight:300;color:var(--ink);line-height:1"><?=date('j',strtotime($ev['event_date']))?></div>
      <div style="font-size:.52rem;font-weight:600;text-transform:uppercase;color:var(--muted)"><?=date('M',strtotime($ev['event_date']))?></div>
    </div>
    <div style="flex:1;min-width:0">
      <div style="font-weight:600;font-size:.82rem;color:var(--ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?=e($ev['title'])?></div>
      <div style="font-size:.7rem;color:var(--dim)"><?=e($ev['location']??'TBA')?></div>
    </div>
  </div>
  <?php endforeach; endif; ?>
</div>

<!-- Recent registrations -->
<div class="ac">
  <div class="ach"><div class="act">Recent Registrations</div><a href="members.php" class="btn btn-outline btn-sm">View all</a></div>
  <div style="overflow-x:auto">
    <table class="at">
      <thead><tr><th>Name</th><th>Reg No.</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
      <tbody>
        <?php if(empty($recent)): ?>
        <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--muted)">No registrations yet.</td></tr>
        <?php else: foreach($recent as $m): ?>
        <tr>
          <td><div style="font-weight:600;color:var(--ink)"><?=e($m['full_name'])?></div><div style="font-size:.7rem;color:var(--dim)"><?=e($m['phone'])?></div></td>
          <td><code style="background:var(--warm-grey);padding:.18rem .42rem;border-radius:5px;font-size:.74rem;font-weight:700"><?=e($m['reg_number'])?></code></td>
          <td><span class="badge <?=$m['is_active']?'b-green':'b-gold'?>"><?=$m['is_active']?'Active':'Pending'?></span></td>
          <td style="font-size:.72rem;color:var(--dim);white-space:nowrap"><?=date('M j, g:iA',strtotime($m['registered_at']))?></td>
          <td>
            <?php if(!$m['is_active']): ?>
            <a href="?activate=<?=urlencode($m['reg_number'])?>" class="btn btn-forest btn-sm" style="font-size:.74rem;padding:.38rem .85rem" onclick="return confirm('Activate <?=e(addslashes($m['full_name']))?>?')">Activate</a>
            <?php else: ?><span style="color:var(--sage);font-size:.78rem;font-weight:600">✓</span><?php endif; ?>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '_layout_end.php'; ?>
