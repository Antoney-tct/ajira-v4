<?php
declare(strict_types=1);
require_once __DIR__.'/config/bootstrap.php';
$success=false; $errors=[]; $fd=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
  $fn=trim($_POST['full_name']??''); $rn=strtoupper(trim($_POST['reg_number']??''));
  $ph=trim($_POST['phone']??''); $em=strtolower(trim($_POST['email']??'')); $pw=$_POST['password']??'';
  $cy=trim($_POST['course_year']??''); $mt=$_POST['member_type']??'new';
  $fd=compact('fn','rn','ph','em','cy','mt');
  if(strlen($fn)<3) $errors[]='Full name is required.';
  if(strlen($rn)<4) $errors[]='Registration number is required.';
  if(!preg_match('/^(07|01)\d{8}$/',$ph)) $errors[]='Enter a valid phone number (07XX or 01XX).';
  if(!filter_var($em,FILTER_VALIDATE_EMAIL)) $errors[]='Enter a valid email address.';
  if(strlen($cy)<3) $errors[]='Course and year is required.';
  if(strlen($pw)<8) $errors[]='Create a password with at least 8 characters.';
  if(empty($errors)){
    try{
      $ex=db()->prepare('SELECT id,is_active FROM members WHERE reg_number=? LIMIT 1'); $ex->execute([$rn]); $ex=$ex->fetch();
      if($ex&&$ex['is_active']&&$mt==='new') $errors[]='This reg number already has an active membership. Select Renewal.';
      else{
        $hash=password_hash($pw,PASSWORD_DEFAULT);
        if($ex) db()->prepare('UPDATE members SET full_name=?,phone=?,email=?,password_hash=?,course_year=?,member_type=?,is_active=0,registered_at=NOW() WHERE reg_number=?')->execute([$fn,$ph,$em,$hash,$cy,$mt,$rn]);
        else    db()->prepare('INSERT INTO members(full_name,reg_number,phone,email,password_hash,course_year,member_type,is_active) VALUES(?,?,?,?,?,?,?,0)')->execute([$fn,$rn,$ph,$em,$hash,$cy,$mt]);
        $memberLookup=db()->prepare('SELECT id FROM members WHERE reg_number=? LIMIT 1'); $memberLookup->execute([$rn]);
        $memberId=(int)$memberLookup->fetchColumn();
        $_SESSION['member_id']=$memberId;
        memberNotification($memberId,'Registration received','Your account has been submitted for admin review. You will be notified when your membership is activated.');
        $success=true;
      }
    }catch(Throwable $e){ error_log($e->getMessage()); $errors[]='Something went wrong. Please try again.'; }
  }
}
$pageTitle='Join the Club'; include '_head.php';
?>

<div class="page-hero">
  <div class="wrap page-hero-inner">
    <nav class="breadcrumb"><a href="index.php">Home</a> <i class="bi bi-chevron-right" style="font-size:.55rem"></i> Join</nav>
    <div class="pg-label"><i class="bi bi-lightning-charge-fill"></i> Membership</div>
    <h1>Join <em>AJira Digital</em></h1>
    <p>Complete the form and submit your membership registration for admin review.</p>
  </div>
</div>

