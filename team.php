<?php
$pageTitle='Our Team'; include '_head.php';
$campuses=[
  'Ruiru (TRC)' => [
    ['Antoney Ouko','Chairperson','BBIT Year 1','Photos/Antoney.jpeg','Full-stack developer and club chair. Built the club\'s digital systems with M-Pesa integration. Leading the revival since mid-2026.'],
    ['Lencia Otieno','Vice Chairperson','BSc. IT Year 2','Photos/lencia.jpg','Coordinates club programmes and member welfare. Specialist in UI/UX design.'],
    ['Branol Joseph','Secretary','Diploma IT Year 2','Photos/branol.jpeg','Manages records, correspondence, and the Helptivo attendance system.'],
    ['Beatrice Wanjiru','Treasurer','BCom Year 3','Photos/Beatrice.jpeg','Handles all financial matters — fees, budgets, and university reporting.'],
    ['Dorcas Wanjama','PR & Comms','Diploma Business IT','Photos/Dorcas.jpeg','Runs social media presence, event promotions, and external communications.'],
  ],
  'Mangu Campus' => [
    ['Mangu Chairperson','Chairperson','Course and year to be updated','','Add the Mangu campus chairperson’s bio here.',true,'photo-1500648767791-00dcc994a43e'],
    ['Mangu Vice Chairperson','Vice Chairperson','Course and year to be updated','','Add the Mangu campus vice chairperson’s bio here.',true,'photo-1534528741775-53994a69daeb'],
    ['Mangu Secretary','Secretary','Course and year to be updated','','Add the Mangu campus secretary’s bio here.',true,'photo-1506794778202-cad84cf45f1d'],
    ['Mangu Treasurer','Treasurer','Course and year to be updated','','Add the Mangu campus treasurer’s bio here.',true,'photo-1531123897727-8f129e1688ce'],
    ['Mangu PR & Comms','PR & Comms','Course and year to be updated','','Add the Mangu campus communications lead’s bio here.',true,'photo-1507003211169-0a1dd7228f2d'],
  ],
];
$patron=['Mr Evans Lagat','Club Patron','Patron details to be updated',' confirmed patron’s, department, and bio here.','photo-1560250097-0b93528c311a'];
function teamInitials(string $name):string{
  $parts=preg_split('/\s+/',trim($name));
  return strtoupper(substr($parts[0]??'',0,1).substr($parts[count($parts)-1]??'',0,1));
}
?>
<div class="page-hero">
  <div class="wrap page-hero-inner">
    <nav class="breadcrumb" aria-label="Breadcrumb"><ol><li><a href="index.php"><i class="bi bi-house-door-fill" aria-hidden="true"></i>Home</a></li><li class="breadcrumb-separator" aria-hidden="true"><i class="bi bi-chevron-right"></i></li><li aria-current="page">Team</li></ol></nav>
    <div class="pg-label"><i class="bi bi-people-fill"></i> One club · two campuses</div>
    <h1>Meet the <em>people behind it</em></h1>
    <p>Student leaders at Ruiru (TRC) and Mangu working together to grow the AJira Digital Club community.</p>
    <div class="team-campus-tags"><span><i class="bi bi-geo-alt-fill"></i> Ruiru (TRC)</span><span><i class="bi bi-geo-alt-fill"></i> Mangu Campus</span></div>
  </div>
</div>

<section class="team-page">
  <div class="wrap">
    <div class="team-intro reveal">
      <div><div class="label">Shared guidance</div><h2 class="t-lg">Meet our <em>patron</em></h2></div>
      <p>Supporting the club’s student leaders and community across both campuses.</p>
    </div>
    <article class="team-patron-card reveal">
      <div class="team-patron-photo">
        <img src="https://images.unsplash.com/<?=$patron[4]?>?auto=format&amp;fit=crop&amp;w=500&amp;q=85" alt="" loading="lazy">
        <span>Placeholder profile</span>
      </div>
      <div class="team-patron-copy">
        <div class="label">Club patron · All campuses</div>
        <h3><?=e($patron[0])?></h3>
        <div class="team-patron-role"><?=e($patron[1])?> <span>·</span> <?=e($patron[2])?></div>
        <p><?=e($patron[3])?></p>
      </div>
      <i class="bi bi-stars team-patron-spark" aria-hidden="true"></i>
    </article>

    <?php foreach($campuses as $campus=>$members): ?>
    <section class="team-campus-section" aria-labelledby="campus-<?=e(strtolower(str_replace([' ','(',')'],'-',$campus)))?>">
      <div class="team-campus-heading reveal">
        <div><div class="label">Executive team</div><h2 id="campus-<?=e(strtolower(str_replace([' ','(',')'],'-',$campus)))?>"><?=e($campus)?></h2></div>
        <span><?=count($members)?> leaders</span>
      </div>
      <div class="team-member-grid">
        <?php foreach($members as $i=>$member):
          [$name,$role,$course,$photo,$bio]=$member;
          $placeholder=$member[5]??false;
          $photoId=$member[6]??'';
          $hasPhoto=$photo!==''&&file_exists(__DIR__.'/'.$photo);
        ?>
        <article class="team-member-card reveal" style="transition-delay:<?=$i*.05?>s">
          <div class="team-member-top">
            <div class="team-member-avatar">
              <?php if($hasPhoto): ?>
              <img src="<?=e($photo)?>" alt="<?=e($name)?>" loading="lazy">
              <?php elseif($photoId!==''): ?>
              <img src="https://images.unsplash.com/<?=$photoId?>?auto=format&amp;fit=crop&amp;w=240&amp;q=80" alt="" loading="lazy">
              <?php else: ?>
              <span><?=e(teamInitials($name))?></span>
              <?php endif; ?>
            </div>
            <?php if($placeholder): ?><span class="team-placeholder-tag">Placeholder</span><?php endif; ?>
            <span class="team-member-campus"><?=e($campus)?></span>
          </div>
          <div class="label team-member-role"><?=e($role)?></div>
          <h3><?=e($name)?></h3>
          <div class="team-member-course"><?=e($course)?></div>
          <p><?=e($bio)?></p>
        </article>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endforeach; ?>
    <div class="team-join-banner reveal">
      <div><span class="label">Your turn</span><h2>Want to help shape the club?</h2><p>Get involved, bring your ideas, and grow with students from both campuses.</p></div>
      <a href="contact.php" class="btn btn-gold">Get in touch <i class="bi bi-arrow-up-right"></i></a>
    </div>
  </div>
</section>
<?php include '_footer.php'; ?>
