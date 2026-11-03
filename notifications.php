<?php
declare(strict_types=1);
require_once __DIR__.'/config/bootstrap.php';
$items=[];
try{
  $where=isMember()?"audience IN ('public','members')":"audience='public'";
  $items=db()->query("SELECT * FROM notifications WHERE $where AND (expires_at IS NULL OR expires_at>NOW()) ORDER BY created_at DESC LIMIT 40")->fetchAll();
  if(isMember()){
    $read=db()->prepare('INSERT IGNORE INTO notification_reads(notification_id,member_id) VALUES(?,?)');
    foreach($items as $item) $read->execute([(int)$item['id'],(int)$_SESSION['member_id']]);
  }else{
    $viewed=array_map('intval',$_SESSION['viewed_notifications']??[]);
    $_SESSION['viewed_notifications']=array_values(array_unique(array_merge($viewed,array_map(static fn(array $item):int=>(int)$item['id'],$items))));
  }
}catch(Throwable){}
$pageTitle='Notifications'; include '_head.php';
?>
<div class="page-hero"><div class="wrap page-hero-inner"><nav class="breadcrumb" aria-label="Breadcrumb"><ol><li><a href="index.php"><i class="bi bi-house-door-fill" aria-hidden="true"></i>Home</a></li><li class="breadcrumb-separator" aria-hidden="true"><i class="bi bi-chevron-right"></i></li><li aria-current="page">Notifications</li></ol></nav><div class="pg-label"><i class="bi bi-bell"></i> Updates</div><h1>Club <em>notifications</em></h1><p>Announcements, opportunities, and important updates from AJira Digital Club.</p></div></div>
<section class="notifications-page"><div class="wrap" style="max-width:780px">
<?php if(!$items): ?><div class="notification-empty"><i class="bi bi-bell-slash" style="display:block;margin-bottom:.6rem;font-size:1.5rem;color:var(--sage)"></i>No notifications yet.</div><?php else: foreach($items as $item): ?><article class="notification-card"><div style="display:flex;align-items:start;justify-content:space-between;gap:1rem"><div><span class="badge <?=$item['audience']==='members'?'b-green':'b-gold'?>"><?=$item['audience']==='members'?'Members':'Everyone'?></span><h2 style="font-family:var(--font-display);font-size:1.2rem;font-weight:300;color:var(--ink);margin:.55rem 0 .35rem"><?=e($item['title'])?></h2><p style="font-size:.86rem;color:var(--muted);line-height:1.7;margin:0"><?=nl2br(e($item['message']))?></p></div><i class="bi bi-megaphone" style="color:var(--sage);font-size:1.1rem;flex-shrink:0"></i></div><?php if(!empty($item['link'])): ?><a href="<?=e($item['link'])?>" class="btn btn-outline btn-sm" style="margin-top:.8rem">Open link <i class="bi bi-arrow-up-right"></i></a><?php endif; ?><div style="font-size:.68rem;color:var(--dim);margin-top:.75rem"><?=e(date('M j, Y g:i A',strtotime($item['created_at'])))?></div></article><?php endforeach; endif; ?>
</div></section>
<?php include '_footer.php'; ?>