<section>
  <div class="wrap">

  <?php if($success): ?>
  <!-- SUCCESS STATE -->
  <div style="max-width:520px;margin:0 auto">
    <div style="text-align:center;margin-bottom:2rem">
      <div style="width:72px;height:72px;border-radius:50%;background:var(--warm-grey);border:1.5px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto 1.25rem">✅</div>
      <div style="font-family:var(--font-display);font-size:1.75rem;font-weight:300;color:var(--ink);margin-bottom:.5rem">Registration submitted.</div>
      <p style="color:var(--muted);font-size:.9rem;line-height:1.75">Thank you, <strong style="color:var(--ink)"><?=e($fd['fn']??'')?></strong>. Your registration is pending admin review.</p>
    </div>
    <div style="background:var(--warm-grey);border-radius:var(--r-xl);padding:1.4rem;margin-bottom:1.75rem">
      <div style="font-weight:600;font-size:.86rem;color:var(--ink);margin-bottom:.85rem">What happens next:</div>
      <?php foreach(['The admin reviews your registration','Your membership is activated when approved','You can sign in to view your profile status','The admin can renew or deactivate your membership at any time'] as $n=>$step): ?>
      <div style="display:flex;align-items:flex-start;gap:.75rem;margin-bottom:.55rem">
        <div style="width:22px;height:22px;border-radius:50%;background:var(--forest);color:var(--gold);display:flex;align-items:center;justify-content:center;font-size:.66rem;font-weight:700;flex-shrink:0"><?=$n+1?></div>
        <div style="font-size:.83rem;color:var(--body);padding-top:.1rem"><?=$step?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <div style="display:flex;flex-direction:column;gap:.65rem">
      <a href="member_profile.php" class="btn btn-forest btn-full btn-lg"><i class="bi bi-person-circle"></i> Open My Member Profile</a>
      <a href="index.php" class="btn btn-outline btn-full">← Back to Home</a>
    </div>
  </div>

  <?php else: ?>
  <!-- FORM -->

  <!-- Benefits checklist -->
  <div style="margin-bottom:2rem;display:flex;flex-direction:column;gap:.45rem">
    <?php foreach(['Free workshops every semester','Freelancing coaching — first client guaranteed','AJira certificates for CV and LinkedIn','Access to 120+ member network','Priority event access'] as $b): ?>
    <div style="display:flex;align-items:center;gap:.65rem;font-size:.84rem;color:var(--body)">
      <i class="bi bi-check2" style="color:var(--gold);font-size:.95rem;flex-shrink:0"></i><?=$b?>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Registration form -->
  <div class="card reveal" style="padding:1.75rem">
    <div style="font-family:var(--font-display);font-size:1.15rem;font-weight:300;color:var(--ink);margin-bottom:1.5rem">Register now</div>

    <?php if(!empty($errors)): ?>
    <div style="background:rgba(192,57,43,.05);border:1px solid rgba(192,57,43,.15);border-radius:var(--r);padding:.8rem 1rem;margin-bottom:1.25rem">
      <?php foreach($errors as $err): ?><div style="color:var(--red);font-size:.84rem;font-weight:600;margin-bottom:.2rem"><?=e($err)?></div><?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <input type="hidden" name="member_type" id="mtField" value="<?=e($fd['mt']??'new')?>">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:.75rem">
        <div style="grid-column:1/-1"><label class="f-label">Full Name *</label><input type="text" name="full_name" class="f-input" placeholder="Jane Wanjiku Kamau" value="<?=e($fd['fn']??'')?>" required></div>
        <div><label class="f-label">Reg Number *</label><input type="text" name="reg_number" class="f-input" placeholder="BCT/2021/003" value="<?=e($fd['rn']??'')?>" style="text-transform:uppercase" required></div>
        <div><label class="f-label">Phone *</label><input type="tel" name="phone" class="f-input" placeholder="07XX XXX XXX" value="<?=e($fd['ph']??'')?>" required></div>
        <div><label class="f-label">Email *</label><input type="email" name="email" class="f-input" placeholder="jane@email.com" value="<?=e($fd['em']??'')?>" required></div>
        <div style="grid-column:1/-1"><label class="f-label">Course & Year *</label><input type="text" name="course_year" class="f-input" placeholder="e.g. BSc. IT — Year 2" value="<?=e($fd['cy']??'')?>" required></div>
        <div style="grid-column:1/-1"><label class="f-label">Create Password *</label><input type="password" name="password" class="f-input" minlength="8" autocomplete="new-password" placeholder="At least 8 characters" required></div>
      </div>

      <button type="submit" class="btn btn-forest btn-full" style="padding:.85rem">Submit Registration</button>
    </form>
    <p style="text-align:center;color:var(--dim);font-size:.74rem;margin-top:.9rem"><i class="bi bi-shield-check" style="color:var(--gold)"></i> Official Zetech University Club · Your data is safe</p>
  </div>
  <?php endif; ?>
  </div>
</section>

<?php include '_footer.php'; ?>
