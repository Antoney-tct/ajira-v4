<?php
declare(strict_types=1);
require_once __DIR__.'/config/bootstrap.php';
$sent=!empty($_SESSION['contact_message_sent']); unset($_SESSION['contact_message_sent']); $errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'&&!isMember()) redirect('member_login.php');
if(isMember()){
  if(empty($_SESSION['contact_message_csrf'])) $_SESSION['contact_message_csrf']=bin2hex(random_bytes(32));
  $contactCsrf=$_SESSION['contact_message_csrf'];
  $memberStatement=db()->prepare('SELECT id,full_name,email FROM members WHERE id=? LIMIT 1');
  $memberStatement->execute([(int)$_SESSION['member_id']]);
  $contactMember=$memberStatement->fetch();
  if(!$contactMember){unset($_SESSION['member_id']);redirect('member_login.php');}
}else $contactMember=null;
if($_SERVER['REQUEST_METHOD']==='POST'){
  $postedCsrf=$_POST['csrf']??null;
  if(!is_string($postedCsrf)||!hash_equals($contactCsrf,$postedCsrf)) $errors[]='Your session expired. Refresh the page and try again.';
  $name=$contactMember['full_name']; $email=strtolower($contactMember['email']);
  $subjectInput=$_POST['subject']??''; $messageInput=$_POST['message']??'';
  $subject=is_string($subjectInput)?trim($subjectInput):'';
  $message=is_string($messageInput)?trim($messageInput):'';
  if(strlen($subject)>200) $errors[]='The subject must be 200 characters or fewer.';
  if(strlen($message)<10||strlen($message)>5000) $errors[]='Please write a message between 10 and 5,000 characters.';
  if(empty($errors)){
    try{
      db()->prepare('INSERT INTO contact_messages(member_id,name,email,subject,message) VALUES(?,?,?,?,?)')->execute([(int)$contactMember['id'],$name,$email,$subject,$message]);
      $_SESSION['contact_message_sent']=true;
      redirect('contact.php');
    }
    catch(Throwable $e){ error_log($e->getMessage()); $errors[]='Failed to send. Try again.'; }
  }
}
$pageTitle='Contact Us'; include '_head.php';
?>
<div class="page-hero">
  <div class="wrap page-hero-inner">
    <nav class="breadcrumb" aria-label="Breadcrumb"><ol><li><a href="index.php"><i class="bi bi-house-door-fill" aria-hidden="true"></i>Home</a></li><li class="breadcrumb-separator" aria-hidden="true"><i class="bi bi-chevron-right"></i></li><li aria-current="page">Contact</li></ol></nav>
    <div class="pg-label"><i class="bi bi-chat-dots-fill"></i> We’re listening</div>
    <h1>Let’s start a <em>conversation</em></h1>
    <p>Questions about joining, events, or working with the club? Reach out to the AJira team at either campus.</p>
  </div>
