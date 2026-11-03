<?php
declare(strict_types=1);
require_once __DIR__.'/../config/bootstrap.php';
requireAdmin();

if(empty($_SESSION['message_csrf'])) $_SESSION['message_csrf']=bin2hex(random_bytes(32));
$csrf=$_SESSION['message_csrf'];
$errors=[];

if($_SERVER['REQUEST_METHOD']==='POST'){
  $postedCsrf=$_POST['csrf']??null;
  $messageInput=$_POST['message']??'';
  $message=is_string($messageInput)?trim($messageInput):'';
  $messageId=filter_var($_POST['message_id']??null,FILTER_VALIDATE_INT);
  if(!is_string($postedCsrf)||!hash_equals($csrf,$postedCsrf)) $errors[]='Your session expired. Refresh the page and try again.';
  if(!$messageId||strlen($message)<1||strlen($message)>5000) $errors[]='Enter a reply of no more than 5,000 characters.';
  if(!$errors){
    $check=db()->prepare('SELECT id FROM contact_messages WHERE id=?');
    $check->execute([$messageId]);
    if(!$check->fetch()) $errors[]='That conversation could not be found.';
    else{
      db()->prepare("INSERT INTO contact_message_replies(contact_message_id,sender_type,sender_admin_id,message) VALUES(?,'admin',?,?)")
        ->execute([$messageId,(int)$_SESSION['admin_id'],$message]);
      redirect('messages.php#conversation-'.$messageId);
    }
  }
}

$msgs=db()->query('SELECT * FROM contact_messages ORDER BY created_at DESC,id DESC')->fetchAll();
db()->query('UPDATE contact_messages SET is_read=1');
$repliesByMessage=[];
$statement=db()->query("SELECT r.*,a.username AS admin_name,m.full_name AS member_name FROM contact_message_replies r LEFT JOIN admin_users a ON a.id=r.sender_admin_id LEFT JOIN members m ON m.id=r.sender_member_id ORDER BY r.created_at,r.id");
foreach($statement->fetchAll() as $reply) $repliesByMessage[(int)$reply['contact_message_id']][]=$reply;

$pageTitle='Messages'; $activePage='msg'; include '_layout.php';
?>
<div class="ac">
  <div class="ach"><div class="act">Contact Conversations</div><div style="font-size:.72rem;color:var(--muted)"><?=count($msgs)?> conversations</div></div>
  <div style="padding:1rem;display:grid;gap:1rem">
    <?php if($errors): ?><div role="alert" style="padding:.8rem 1rem;border-radius:var(--r);background:rgba(192,57,43,.07);color:var(--red);font-size:.84rem"><?php foreach($errors as $error): ?><div><?=e($error)?></div><?php endforeach; ?></div><?php endif; ?>
    <?php if(!$msgs): ?><p style="text-align:center;padding:2rem;color:var(--muted)">No messages yet.</p>
    <?php else: foreach($msgs as $m): $messageId=(int)$m['id']; ?>
    <article id="conversation-<?=$messageId?>" style="border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden">
      <header style="padding:.9rem 1rem;background:var(--warm-grey);display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap">
        <div><strong style="color:var(--ink)"><?=e($m['name'])?></strong> <a href="mailto:<?=e($m['email'])?>" style="color:var(--sage);font-size:.82rem"><?=e($m['email'])?></a><?php if(!(int)$m['is_read']): ?><span class="badge b-gold" style="margin-left:.35rem">New</span><?php endif; ?><div style="font-size:.76rem;color:var(--muted);margin-top:.2rem"><?=e($m['subject']?:'General inquiry')?><?=!empty($m['member_id'])?' · Member':''?></div></div>
        <time style="font-size:.7rem;color:var(--dim);white-space:nowrap"><?=e(date('M j, Y g:i A',strtotime($m['created_at'])))?></time>
      </header>
      <div style="padding:1rem">
        <div style="padding:.75rem .9rem;background:#F7F6F1;border-radius:8px;margin-bottom:.75rem">
          <strong style="font-size:.75rem;color:var(--forest)">Original message</strong>
          <div style="font-size:.84rem;line-height:1.6;color:var(--ink);margin-top:.3rem;white-space:pre-wrap"><?=e($m['message'])?></div>
        </div>
        <?php foreach($repliesByMessage[$messageId]??[] as $reply): ?>
        <div style="padding:.75rem .9rem;background:<?=$reply['sender_type']==='admin'?'rgba(212,168,67,.10)':'rgba(7,59,46,.06)'?>;border-radius:8px;margin:.55rem 0 0">
          <div style="display:flex;justify-content:space-between;gap:.75rem"><strong style="font-size:.75rem;color:var(--forest)"><?=$reply['sender_type']==='admin'?'AJira Admin':e($reply['member_name']??$m['name'])?></strong><time style="font-size:.68rem;color:var(--dim)"><?=e(date('M j, Y g:i A',strtotime($reply['created_at'])))?></time></div>
          <div style="font-size:.84rem;line-height:1.6;color:var(--ink);margin-top:.3rem;white-space:pre-wrap"><?=e($reply['message'])?></div>
        </div>
        <?php endforeach; ?>
        <form method="POST" style="display:grid;gap:.55rem;margin-top:.85rem">
          <input type="hidden" name="csrf" value="<?=e($csrf)?>">
          <input type="hidden" name="message_id" value="<?=$messageId?>">
          <label for="reply-<?=$messageId?>" style="font-size:.75rem;font-weight:600;color:var(--ink)">Reply to <?=e($m['name'])?></label>
          <textarea id="reply-<?=$messageId?>" name="message" rows="3" maxlength="5000" required class="ai" placeholder="Write your reply..."></textarea>
          <button type="submit" class="btn btn-forest btn-sm" style="justify-self:start"><i class="bi bi-reply"></i> Send reply</button>
        </form>
      </div>
    </article>
    <?php endforeach; endif; ?>
  </div>
</div>
<?php include '_layout_end.php'; ?>
