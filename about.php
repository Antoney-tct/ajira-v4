<?php
$pageTitle='About Us';
$pageDesc='AJira Digital Club — our mission, vision, values and history at Zetech University since 2021.';
include '_head.php';
?>
<div class="page-hero">
  <div class="wrap page-hero-inner">
    <nav class="breadcrumb"><a href="index.php">Home</a> <i class="bi bi-chevron-right" style="font-size:.55rem"></i> About</nav>
    <div class="pg-label"><i class="bi bi-info-circle"></i> About the Club</div>
    <h1>Building <em>Digital Kenya</em>,<br>one student at a time.</h1>
    <p>Founded in 2021 at Zetech University, Ruiru — training, connecting, and launching Kenya's next generation of digital professionals.</p>
  </div>
</div>

<section>
  <div class="wrap">
    <div class="masonry reveal" style="margin-bottom:3rem">
      <?php foreach(['photo-1522202176988-66273c2fd55f','photo-1517048676732-d65bc937f952','photo-1552664730-d307ca884978','photo-1503676260728-1c00da094a0b'] as $id): ?>
      <div class="g-item" data-lb="https://images.unsplash.com/<?=$id?>?w=1400&q=80"><img src="https://images.unsplash.com/<?=$id?>?w=600&q=80" alt="Club photo" loading="lazy"><div class="g-hover"><i class="bi bi-zoom-in"></i></div></div>
      <?php endforeach; ?>
    </div>

    <!-- Mission / Vision / Tagline -->
    <div style="border-top:1px solid var(--border)">
      <?php foreach([['Mission','To equip Zetech students with practical digital skills enabling sustainable income and contribution to Kenya\'s digital economy.'],['Vision','To be the leading digital skills society in Kenyan universities — producing graduates who thrive in the global digital marketplace.'],['Tagline','<em>Innovate. Create. Impact.</em> — three words that define every workshop, every member, every outcome.']] as $i=>[$t,$d]): ?>
      <div class="reveal" style="transition-delay:<?=$i*.07?>s;display:grid;grid-template-columns:1fr 2fr;gap:2rem;padding:1.75rem 0;border-bottom:1px solid var(--border);align-items:start">
        <div style="font-family:var(--font-display);font-size:.95rem;font-weight:400;color:var(--ink)"><?=$t?></div>
        <div style="font-size:.92rem;color:var(--body);line-height:1.75"><?=$d?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="bg-warm">
  <div class="wrap">
    <div class="stats-band reveal">
      <div class="stat-block"><div class="stat-num" data-n="120" data-s="+">0</div><div class="stat-lbl">Members</div></div>
      <div class="stat-block"><div class="stat-num" data-n="26" data-s="+">0</div><div class="stat-lbl">Events</div></div>
      <div class="stat-block"><div class="stat-num" data-n="4" data-s="">0</div><div class="stat-lbl">Skill Tracks</div></div>
      <div class="stat-block"><div class="stat-num" data-n="3" data-s="">0</div><div class="stat-lbl">Years</div></div>
    </div>
  </div>
</section>

<section>
  <div class="wrap">
    <div class="label reveal" style="margin-bottom:1.25rem">Our principles</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
      <?php foreach([['Learn by Doing','Real projects, real portfolios — not theory slides.'],['Community First','Senior members mentor juniors. We grow together.'],['Kenya Focused','Local platforms, local clients, the real Kenyan market.'],['Results Driven','Your first freelancing client — then many more.'],['Academic Balance','Flexible sessions that complement your timetable.'],['Global Ready','Upwork, Fiverr, and beyond — local AND international.']] as $i=>[$t,$d]): ?>
      <div class="card reveal" style="padding:1.35rem;transition-delay:<?=$i*.06?>s">
        <div style="font-family:var(--font-display);font-size:.95rem;color:var(--ink);margin-bottom:.3rem"><?=$t?></div>
        <div style="font-size:.8rem;color:var(--muted);line-height:1.6"><?=$d?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="bg-warm">
  <div class="wrap">
    <div class="label reveal" style="margin-bottom:1.5rem">Club history</div>
    <div style="border-top:1px solid var(--border)">
      <?php foreach([['2021','Club Founded','Started by 8 IT students who wanted to turn skills into income.'],['2022','First Bootcamp','3-day bootcamp. Members began landing their first freelancing clients.'],['2023','University Registration','Officially registered by Zetech University. Partnership with Helptivo.'],['2024','Digital Membership','Digital registration launched. Community grew to 120+ members.'],['2026','Revived & Rebuilt','New leadership, new systems, fresh energy and bigger vision.']] as $i=>[$y,$t,$d]): ?>
      <div class="reveal" style="transition-delay:<?=$i*.07?>s;display:grid;grid-template-columns:80px 1fr;gap:1.5rem;padding:1.35rem 0;border-bottom:1px solid var(--border);align-items:start">
        <div style="font-family:var(--font-display);font-size:.88rem;color:var(--gold);font-weight:400"><?=$y?></div>
        <div><div style="font-weight:600;font-size:.9rem;color:var(--ink);margin-bottom:.2rem"><?=$t?></div><div style="font-size:.82rem;color:var(--muted);line-height:1.65"><?=$d?></div></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section style="padding:3rem 0 5rem">
  <div class="wrap">
    <div class="reveal" style="background:var(--forest);border-radius:var(--r-xl);padding:2.5rem 1.75rem;position:relative;overflow:hidden;box-shadow:var(--sh-xl)">
      <div style="position:absolute;top:0;left:0;right:0;height:2px;background:var(--gold)"></div>
      <h3 style="font-family:var(--font-display);font-size:clamp(1.6rem,4vw,2.5rem);font-weight:300;color:white;margin-bottom:.65rem">Be part of the story.</h3>
      <p style="color:rgba(255,255,255,.4);margin-bottom:1.75rem;font-size:.9rem">Register online and wait for admin approval.</p>
      <a href="join.php" class="btn btn-gold btn-lg btn-full" style="max-width:260px;display:flex"><i class="bi bi-lightning-charge-fill"></i> Join Now</a>
    </div>
  </div>
</section>
<?php include '_footer.php'; ?>
