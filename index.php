<?php
$pageTitle = 'Home';
$pageDesc  = 'AJira Digital Club — official student club at Zetech University, Ruiru. Join 120+ members building digital careers.';
include '_head.php';
?>

<!-- ═══════ HERO ═══════ -->
<section style="min-height:100svh;display:flex;flex-direction:column;justify-content:flex-end;padding:0;background:var(--forest);overflow:hidden;position:relative">

  <!-- Background image -->
  <div style="position:absolute;inset:0;z-index:0">
    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=1400&q=80"
         alt="" style="width:100%;height:100%;object-fit:cover;display:block;opacity:.18;filter:grayscale(30%)">
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
      AJira Digital Club trains Zetech students in web development, design, marketing, and freelancing — skills that pay, while you're still on campus.
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

<!-- WHAT WE DO — numbered editorial grid -->
<section class="bg-warm">
  <div class="wrap">
    <div style="display:grid;grid-template-columns:1fr;gap:1rem;margin-bottom:3rem">
      <div style="display:flex;align-items:center;gap:.75rem" class="reveal">
        <div style="height:1px;background:var(--border);flex:1"></div>
        <span class="label">What we do</span>
        <div style="height:1px;background:var(--border);flex:1"></div>
      </div>
      <h2 class="t-lg reveal d1" style="max-width:480px">Building skills that <em>actually pay</em></h2>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.85rem">
      <?php foreach([
        ['01','Web Development','HTML, CSS, JavaScript, PHP — build real websites and web apps from scratch.'],
        ['02','Graphic Design','Canva, Figma, Photoshop — create brand identities, posters, and digital content.'],
        ['03','Digital Marketing','SEO, social strategy, content creation — grow brands online for real clients.'],
        ['04','Freelancing','Upwork, Fiverr, local platforms — earn from your skills before you graduate.'],
      ] as $i=>[$n,$t,$d]): ?>
      <div class="reveal" style="transition-delay:<?=$i*.07?>s">
        <div style="background:white;border:1px solid var(--border);border-radius:var(--r-lg);padding:1.5rem;height:100%;box-shadow:var(--sh-xs);transition:transform var(--dur) var(--spring),box-shadow var(--dur)" onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='var(--sh-md)'" onmouseleave="this.style.transform='';this.style.boxShadow=''">
          <div style="font-family:var(--font-display);font-size:2rem;font-weight:300;color:var(--stone);letter-spacing:-.04em;margin-bottom:.75rem;line-height:1"><?=$n?></div>
          <div style="font-family:var(--font-display);font-size:1rem;font-weight:400;color:var(--ink);margin-bottom:.4rem"><?=$t?></div>
          <div style="font-size:.8rem;color:var(--muted);line-height:1.65"><?=$d?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- STATS -->
<section style="padding:3rem 0">
  <div class="wrap">
    <div class="stats-band reveal">
      <div class="stat-block"><div class="stat-num" data-n="120" data-s="+">0</div><div class="stat-lbl">Members</div></div>
      <div class="stat-block"><div class="stat-num" data-n="26" data-s="+">0</div><div class="stat-lbl">Events</div></div>
      <div class="stat-block"><div class="stat-num" data-n="4" data-s="">0</div><div class="stat-lbl">Skill Tracks</div></div>
      <div class="stat-block"><div class="stat-num" data-n="3" data-s="">0</div><div class="stat-lbl">Years</div></div>
    </div>
  </div>
</section>

