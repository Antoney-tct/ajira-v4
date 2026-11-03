<?php
declare(strict_types=1);
require_once __DIR__.'/config/bootstrap.php';
requireMember();

$memberId=(int)$_SESSION['member_id'];
$memberStatement=db()->prepare('SELECT full_name,email FROM members WHERE id=? LIMIT 1');
$memberStatement->execute([$memberId]);
$member=$memberStatement->fetch();
if(!$member){unset($_SESSION['member_id']);redirect('member_login.php');}

if(empty($_SESSION['member_message_csrf'])) $_SESSION['member_message_csrf']=bin2hex(random_bytes(32));
$csrf=$_SESSION['member_message_csrf'];
$errors=[];

db()->prepare('UPDATE contact_messages SET member_id=? WHERE member_id IS NULL AND email=? AND (SELECT COUNT(*) FROM members WHERE email=?)=1')
  ->execute([$memberId,$member['email'],$member['email']]);

if($_SERVER['REQUEST_METHOD']==='POST'){
  $postedCsrf=$_POST['csrf']??null;
  $messageInput=$_POST['message']??'';
  $message=is_string($messageInput)?trim($messageInput):'';
  $messageId=filter_var($_POST['message_id']??null,FILTER_VALIDATE_INT);
  if(!is_string($postedCsrf)||!hash_equals($csrf,$postedCsrf)) $errors[]='Your session expired. Refresh the page and try again.';
  if(!$messageId||strlen($message)<1||strlen($message)>5000) $errors[]='Enter a reply of no more than 5,000 characters.';
  if(!$errors){
    $check=db()->prepare('SELECT id FROM contact_messages WHERE id=? AND member_id=?');
    $check->execute([$messageId,$memberId]);
    if(!$check->fetch()) $errors[]='That conversation could not be found.';
    else{
      $statement=db()->prepare("INSERT INTO contact_message_replies(contact_message_id,sender_type,sender_member_id,message) VALUES(?,'member',?,?)");
      $statement->execute([$messageId,$memberId,$message]);
      db()->prepare('UPDATE contact_messages SET is_read=0 WHERE id=?')->execute([$messageId]);
      redirect('member_messages.php#conversation-'.$messageId);
    }
  }
}

$statement=db()->prepare('SELECT * FROM contact_messages WHERE member_id=? ORDER BY created_at DESC,id DESC');
$statement->execute([$memberId]);
$conversations=$statement->fetchAll();
$repliesByMessage=[];
$statement=db()->prepare("SELECT r.*,a.username AS admin_name FROM contact_message_replies r LEFT JOIN admin_users a ON a.id=r.sender_admin_id WHERE r.contact_message_id IN (SELECT id FROM contact_messages WHERE member_id=?) ORDER BY r.created_at,r.id");
$statement->execute([$memberId]);
foreach($statement->fetchAll() as $reply) $repliesByMessage[(int)$reply['contact_message_id']][]=$reply;

$pageTitle='My Messages'; include '_head.php';
?>
<section class="member-profile-page"><div class="wrap" style="max-width:820px">
  <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap">
    <div><div class="pg-label"><i class="bi bi-chat-dots"></i> Member portal</div><h1 style="font-family:var(--font-display);font-weight:300;font-size:2rem;margin:.35rem 0 0">My <em>messages</em></h1></div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap"><a class="btn btn-forest btn-sm" href="contact.php"><i class="bi bi-plus-lg"></i> New conversation</a><a class="btn btn-outline btn-sm" href="member_profile.php"><i class="bi bi-person-circle"></i> My profile</a></div>
  </div>
  <?php if($errors): ?><div role="alert" style="background:rgba(192,57,43,.06);padding:.8rem 1rem;border-radius:var(--r);margin-bottom:1rem;color:var(--red);font-size:.84rem"><?php foreach($errors as $error): ?><div><?=e($error)?></div><?php endforeach; ?></div><?php endif; ?>
  <?php if(!$conversations): ?>
    <div class="card" style="padding:1.5rem"><p style="color:var(--muted);font-size:.88rem;margin:0">You have no conversations yet.</p><a class="btn btn-forest btn-sm" href="contact.php" style="margin-top:1rem"><i class="bi bi-send"></i> Start a conversation</a></div>
  <?php else: foreach($conversations as $conversation): $messageId=(int)$conversation['id']; ?>
    <article id="conversation-<?=$messageId?>" class="card" style="padding:1.15rem;margin-bottom:1rem">
      <header style="display:flex;justify-content:space-between;align-items:flex-start;gap:.75rem;flex-wrap:wrap;margin-bottom:.85rem">
        <div><h2 style="font-family:var(--font-display);font-size:1.15rem;font-weight:400;color:var(--ink);margin:0"><?=e($conversation['subject']?:'General inquiry')?></h2><div style="font-size:.7rem;color:var(--dim);margin-top:.25rem">Started <?=e(date('M j, Y g:i A',strtotime($conversation['created_at'])))?></div></div>
      </header>
      <div style="padding:.75rem .9rem;background:#F7F6F1;border-radius:8px">
        <strong style="font-size:.75rem;color:var(--forest)">You</strong>
        <div style="font-size:.84rem;line-height:1.6;color:var(--ink);margin-top:.3rem;white-space:pre-wrap"><?=e($conversation['message'])?></div>
      </div>
      <?php foreach($repliesByMessage[$messageId]??[] as $reply): ?>
      <div style="padding:.75rem .9rem;background:<?=$reply['sender_type']==='admin'?'rgba(212,168,67,.10)':'rgba(7,59,46,.06)'?>;border-radius:8px;margin-top:.6rem">
        <div style="display:flex;justify-content:space-between;gap:.75rem"><strong style="font-size:.75rem;color:var(--forest)"><?=$reply['sender_type']==='admin'?'AJira Admin':'You'?></strong><time style="font-size:.68rem;color:var(--dim)"><?=e(date('M j, Y g:i A',strtotime($reply['created_at'])))?></time></div>
        <div style="font-size:.84rem;line-height:1.6;color:var(--ink);margin-top:.3rem;white-space:pre-wrap"><?=e($reply['message'])?></div>
      </div>
      <?php endforeach; ?>
      <form method="POST" style="display:grid;gap:.55rem;margin-top:.85rem">
        <input type="hidden" name="csrf" value="<?=e($csrf)?>">
        <input type="hidden" name="message_id" value="<?=$messageId?>">
        <label for="reply-<?=$messageId?>" style="font-size:.75rem;font-weight:600;color:var(--ink)">Reply</label>
        <textarea id="reply-<?=$messageId?>" name="message" rows="3" maxlength="5000" required class="f-input" placeholder="Write your reply..."></textarea>
        <button type="submit" class="btn btn-forest btn-sm" style="justify-self:start"><i class="bi bi-reply"></i> Send reply</button>
      </form>
    </article>
  <?php endforeach; endif; ?>
</div></section>
<?php include '_footer.php'; ?>
