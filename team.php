<?php
$pageTitle='Our Team'; include '_head.php';
$exec=[
  ['Antoney Ouko','Chairperson','BBIT Year 1','Photos/Antoney.jpeg','Full-stack developer and club chair. Built the club\'s digital systems with M-Pesa integration. Leading the revival since mid-2026.'],
  ['Lencia Otieno','Vice Chairperson','BSc. IT Year 2','Photos/lencia.jpg','Coordinates club programmes and member welfare. Specialist in UI/UX design.'],
  ['Branol Joseph','Secretary','Diploma IT Year 2','Photos/branol.jpeg','Manages records, correspondence, and the Helptivo attendance system.'],
  ['Beatrice Wanjiru','Treasurer','BCom Year 3','Photos/Beatrice.jpeg','Handles all financial matters — fees, budgets, and university reporting.'],
  ['Dorcas Wanjama','PR & Comms','Diploma Business IT','Photos/Dorcas.jpeg','Runs social media presence, event promotions, and external communications.'],
];
?>
<div class="page-hero">
  <div class="wrap page-hero-inner">
    <nav class="breadcrumb"><a href="index.php">Home</a> <i class="bi bi-chevron-right" style="font-size:.55rem"></i> Team</nav>
    <div class="pg-label"><i class="bi bi-people-fill"></i> Leadership</div>
    <h1>Executive <em>Committee</em></h1>
    <p>Elected students leading AJira Digital Club this semester — accountable to every member.</p>
  </div>
</div>
<section>
  <div class="wrap">
    <!-- Chairperson featured -->
    <?php $c=$exec[0]; [$n,$r,$co,$ph,$bio]=$c; $init=strtoupper(substr($n,0,1).substr(strstr($n,' '),1,1)); ?>
    <div class="reveal" style="background:var(--forest);border-radius:var(--r-xl);padding:2rem 1.5rem;margin-bottom:1.5rem;position:relative;overflow:hidden;box-shadow:var(--sh-xl)">
      <div style="position:absolute;top:0;left:0;right:0;height:2px;background:var(--gold)"></div>
      <div style="display:flex;align-items:flex-start;gap:1.25rem">
        <?php if(file_exists(__DIR__.'/'.$ph)): ?>
        <img src="<?=e($ph)?>" alt="<?=e($n)?>" style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:2px solid rgba(212,168,67,.25);flex-shrink:0">
        <?php else: ?>
        <div style="width:72px;height:72px;border-radius:50%;background:rgba(212,168,67,.12);border:2px solid rgba(212,168,67,.2);display:flex;align-items:center;justify-content:center;font-family:var(--font-display);font-size:1.35rem;color:var(--gold);flex-shrink:0"><?=$init?></div>
        <?php endif; ?>
        <div>
          <div style="font-size:.65rem;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:var(--gold);margin-bottom:.3rem"><?=e($r)?></div>
          <div style="font-family:var(--font-display);font-size:1.4rem;font-weight:300;color:white;margin-bottom:.25rem;letter-spacing:-.02em"><?=e($n)?></div>
          <div style="font-size:.78rem;color:rgba(255,255,255,.35);margin-bottom:.75rem"><?=e($co)?></div>
          <p style="font-size:.84rem;color:rgba(255,255,255,.55);line-height:1.7"><?=e($bio)?></p>
        </div>
      </div>
    </div>
    <!-- Rest of exec -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
      <?php foreach(array_slice($exec,1) as $i=>$m): [$n,$r,$co,$ph,$bio]=$m; $init=strtoupper(substr($n,0,1).substr(strstr($n,' '),1,1)); ?>
      <div class="card reveal" style="padding:1.35rem;transition-delay:<?=$i*.07?>s">
        <?php if(file_exists(__DIR__.'/'.$ph)): ?>
        <img src="<?=e($ph)?>" alt="<?=e($n)?>" style="width:56px;height:56px;border-radius:50%;object-fit:cover;border:1.5px solid var(--border);margin-bottom:.85rem">
        <?php else: ?>
        <div style="width:56px;height:56px;border-radius:50%;background:var(--warm-grey);border:1.5px solid var(--border);display:flex;align-items:center;justify-content:center;font-family:var(--font-display);font-size:1rem;color:var(--muted);margin-bottom:.85rem"><?=$init?></div>
        <?php endif; ?>
        <div style="font-family:var(--font-display);font-size:.92rem;color:var(--ink);margin-bottom:.2rem"><?=e($n)?></div>
        <div style="font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:var(--gold);margin-bottom:.25rem"><?=e($r)?></div>
        <div style="font-size:.72rem;color:var(--dim);margin-bottom:.65rem"><?=e($co)?></div>
        <p style="font-size:.78rem;color:var(--muted);line-height:1.6"><?=e($bio)?></p>
      </div>
      <?php endforeach; ?>
    </div>
    <p style="text-align:center;color:var(--muted);font-size:.82rem;margin-top:2rem" class="reveal">Interested in joining the exec team? <a href="contact.php" style="color:var(--sage);font-weight:600;text-decoration:none">Get in touch →</a></p>
  </div>
</section>
<?php include '_footer.php'; ?>