<!-- UPCOMING EVENTS — editorial list -->
<section>
  <div class="wrap">
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:2.5rem;flex-wrap:wrap;gap:.75rem">
      <div>
        <div class="label reveal" style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem"><i class="bi bi-calendar3"></i> Schedule</div>
        <h2 class="t-lg reveal d1">Upcoming <em>events</em></h2>
      </div>
      <a href="events.php" class="btn btn-outline reveal d2">View all →</a>
    </div>
    <div style="border-top:1px solid var(--border)">
      <?php foreach([
        ['Jun 21','Sat','Digital Skills Bootcamp','Hands-on web dev, design & digital marketing.','Lab 3, Main Campus'],
        ['Jun 27','Fri','Freelancing 101 Workshop','Land your first client on Upwork and Fiverr.','Block C Lecture Hall'],
        ['Jul 5', 'Sat','Monthly Meetup','Networking, project showcase and peer feedback.','Online — Zoom'],
      ] as $i=>[$date,$day,$title,$desc,$loc]): ?>
      <div class="reveal" style="transition-delay:<?=$i*.07?>s;display:flex;align-items:center;gap:1.25rem;padding:1.25rem 0;border-bottom:1px solid var(--border)">
        <!-- Date block -->
        <div style="width:52px;flex-shrink:0;text-align:center">
          <div style="font-family:var(--font-display);font-size:1.5rem;font-weight:300;color:var(--ink);line-height:1;letter-spacing:-.03em"><?=strtok($date,' ')?></div>
          <div style="font-size:.6rem;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:var(--muted)"><?=$day?></div>
        </div>
        <!-- Thin rule -->
        <div style="width:1px;height:40px;background:var(--border);flex-shrink:0"></div>
        <!-- Info -->
        <div style="flex:1;min-width:0">
          <div style="font-family:var(--font-display);font-size:1rem;font-weight:400;color:var(--ink);margin-bottom:.2rem"><?=$title?></div>
          <div style="font-size:.78rem;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?=$desc?></div>
        </div>
        <!-- Location pill -->
        <div style="font-size:.72rem;color:var(--muted);display:flex;align-items:center;gap:.3rem;flex-shrink:0;display:none" class="loc-pill">
          <i class="bi bi-geo-alt" style="color:var(--gold)"></i><?=$loc?>
        </div>
        <a href="events.php" style="color:var(--muted);text-decoration:none;font-size:.9rem;flex-shrink:0"><i class="bi bi-arrow-right"></i></a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- WHY JOIN — editorial feature list -->
<section class="bg-warm">
  <div class="wrap">
    <div style="display:grid;grid-template-columns:1fr;gap:3rem">
      <div>
        <div class="label reveal" style="margin-bottom:.75rem">Membership benefits</div>
        <h2 class="t-lg reveal d1">Membership that opens <em>real doors</em></h2>
      </div>
      <div style="display:flex;flex-direction:column;gap:0;border-top:1px solid var(--border)">
        <?php foreach([
          ['Hands-on workshops every semester','Free for members — web dev, design, marketing, and more. No theory, just projects.'],
          ['Freelancing coaching','We get you to your first paying client on Upwork or Fiverr.'],
          ['AJira certificates','Complete a workshop, earn a certificate. Real value for your CV.'],
          ['Professional network','120+ students, alumni, and mentors. Connections that become clients.'],
          ['Exclusive events','Priority access to bootcamps, industry talks, and career fairs.'],
        ] as $i=>[$t,$d]): ?>
        <div class="reveal" style="transition-delay:<?=$i*.06?>s;display:flex;align-items:flex-start;gap:1.25rem;padding:1.25rem 0;border-bottom:1px solid var(--border)">
          <div style="width:24px;height:24px;border-radius:50%;background:var(--gold-pale);border:1px solid rgba(212,168,67,.3);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:.15rem">
            <i class="bi bi-check" style="color:var(--gold);font-size:.75rem;font-weight:700"></i>
          </div>
          <div style="flex:1">
            <div style="font-weight:600;font-size:.92rem;color:var(--ink);margin-bottom:.2rem"><?=$t?></div>
            <div style="font-size:.8rem;color:var(--muted);line-height:1.65"><?=$d?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- TESTIMONIALS — pull quotes editorial -->
