<?php
declare(strict_types=1);
require_once __DIR__.'/config/bootstrap.php';
$projects=[
  ['title'=>'Campus Connect','type'=>'Community platform','description'=>'A student-first platform that helps members discover club activities, connect with peers, and find opportunities across campus.','tools'=>'UX research · Product design · PHP','image'=>'https://images.unsplash.com/photo-1556761175-b413da4baf72?auto=format&fit=crop&w=1000&q=85','status'=>'In progress'],
  ['title'=>'Mkulima Market','type'=>'Digital commerce','description'=>'A lightweight marketplace concept connecting small Kenyan growers with local customers through clear listings and simple mobile-first flows.','tools'=>'Branding · Web design · Market research','image'=>'https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&w=1000&q=85','status'=>'Prototype'],
  ['title'=>'Skill Sprint','type'=>'Learning experience','description'=>'Short, practical learning journeys that help students move from curiosity to a portfolio-ready digital project in four weeks.','tools'=>'Curriculum · Content · Community','image'=>'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1000&q=85','status'=>'Active'],
  ['title'=>'Ruiru Stories','type'=>'Digital storytelling','description'=>'A student-led story archive documenting the people, places, and ideas shaping the creative technology community around Zetech.','tools'=>'Photography · Editorial · Social media','image'=>'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1000&q=85','status'=>'Ongoing'],
];
$pageTitle='Projects'; include '_head.php';
?>
<div class="page-hero">
  <div class="wrap page-hero-inner">
    <nav class="breadcrumb"><a href="index.php">Home</a> <i class="bi bi-chevron-right" style="font-size:.55rem"></i> Projects</nav>
    <div class="pg-label"><i class="bi bi-kanban"></i> Work by the club</div>
    <h1>Ideas into <em>projects</em></h1>
    <p>Real briefs, useful experiments, and student-built work that turns digital skills into visible impact.</p>
  </div>
</div>
<section>
  <div class="wrap">
    <div style="display:flex;align-items:end;justify-content:space-between;gap:1rem;margin-bottom:2rem;flex-wrap:wrap">
      <div>
        <div class="label" style="margin-bottom:.65rem">Selected work</div>
        <h2 class="t-lg" style="margin:0">Built together, <em>shipped proudly</em></h2>
      </div>
      <p style="max-width:330px;color:var(--muted);font-size:.84rem;line-height:1.7;margin:0">Every project is a chance to practise collaboration, solve a real problem, and leave something useful behind.</p>
    </div>
    <div class="projects-grid" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem">
      <?php foreach($projects as $i=>$project): ?>
      <article class="card reveal" style="overflow:hidden;transition-delay:<?=$i*.06?>s">
        <div style="height:210px;background:var(--forest);position:relative;overflow:hidden">
          <img src="<?=e($project['image'])?>" alt="<?=e($project['title'])?> project" loading="lazy" style="width:100%;height:100%;object-fit:cover;opacity:.82;display:block">
          <div style="position:absolute;inset:0;background:linear-gradient(180deg,rgba(12,26,14,.05),rgba(12,26,14,.72))"></div>
          <span class="badge b-gold" style="position:absolute;top:1rem;left:1rem"><?=e($project['status'])?></span>
          <div style="position:absolute;left:1.1rem;right:1.1rem;bottom:1rem;color:white;font-family:var(--font-display);font-size:1.45rem;font-weight:300"><?=e($project['title'])?></div>
        </div>
        <div style="padding:1.25rem">
          <div style="font-size:.66rem;color:var(--sage);font-weight:700;text-transform:uppercase;letter-spacing:.1em;margin-bottom:.5rem"><?=e($project['type'])?></div>
          <p style="font-size:.84rem;color:var(--muted);line-height:1.7;margin-bottom:1rem"><?=e($project['description'])?></p>
          <div style="font-size:.72rem;color:var(--dim);border-top:1px solid var(--border);padding-top:.8rem"><i class="bi bi-tools" style="color:var(--gold);margin-right:.35rem"></i><?=e($project['tools'])?></div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<section style="background:var(--forest);padding:4rem 0">
  <div class="wrap" style="display:flex;align-items:center;justify-content:space-between;gap:2rem;flex-wrap:wrap">
    <div style="max-width:560px"><div class="label" style="color:var(--gold-2);margin-bottom:.65rem">Bring an idea</div><h2 class="t-lg" style="color:white;margin-bottom:.7rem">Have a problem worth <em>solving?</em></h2><p style="color:rgba(255,255,255,.55);font-size:.88rem;line-height:1.75;margin:0">Members can propose projects, join a working group, or partner with the club on a useful digital idea.</p></div>
    <a href="contact.php" class="btn btn-gold btn-lg"><i class="bi bi-arrow-up-right"></i> Start a conversation</a>
  </div>
</section>
<?php include '_footer.php'; ?>