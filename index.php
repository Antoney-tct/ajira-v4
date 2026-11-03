<?php
require_once __DIR__.'/config/bootstrap.php';
$pageTitle = 'Home';
$pageDesc  = 'AJira Digital Club — official student club at Zetech University, Ruiru , Mangu Campus. Join 120+ members building digital careers.';
$projects = require __DIR__.'/config/project-showcase.php';
$homeEvents = [];
$eventsQuerySucceeded = false;
try {
  $homeEvents = db()->query("SELECT title,description,event_date,location,is_online FROM events WHERE event_date>=NOW() AND is_published=1 ORDER BY event_date ASC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
  $eventsQuerySucceeded = true;
} catch (Throwable $error) {
  error_log('Unable to load homepage events: '.$error->getMessage());
}
if ($eventsQuerySucceeded && !$homeEvents) {
  $homeEvents = [
    ['title'=>'Digital Skills Bootcamp','description'=>'Hands-on training in web development, graphic design, and digital marketing.','event_date'=>date('Y-m-d H:i:s',strtotime('+7 days')),'location'=>'Lab 3, Main Campus','is_online'=>0],
    ['title'=>'Freelancing 101 Workshop','description'=>'Learn to land your first client on Upwork and Fiverr.','event_date'=>date('Y-m-d H:i:s',strtotime('+14 days')),'location'=>'Block C Lecture Hall','is_online'=>0],
    ['title'=>'Monthly Meetup','description'=>'Networking, project showcase, and peer feedback.','event_date'=>date('Y-m-d H:i:s',strtotime('+21 days')),'location'=>'Online — Zoom','is_online'=>1],
  ];
}
include '_head.php';
?>

<!-- ═══════ HERO ═══════ -->
<section style="min-height:100svh;display:flex;flex-direction:column;justify-content:flex-end;padding:0;background:var(--forest);overflow:hidden;position:relative">

  <!-- Background image -->
  <div style="position:absolute;inset:0;z-index:0">
    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=1400&q=80"
         alt="" style="width:100%;height:100%;object-fit:cover;display:block;opacity:.36;filter:grayscale(30%)">
  </div>

  <!-- Gold top line -->
  <div style="position:absolute;top:0;left:0;right:0;height:2px;background:var(--gold);z-index:3"></div>

  <!-- Content -->
  <div class="wrap" style="position:relative;z-index:2;padding-bottom:4rem;padding-top:6rem">

    <!-- Issue label -->
    <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:2.5rem">
      <div style="height:1px;width:32px;background:var(--gold);flex-shrink:0"></div>
      <span style="font-size:.68rem;font-weight:600;letter-spacing:.16em;text-transform:uppercase;color:rgba(255,255,255,.45)">Semester 2, 2026 · Zetech University, Ruiru</span>
    </div>

    <!-- Headline -->
    <h1 style="font-family:var(--font-display);font-size:clamp(3.2rem,11vw,7rem);font-weight:300;line-height:.95;letter-spacing:-.04em;color:white;margin-bottom:2rem;max-width:700px">
      Learn.<br>
      <em style="font-style:italic;color:var(--gold)">Build.</em><br>
      Earn.
    </h1>

    <!-- Body text -->
    <p style="color:rgba(255,255,255,.5);font-size:clamp(.92rem,2.5vw,1.08rem);line-height:1.8;max-width:440px;margin-bottom:2.5rem">
      AJira Digital Club trains Zetech students in web development, Graphic design, Digital marketing, and virtual Assistance — skills that pay, while you're still on campus.
    </p>

    <!-- CTAs -->
    <div style="display:flex;flex-wrap:wrap;gap:.75rem;margin-bottom:4rem">
      <a href="join.php" class="btn btn-gold btn-lg"><i class="bi bi-lightning-charge-fill"></i> Join Now</a>
      <a href="about.php" class="btn btn-ghost btn-lg">Learn more →</a>
    </div>

    <!-- Divider + proof row -->
    <div style="border-top:1px solid rgba(255,255,255,.08);padding-top:1.75rem;display:flex;flex-wrap:wrap;gap:2rem;align-items:center">
      <!-- Avatars -->
      <div style="display:flex;align-items:center;gap:.85rem">
        <div style="display:flex">
          <?php foreach(['photo-1507003211169-0a1dd7228f2d','photo-1494790108377-be9c29b29330','photo-1472099645785-5658abf4ff4e','photo-1438761681033-6461ffad8d80'] as $i=>$id): ?>
          <img src="https://images.unsplash.com/<?= $id ?>?w=80&q=80" alt="Member" style="width:32px;height:32px;border-radius:50%;border:2px solid rgba(255,255,255,.15);object-fit:cover;<?= $i?'margin-left:-9px':'' ?>" loading="eager">
          <?php endforeach; ?>
        </div>
        <div>
          <div style="font-size:.82rem;font-weight:600;color:white">120+ active members</div>
          <div style="font-size:.7rem;color:rgba(255,255,255,.35)">Zetech University</div>
        </div>
      </div>

      <!-- Stats pills -->
      <?php foreach([['26+','Events hosted'],['4','Skill tracks'],['Open','Membership']] as [$n,$l]): ?>
      <div style="display:flex;flex-direction:column;gap:.15rem">
        <div style="font-family:var(--font-display);font-size:1.15rem;font-weight:300;color:var(--gold-2);letter-spacing:-.02em"><?= $n ?></div>
        <div style="font-size:.66rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:rgba(255,255,255,.3)"><?= $l ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Scroll cue -->
  <div style="position:absolute;bottom:1.25rem;left:50%;transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:.3rem;color:rgba(255,255,255,.25);font-size:.65rem;font-weight:600;letter-spacing:.12em;text-transform:uppercase;z-index:2">
    <span>Scroll</span>
    <i class="bi bi-chevron-down" style="animation:float 2.5s ease-in-out infinite"></i>
  </div>
</section>

<!-- TICKER -->
<div class="ticker-wrap">
  <div class="ticker-track"><?php
  $ti=[['AJira Digital Club','⬦'],['120+ Members','⬦'],['Web Dev · Design · Marketing','⬦'],['Zetech University, Ruiru','⬦'],['Admin-managed membership','⬦'],['Freelancing Skills','⬦'],['Certified Training','⬦'],['Est. 2021','⬦']];
  for($x=0;$x<2;$x++) foreach($ti as [$t,$s]) echo "<div class='ticker-item'><strong>$t</strong><span class='ticker-sep'></span></div>";
  ?></div>
</div>

<!-- SKILLS — hands-on learning tracks -->
<section class="bg-warm home-skills">
  <div class="wrap">
    <div class="home-section-head reveal">
      <div>
        <div class="label" style="margin-bottom:.65rem">Learn by doing</div>
        <h2 class="t-lg">Pick a skill. <em>Make something.</em></h2>
      </div>
      <p>Practical sessions, friendly mentors, and room to try ideas before you take them to the real world.</p>
    </div>
    <div class="home-skill-grid">
      <?php foreach([
        ['bi-code-slash','Web Development','HTML, CSS, JavaScript and PHP','Build a portfolio website'],
        ['bi-palette','Graphic Design','Canva, Figma and visual storytelling','Create a brand identity'],
        ['bi-megaphone','Digital Marketing','SEO, social strategy and content','Grow an idea online'],
        ['bi-briefcase','Freelancing','Find clients and manage real briefs','Pitch your first project'],
      ] as $i=>[$icon,$title,$tools,$outcome]): ?>
      <article class="home-skill-card reveal" style="transition-delay:<?=$i*.07?>s">
        <div class="home-skill-top">
          <span class="home-skill-icon"><i class="bi <?=$icon?>"></i></span>
          <span class="home-skill-number">0<?=$i+1?></span>
        </div>
        <h3><?=$title?></h3>
        <p><?=$tools?></p>
        <div class="home-skill-outcome"><i class="bi bi-arrow-up-right-circle"></i> <?=$outcome?></div>
      </article>
      <?php endforeach; ?>
    </div>
    <div class="home-inline-link reveal"><a href="about.php">Explore how we learn <i class="bi bi-arrow-right"></i></a></div>
  </div>
</section>

<!-- COMMUNITY — visual break with illustrative campus imagery -->
<section class="home-community">
  <div class="wrap home-community-grid">
    <div class="home-photo-mosaic reveal" aria-label="Students collaborating and learning together">
      <img class="home-photo-main" src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&amp;fit=crop&amp;w=1000&amp;q=85" alt="Students collaborating around a table" loading="lazy">
      <img class="home-photo-small" src="https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&amp;fit=crop&amp;w=600&amp;q=85" alt="A small team sharing ideas" loading="lazy">
      <div class="home-photo-sticker"><i class="bi bi-stars"></i><span>Learn<br>together</span></div>
    </div>
    <div class="home-community-copy reveal d1">
      <div class="label" style="margin-bottom:.75rem">More than a class</div>
      <h2 class="t-lg">Find your people.<br><em>Build something real.</em></h2>
      <p>Bring your curiosity, meet people who want to grow, and turn what you learn into projects you can share.</p>
      <div class="home-community-points">
        <span><i class="bi bi-people-fill"></i> Learn alongside your peers</span>
        <span><i class="bi bi-lightbulb-fill"></i> Try ideas in a supportive space</span>
        <span><i class="bi bi-chat-heart-fill"></i> Share feedback and celebrate progress</span>
      </div>
      <a href="gallery.php" class="btn btn-forest">Explore club life <i class="bi bi-arrow-up-right"></i></a>
    </div>
  </div>
</section>

<!-- MEMBER PROJECTS -->
<section class="home-projects">
  <div class="wrap">
    <div class="home-section-head reveal">
      <div>
        <div class="label" style="margin-bottom:.65rem">Ideas in the making</div>
        <h2 class="t-lg">Small steps. <em>Real projects.</em></h2>
      </div>
      <a href="projects.php" class="btn btn-outline">See all projects <i class="bi bi-arrow-up-right"></i></a>
    </div>
    <div class="home-project-grid">
      <?php foreach(array_slice($projects,0,3) as $i=>$project): ?>
      <a class="home-project-card reveal" style="transition-delay:<?=$i*.08?>s" href="projects.php">
        <div class="home-project-image">
          <img src="<?=e($project['image'])?>" alt="<?=e($project['title'])?> concept illustration" loading="lazy">
          <span><?=e($project['status'])?></span>
          <i class="bi bi-arrow-up-right"></i>
        </div>
        <div class="home-project-info">
          <div class="label"><?=e($project['type'])?></div>
          <h3><?=e($project['title'])?></h3>
          <p><?=e($project['description'])?></p>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <p class="home-project-note"><i class="bi bi-info-circle"></i> Project concepts and progress are shared by the club; explore the project page for details.</p>
  </div>
</section>

<!-- STATS -->
<section class="home-stats-section">
  <div class="wrap">
    <div class="stats-band reveal">
      <div class="stat-block"><div class="stat-num" data-n="120" data-s="+">0</div><div class="stat-lbl">Members</div></div>
      <div class="stat-block"><div class="stat-num" data-n="26" data-s="+">0</div><div class="stat-lbl">Events</div></div>
      <div class="stat-block"><div class="stat-num" data-n="4" data-s="">0</div><div class="stat-lbl">Skill Tracks</div></div>
      <div class="stat-block"><div class="stat-num" data-n="3" data-s="">0</div><div class="stat-lbl">Years</div></div>
    </div>
  </div>
</section>

<!-- UPCOMING EVENTS -->
<section class="home-events">
  <div class="wrap">
    <div class="home-section-head reveal">
      <div>
        <div class="label" style="margin-bottom:.65rem"><i class="bi bi-calendar3"></i> Make it a date</div>
        <h2 class="t-lg">Learn something. <em>Meet someone.</em></h2>
      </div>
      <a href="events.php" class="btn btn-outline">All events <i class="bi bi-arrow-up-right"></i></a>
    </div>
    <?php if($homeEvents): ?>
    <div class="home-event-grid">
      <?php foreach($homeEvents as $i=>$event): ?>
      <article class="home-event-card reveal" style="transition-delay:<?=$i*.08?>s">
        <div class="home-event-date">
          <span><?=date('M',strtotime($event['event_date']))?></span>
          <strong><?=date('j',strtotime($event['event_date']))?></strong>
          <small><?=date('D',strtotime($event['event_date']))?></small>
        </div>
        <div class="home-event-content">
          <div class="home-event-meta">
            <span><i class="bi bi-clock"></i> <?=date('g:i A',strtotime($event['event_date']))?></span>
            <?php if(!empty($event['is_online'])): ?><span class="home-online-tag">Online</span><?php endif; ?>
          </div>
          <h3><?=e($event['title'])?></h3>
          <p><?=e($event['description']?:'Join us for a practical session with the AJira Digital Club community.')?></p>
          <div class="home-event-footer">
            <span><i class="bi bi-geo-alt"></i> <?=e($event['location']?:'Details to be announced')?></span>
            <a href="events.php" aria-label="View details for <?=e($event['title'])?>"><i class="bi bi-arrow-up-right"></i></a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="home-events-empty reveal">
      <span><i class="bi bi-calendar-heart"></i></span>
      <div><h3>New events are on the way</h3><p>Check back soon, or explore the club to see what we’re building.</p></div>
      <a href="about.php" class="btn btn-outline btn-sm">Discover the club <i class="bi bi-arrow-right"></i></a>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- WHY JOIN — membership benefits -->
<section class="bg-warm home-benefits">
  <div class="wrap">
    <div class="home-section-head reveal">
      <div>
        <div class="label" style="margin-bottom:.65rem">Your next chapter starts here</div>
        <h2 class="t-lg">A community that helps you <em>level up</em></h2>
      </div>
      <p>From your first workshop to your first portfolio piece, get support and opportunities to keep moving forward.</p>
    </div>
    <div class="home-benefit-grid">
        <?php foreach([
          ['bi-easel2','Hands-on workshops','Free member sessions in web development, design, marketing, and more.'],
          ['bi-person-workspace','Freelancing guidance','Learn how to find opportunities and present your skills professionally.'],
          ['bi-patch-check','Certificates','Earn a certificate when you complete an eligible workshop.'],
          ['bi-diagram-3','A supportive network','Meet students, alumni, and mentors who share what they know.'],
          ['bi-mic','Events and talks','Join bootcamps, industry talks, and community meetups.'],
        ] as $i=>[$icon,$title,$description]): ?>
        <article class="home-benefit-card reveal" style="transition-delay:<?=$i*.06?>s">
          <span><i class="bi <?=$icon?>"></i></span>
          <h3><?=$title?></h3>
          <p><?=$description?></p>
          <i class="bi bi-arrow-up-right home-benefit-arrow" aria-hidden="true"></i>
        </article>
        <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="home-testimonials">
  <div class="wrap">
    <div class="home-testimonial-heading reveal">
      <div class="label" style="margin-bottom:.65rem">People make the club</div>
      <h2 class="t-lg">Stories from the <em>community</em></h2>
    </div>
    <div class="home-testimonial-grid">
      <?php foreach([
        ['"AJira changed my trajectory. Six months in, I was earning KES 15,000/month on Fiverr — still in second year."','James Kimani','BSc. IT, Year 3'],
        ['"I presented at the Monthly Meetup and got three clients from the audience. The club opened real opportunities for me."','Amina Hassan','Diploma IT, Year 2'],
        ['"Built my portfolio, learned SEO, closed my first contract in three weeks."','Peter Omondi','BCom, Year 2'],
      ] as $i=>[$q,$n,$r]): ?>
      <article class="home-testimonial-card reveal" style="transition-delay:<?=$i*.08?>s">
        <div class="home-quote-mark" aria-hidden="true">“</div>
        <p><?=$q?></p>
        <div class="home-quote-author">
          <span class="home-quote-avatar" aria-hidden="true"><?=e(strtoupper($n[0]))?></span>
          <div><strong><?=$n?></strong><span><?=$r?></span></div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <p class="home-testimonial-note">Member stories are shared as provided by the club.</p>
  </div>
</section>
     
<!-- CTA -->
<section style="padding:3rem 0 5rem" class="bg-warm">
  <div class="wrap">
    <div class="home-join-panel reveal">
      <div style="position:absolute;top:0;left:0;right:0;height:2px;background:var(--gold)"></div>
      <div style="position:absolute;inset:0;background:url('data:image/svg+xml,%3Csvg width=40 height=40 viewBox=0 0 40 40 xmlns=http://www.w3.org/2000/svg%3E%3Cg fill=%23ffffff fill-opacity=0.02%3E%3Cpath d=M0 40L40 0H20L0 20M40 40V20L20 40/%3E%3C/g%3E%3C/svg%3E');pointer-events:none"></div>
      <div class="home-join-copy">
        <div class="label" style="color:rgba(212,168,67,.55);margin-bottom:1rem">Ready to start?</div>
        <h2 style="font-family:var(--font-display);font-size:clamp(1.85rem,5vw,3rem);font-weight:300;color:white;letter-spacing:-.03em;line-height:1.08;margin-bottom:.85rem">
          Join AJira Digital Club<br><em style="color:var(--gold-2)">today.</em>
        </h2>
        <p style="color:rgba(255,255,255,.4);margin-bottom:2rem;font-size:.92rem;line-height:1.75;max-width:380px">Complete the registration form and the club administrator will review and activate your membership.</p>
        <div class="home-join-actions">
          <a href="join.php" class="btn btn-gold btn-full btn-lg" style="max-width:300px"><i class="bi bi-lightning-charge-fill"></i> Register Now</a>
          <a href="https://chat.whatsapp.com/Lu87qER9F2FKC11jTCktrO?s=cl&amp;p=a&amp;mlu=4&amp;ilr=4" target="_blank" rel="noopener noreferrer" class="btn btn-ghost btn-full" style="max-width:300px"><i class="bi bi-whatsapp" style="color:#4ade80"></i> Join WhatsApp Group</a>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
@media(min-width:860px){
  section[style*="min-height:100svh"] .wrap{padding-bottom:5rem}
}
</style>
<?php include '_footer.php'; ?>