<section>
  <div class="wrap">
    <div class="label reveal" style="text-align:center;margin-bottom:2.5rem">What members say</div>
    <div style="display:flex;flex-direction:column;gap:1.25rem">
      <?php foreach([
        ['"AJira changed my trajectory. Six months in, I was earning KES 15,000/month on Fiverr — still in second year."','James Kimani','BSc. IT, Year 3','photo-1507003211169-0a1dd7228f2d'],
        ['"I presented at the Monthly Meetup and got three clients from the audience. The club opened real opportunities for me."','Amina Hassan','Diploma IT, Year 2','photo-1494790108377-be9c29b29330'],
        ['"Built my portfolio, learned SEO, closed my first contract in three weeks."','Peter Omondi','BCom, Year 2','photo-1472099645785-5658abf4ff4e'],
      ] as $i=>[$q,$n,$r,$ph]): ?>
      <div class="reveal" style="transition-delay:<?=$i*.08?>s;background:white;border:1px solid var(--border);border-radius:var(--r-xl);padding:1.75rem;box-shadow:var(--sh-xs)">
        <div style="font-family:var(--font-display);font-size:2.5rem;line-height:.85;color:var(--stone);margin-bottom:.5rem;font-weight:300">"</div>
        <p style="font-family:var(--font-display);font-size:clamp(1rem,2.5vw,1.2rem);font-weight:300;color:var(--ink);line-height:1.55;font-style:italic;margin-bottom:1.25rem"><?=$q?></p>
        <div style="display:flex;align-items:center;gap:.75rem;padding-top:1rem;border-top:1px solid var(--border)">
          <img src="https://images.unsplash.com/<?=$ph?>?w=80&q=80" alt="<?=$n?>" style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:1.5px solid var(--border)" loading="lazy">
          <div><div style="font-weight:600;font-size:.85rem;color:var(--ink)"><?=$n?></div><div style="font-size:.73rem;color:var(--muted)"><?=$r?></div></div>
          <div style="margin-left:auto;color:var(--gold);font-size:.7rem;letter-spacing:1.5px">★★★★★</div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
     
<!-- CTA -->
<section style="padding:3rem 0 5rem" class="bg-warm">
  <div class="wrap">
    <div class="reveal" style="background:var(--forest);border-radius:var(--r-xl);padding:3rem 1.75rem;position:relative;overflow:hidden;box-shadow:var(--sh-xl)">
      <div style="position:absolute;top:0;left:0;right:0;height:2px;background:var(--gold)"></div>
      <div style="position:absolute;inset:0;background:url('data:image/svg+xml,%3Csvg width=40 height=40 viewBox=0 0 40 40 xmlns=http://www.w3.org/2000/svg%3E%3Cg fill=%23ffffff fill-opacity=0.02%3E%3Cpath d=M0 40L40 0H20L0 20M40 40V20L20 40/%3E%3C/g%3E%3C/svg%3E');pointer-events:none"></div>
      <div style="position:relative;z-index:1">
        <div class="label" style="color:rgba(212,168,67,.55);margin-bottom:1rem">Ready to start?</div>
        <h2 style="font-family:var(--font-display);font-size:clamp(1.85rem,5vw,3rem);font-weight:300;color:white;letter-spacing:-.03em;line-height:1.08;margin-bottom:.85rem">
          Join AJira Digital Club<br><em style="color:var(--gold-2)">today.</em>
        </h2>
        <p style="color:rgba(255,255,255,.4);margin-bottom:2rem;font-size:.92rem;line-height:1.75;max-width:380px">Complete the registration form and the club administrator will review and activate your membership.</p>
        <div style="display:flex;flex-direction:column;gap:.65rem">
          <a href="join.php" class="btn btn-gold btn-full btn-lg" style="max-width:300px"><i class="bi bi-lightning-charge-fill"></i> Register Now</a>
          <a href="https://chat.whatsapp.com/YOUR-LINK-HERE" target="_blank" class="btn btn-ghost btn-full" style="max-width:300px"><i class="bi bi-whatsapp" style="color:#4ade80"></i> Join WhatsApp Group</a>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
@media(min-width:640px){
  .loc-pill{display:flex!important}
}
@media(min-width:860px){
  section[style*="min-height:100svh"] .wrap{padding-bottom:5rem}
}
</style>
<?php include '_footer.php'; ?>