</div>
<section class="contact-page">
  <div class="wrap">
    <div class="contact-layout">
      <aside class="contact-sidebar">
        <div class="contact-heading reveal">
          <div class="label">Come say hello</div>
          <h2 class="t-lg">Two campuses.<br><em>One community.</em></h2>
          <p>We’d love to hear from you. Pick your campus or send us a message and we’ll help you find the right person.</p>
        </div>
        <div class="contact-campus-grid">
          <article class="contact-campus-card reveal">
            <span class="contact-icon"><i class="bi bi-geo-alt-fill"></i></span>
            <div class="label">Ruiru campus</div>
            <h3>Ruiru (TRC)</h3>
            <p>Zetech University<br>Ruiru, Kiambu County</p>
          </article>
          <article class="contact-campus-card reveal d1">
            <span class="contact-icon contact-icon-mangu"><i class="bi bi-geo-alt-fill"></i></span>
            <div class="label">Mangu campus</div>
            <h3>Mangu Campus</h3>
            <p>Zetech University<br>Mangu</p>
          </article>
        </div>
        <a class="contact-email-card reveal" href="mailto:ajiraclub@zetech.ac.ke">
          <span><i class="bi bi-envelope-fill"></i></span>
          <div><small>Email the club</small><strong>ajiraclub@zetech.ac.ke</strong></div>
          <i class="bi bi-arrow-up-right"></i>
        </a>
        <div class="contact-hours reveal"><i class="bi bi-clock"></i><span><strong>Club hours</strong><br>Monday – Friday · 8:00 AM – 6:00 PM</span></div>
      </div>

      <div class="contact-main">
        <?php if($sent): ?>
        <div class="contact-success reveal" role="status">
          <span><i class="bi bi-check2"></i></span>
          <div class="label">Message received</div>
          <h2>Thanks for reaching out.</h2>
          <p>Your message is with our team. We’ll get back to you as soon as we can.</p>
          <a href="member_messages.php" class="btn btn-forest">View message history <i class="bi bi-arrow-right"></i></a>
          <a href="contact.php" class="btn btn-outline">Send another message <i class="bi bi-arrow-right"></i></a>
        </div>
        <?php else: ?>
        <?php if(!$contactMember): ?>
        <div class="contact-form-card reveal">
          <div class="contact-form-heading">
            <span><i class="bi bi-person-lock"></i></span>
            <div><div class="label">Member conversations</div><h2>Sign in to message us</h2><p>Sign in to start a conversation and follow the full reply history from your member portal.</p></div>
          </div>
          <a href="member_login.php" class="btn btn-forest contact-submit"><i class="bi bi-box-arrow-in-right"></i> Member sign in <i class="bi bi-arrow-right"></i></a>
          <p style="font-size:.82rem;color:var(--muted);margin-top:1rem">Not a member yet? <a href="join.php" style="color:var(--forest);font-weight:700">Create your member profile</a>.</p>
        </div>
        <?php else: ?>
        <div class="contact-form-card reveal">
          <div class="contact-form-heading">
            <span><i class="bi bi-send-fill"></i></span>
            <div><div class="label">Drop us a note</div><h2>Send a message</h2><p>Fields marked * are required.</p></div>
          </div>
          <?php if(!empty($errors)): ?>
          <div class="contact-errors" role="alert">
            <?php foreach($errors as $err): ?><div><i class="bi bi-exclamation-circle"></i> <?=e($err)?></div><?php endforeach; ?>
          </div>
          <?php endif; ?>
          <form method="POST" class="contact-form">
            <input type="hidden" name="csrf" value="<?=e($contactCsrf)?>">
            <div style="font-size:.82rem;color:var(--muted)">Sending as <strong style="color:var(--ink)"><?=e($contactMember['full_name'])?></strong> · <?=e($contactMember['email'])?></div>
            <div>
              <label class="f-label" for="contact-subject">What’s this about?</label>
              <select id="contact-subject" name="subject" class="f-input">
                <option value="">Select a topic…</option>
                <?php foreach(['Membership','Partnership / Sponsorship','Event Collaboration','Media / Press','General Feedback','Other'] as $o): ?>
                <option value="<?=$o?>" <?=($_POST['subject']??'')===$o?'selected':''?>><?=$o?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div><label class="f-label" for="contact-message">Your message *</label><textarea id="contact-message" name="message" class="f-input" rows="5" maxlength="5000" placeholder="Tell us what’s on your mind…" required><?=e($_POST['message']??'')?></textarea></div>
            <button type="submit" class="btn btn-forest contact-submit"><i class="bi bi-send-fill"></i> Send message <i class="bi bi-arrow-right"></i></button>
          </form>
        </div>
        <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Socials -->
    <div class="contact-socials reveal">
      <div><div class="label">Keep up with us</div><p>Follow club updates, student work, and upcoming events.</p></div>
      <div class="contact-social-grid">
        <?php foreach([['bi-instagram','#E1306C','Instagram','https://www.instagram.com/ajira_digital.club.zetech/'],['bi-twitter-x','var(--ink)','X','https://x.com/ZetechAjiraclub'],['bi-facebook','#1877F2','Facebook','https://www.facebook.com/profile.php?id=61595630222788'],['bi-tiktok','var(--ink)','TikTok','https://www.tiktok.com/@zetechajiraclub?lang=en']] as [$i,$c,$n,$url]): ?>
        <a href="<?=$url?>" target="_blank" rel="noopener noreferrer" class="card" style="padding:.85rem;display:flex;flex-direction:column;align-items:center;gap:.4rem;font-size:.72rem;font-weight:600;color:var(--ink);text-decoration:none">
          <i class="bi <?=$i?>" style="font-size:1.25rem;color:<?=$c?>"></i><?=$n?>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<?php include '_footer.php'; ?>
