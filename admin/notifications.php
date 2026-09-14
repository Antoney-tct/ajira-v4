<?php
declare(strict_types=1);
$pageTitle='Notifications'; $activePage='notif';
require_once __DIR__.'/../config/bootstrap.php';
requireAdmin();
$errors=[]; $notice=''; $editing=null;
if(isset($_GET['edit'])){try{$s=db()->prepare('SELECT * FROM notifications WHERE id=?');$s->execute([(int)$_GET['edit']]);$editing=$s->fetch()?:null;}catch(Throwable){}}
if($_SERVER['REQUEST_METHOD']==='POST'){
  $action=$_POST['action']??'create'; $id=(int)($_POST['id']??0); $title=trim($_POST['title']??''); $message=trim($_POST['message']??''); $audience=$_POST['audience']??'public'; $link=trim($_POST['link']??''); $expiresAt=trim($_POST['expires_at']??'');
  if(strlen($title)<3) $errors[]='Enter a notification title.';
  if(strlen($message)<3) $errors[]='Enter a notification message.';
  if(!in_array($audience,['public','members'],true)) $errors[]='Choose a valid audience.';
  if($link!==''&&!filter_var($link,FILTER_VALIDATE_URL)&&!str_starts_with($link,'/')) $errors[]='Use a full URL or a site path such as events.php.';
  $expiryValue=null;
  if($expiresAt!==''){$expiryDate=DateTime::createFromFormat('Y-m-d\TH:i',$expiresAt);if(!$expiryDate||$expiryDate->format('Y-m-d\TH:i')!==$expiresAt)$errors[]='Choose a valid expiry time.';else $expiryValue=$expiryDate->format('Y-m-d H:i:s');}
  if(!$errors){try{if($action==='edit'&&$id){db()->prepare('UPDATE notifications SET title=?,message=?,audience=?,link=?,expires_at=? WHERE id=?')->execute([$title,$message,$audience,$link?:null,$expiryValue,$id]);$notice='Notification updated.';}else{db()->prepare('INSERT INTO notifications(title,message,audience,link,expires_at,created_by) VALUES(?,?,?,?,?,?)')->execute([$title,$message,$audience,$link?:null,$expiryValue,$_SESSION['admin_id']??null]);$notice='Notification published.';}$editing=null;}catch(Throwable $e){error_log($e->getMessage());$errors[]='Notification could not be saved. Run the database migration first.';}}
}
include '_layout.php';
try{$items=db()->query('SELECT n.*,a.username FROM notifications n LEFT JOIN admin_users a ON a.id=n.created_by ORDER BY n.created_at DESC LIMIT 30')->fetchAll();}catch(Throwable){$items=[];}
$formValue=static function(string $field,array $item=[]):string{return e($_POST[$field]??($item[$field]??''));};
?>
<div class="ac" style="margin-bottom:1.1rem">
  <div class="ach"><div class="act"><?=$editing?'Edit notification':'Create notification'?></div><div style="font-size:.72rem;color:var(--muted)">Add an optional link and expiry time.</div></div>
  <div style="padding:1rem 1.1rem">
    <?php if($notice): ?><div style="background:var(--warm-grey);border:1px solid var(--border);padding:.7rem .85rem;border-radius:var(--r);font-size:.82rem;font-weight:600;margin-bottom:1rem"><?=e($notice)?></div><?php endif; ?>
    <?php if($errors): ?><div style="background:rgba(192,57,43,.06);padding:.7rem .85rem;border-radius:var(--r);font-size:.82rem;color:var(--red);margin-bottom:1rem"><?php foreach($errors as $error): ?><div><?=e($error)?></div><?php endforeach; ?></div><?php endif; ?>
    <form method="POST" style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
      <input type="hidden" name="action" value="<?=$editing?'edit':'create'?>"><input type="hidden" name="id" value="<?=$editing?(int)$editing['id']:0?>">
      <div style="grid-column:1/-1"><label class="f-label">Title *</label><input class="ai" style="width:100%" name="title" maxlength="160" required value="<?=$formValue('title',$editing?:[])?>" placeholder="e.g. New workshop announced"></div>
      <div style="grid-column:1/-1"><label class="f-label">Message *</label><textarea class="ai" style="width:100%;resize:vertical" name="message" rows="3" maxlength="2000" required placeholder="Write the announcement..."><?=$formValue('message',$editing?:[])?></textarea></div>
      <div><label class="f-label">Audience *</label><select class="ai" style="width:100%" name="audience"><option value="public" <?=($_POST['audience']??$editing['audience']??'public')==='public'?'selected':''?>>Everyone who visits the site</option><option value="members" <?=($_POST['audience']??$editing['audience']??'')==='members'?'selected':''?>>Registered members only</option></select></div>
      <div><label class="f-label">Optional link</label><input class="ai" style="width:100%" name="link" maxlength="500" value="<?=$formValue('link',$editing?:[])?>" placeholder="events.php or https://..."></div>
      <div><label class="f-label">Disappear after</label><input class="ai" style="width:100%" type="datetime-local" name="expires_at" value="<?=e($_POST['expires_at']??(!empty($editing['expires_at'])?date('Y-m-d\TH:i',strtotime($editing['expires_at'])):''))?>"><div style="font-size:.68rem;color:var(--dim);margin-top:.25rem">Leave blank to keep it visible.</div></div>
      <button class="btn btn-forest btn-sm" type="submit" style="grid-column:1/-1"><i class="bi bi-<?=$editing?'save':'send'?>"></i> <?=$editing?'Save changes':'Publish notification'?></button>
      <?php if($editing): ?><a href="notifications.php" class="btn btn-outline btn-sm" style="grid-column:1/-1;text-align:center">Cancel edit</a><?php endif; ?>
    </form>
  </div>
</div>
<div class="ac"><div class="ach"><div class="act">Published notifications</div><div style="font-size:.72rem;color:var(--muted)"><?=count($items)?> shown</div></div><div style="overflow-x:auto"><table class="at"><thead><tr><th>Title</th><th>Audience</th><th>Link</th><th>Visibility</th><th>Actions</th></tr></thead><tbody>
<?php if(!$items): ?><tr><td colspan="5" style="padding:2rem;text-align:center;color:var(--muted)">No notifications yet.</td></tr><?php else: foreach($items as $item): $expired=!empty($item['expires_at'])&&strtotime($item['expires_at'])<=time(); $visibilityColor=$expired?'var(--red)':'var(--dim)'; ?><tr><td><strong><?=e($item['title'])?></strong><div style="font-size:.72rem;color:var(--muted);margin-top:.2rem"><?=e($item['message'])?></div></td><td><span class="badge <?=$item['audience']==='members'?'b-green':'b-gold'?>"><?=$item['audience']==='members'?'Members':'Everyone'?></span></td><td style="font-size:.75rem"><?=!empty($item['link'])?e($item['link']):'—'?></td><td style="font-size:.72rem;color:<?=$visibilityColor?>"><?=$expired?'Expired':(!empty($item['expires_at'])?'Until '.date('M j, Y g:i A',strtotime($item['expires_at'])):'No expiry')?></td><td><a href="?edit=<?=$item['id']?>" class="btn btn-outline btn-sm"><i class="bi bi-pencil"></i> Edit</a></td></tr><?php endforeach; endif; ?></tbody></table></div></div>
<?php include '_layout_end.php'; ?>