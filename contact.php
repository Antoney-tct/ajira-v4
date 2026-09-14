<?php
declare(strict_types=1);
require_once __DIR__.'/config/bootstrap.php';
$sent=false; $errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
  $name=trim($_POST['name']??''); $email=strtolower(trim($_POST['email']??''));
  $subject=trim($_POST['subject']??''); $message=trim($_POST['message']??'');
  if(strlen($name)<2) $errors[]='Your name is required.';
  if(!filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[]='Enter a valid email address.';
  if(strlen($message)<10) $errors[]='Please write a message (min 10 characters).';
  if(empty($errors)){
    try{ db()->prepare('INSERT INTO contact_messages(name,email,subject,message) VALUES(?,?,?,?)')->execute([$name,$email,$subject,$message]); $sent=true; }
    catch(Throwable $e){ error_log($e->getMessage()); $errors[]='Failed to send. Try again.'; }
  }
}
$pageTitle='Contact Us'; include '_head.php';
?>
<div class="page-hero">
  <div class="wrap page-hero-inner">
    <nav class="breadcrumb"><a href="index.php">Home</a> <i class="bi bi-chevron-right" style="font-size:.55rem"></i> Contact</nav>
    <div class="pg-label"><i class="bi bi-chat-dots-fill"></i> Get in Touch</div>
    <h1>Contact <em>Us</em></h1>
    <p>WhatsApp is fastest. Email us for formal enquiries, partnerships, or media requests.</p>
  </div>
</div>
<section>
  <div class="wrap">
    <!-- Contact details as editorial list -->
    <div style="border-top:1px solid var(--border);margin-bottom:2.5rem">
      <?php foreach([['Location','Zetech University, Ruiru Campus, Kiambu County'],['Email','ajiraclub@zetech.ac.ke'],['WhatsApp','Join our group for instant updates and event notices'],['Hours','Monday – Friday, 8:00 AM – 6:00 PM']] as $i=>[$l,$v]): ?>
      <div class="reveal" style="transition-delay:<?=$i*.06?>s;display:grid;grid-template-columns:100px 1fr;gap:1.5rem;padding:1.1rem 0;border-bottom:1px solid var(--border);align-items:center">
        <div style="font-size:.68rem;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:var(--muted)"><?=$l?></div>
        <div style="font-size:.9rem;color:var(--ink)"><?=$v?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Form -->
    <?php if($sent): ?>
    <div style="background:var(--warm-grey);border:1px solid var(--border);border-radius:var(--r-xl);padding:2.5rem;text-align:center" class="reveal">
      <div style="font-family:var(--font-display);font-size:1.5rem;font-weight:300;color:var(--ink);margin-bottom:.4rem">Message sent.</div>
      <p style="color:var(--muted);font-size:.88rem">We'll reply within 24 hours.</p>
      <a href="contact.php" class="btn btn-outline" style="margin-top:1.25rem;display:inline-flex">Send another →</a>
    </div>
    <?php else: ?>
    <div class="card reveal" style="padding:1.75rem">
      <div style="font-family:var(--font-display);font-size:1.2rem;color:var(--ink);margin-bottom:1.5rem;font-weight:300">Send a message</div>
      <?php if(!empty($errors)): ?>
      <div style="background:rgba(192,57,43,.06);border:1.5px solid rgba(192,57,43,.15);border-radius:var(--r);padding:.8rem 1rem;margin-bottom:1.25rem">
        <?php foreach($errors as $err): ?><div style="color:var(--red);font-size:.84rem;font-weight:600"><?=e($err)?></div><?php endforeach; ?>
      </div>
      <?php endif; ?>
      <form method="POST" style="display:flex;flex-direction:column;gap:.85rem">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
          <div><label class="f-label">Full Name *</label><input type="text" name="name" class="f-input" placeholder="Jane Wanjiku" value="<?=e($_POST['name']??'')?>" required></div>
          <div><label class="f-label">Email *</label><input type="email" name="email" class="f-input" placeholder="jane@email.com" value="<?=e($_POST['email']??'')?>" required></div>
        </div>
        <div>
          <label class="f-label">Subject</label>
          <select name="subject" class="f-input">
            <option value="">Select a topic…</option>
            <?php foreach(['Membership','Partnership / Sponsorship','Event Collaboration','Media / Press','General Feedback','Other'] as $o): ?>
            <option value="<?=$o?>" <?=($_POST['subject']??'')===$o?'selected':''?>><?=$o?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div><label class="f-label">Message *</label><textarea name="message" class="f-input" rows="4" placeholder="Tell us what's on your mind…" required><?=e($_POST['message']??'')?></textarea></div>
        <button type="submit" class="btn btn-forest btn-full" style="padding:.82rem"><i class="bi bi-send-fill"></i> Send Message</button>
      </form>
    </div>
    <?php endif; ?>

    <!-- Socials -->
    <div class="reveal" style="margin-top:2rem;border-top:1px solid var(--border);padding-top:1.75rem">
      <div class="label" style="margin-bottom:1rem">Follow us</div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.6rem">
        <?php foreach([['bi-whatsapp','#25D366','WhatsApp'],['bi-instagram','#E1306C','Instagram'],['bi-twitter-x','var(--ink)','Twitter/X'],['bi-facebook','#1877F2','Facebook'],['bi-tiktok','var(--ink)','TikTok'],['bi-youtube','#FF0000','YouTube']] as [$i,$c,$n]): ?>
        <a href="#" class="card" style="padding:.85rem;display:flex;flex-direction:column;align-items:center;gap:.4rem;font-size:.72rem;font-weight:600;color:var(--ink);text-decoration:none">
          <i class="bi <?=$i?>" style="font-size:1.25rem;color:<?=$c?>"></i><?=$n?>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<?php include '_footer.php'; ?>
