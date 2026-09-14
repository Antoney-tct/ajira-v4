<?php
declare(strict_types=1);
require_once __DIR__.'/config/bootstrap.php';
$upcoming=[]; $past=[];
try{
  $upcoming=db()->query("SELECT * FROM events WHERE event_date>=NOW() AND is_published=1 ORDER BY event_date ASC LIMIT 6")->fetchAll();
  $past=db()->query("SELECT * FROM events WHERE event_date<NOW() AND is_published=1 ORDER BY event_date DESC LIMIT 8")->fetchAll();
}catch(Throwable){}
if(empty($upcoming)) $upcoming=[
  ['id'=>1,'title'=>'Digital Skills Bootcamp','description'=>'Hands-on training in web development, graphic design, and digital marketing.','event_date'=>date('Y-m-d H:i:s',strtotime('+7 days')),'location'=>'Lab 3, Main Campus','is_online'=>0],
  ['id'=>2,'title'=>'Freelancing 101 Workshop','description'=>'Learn to land your first client on Upwork and Fiverr.','event_date'=>date('Y-m-d H:i:s',strtotime('+14 days')),'location'=>'Block C Lecture Hall','is_online'=>0],
  ['id'=>3,'title'=>'Monthly Meetup','description'=>'Networking, project showcase, and peer feedback.','event_date'=>date('Y-m-d H:i:s',strtotime('+21 days')),'location'=>'Online — Zoom','is_online'=>1],
  ['id'=>4,'title'=>'Digital Marketing Masterclass','description'=>'SEO, social strategy and content creation for Kenyan businesses.','event_date'=>date('Y-m-d H:i:s',strtotime('+30 days')),'location'=>'Student Centre','is_online'=>0],
];
$thumbs=['photo-1540575467063-178a50c2df87','photo-1517245386807-bb43f82c33c4','photo-1522071820081-009f0129c71c','photo-1515187029135-18ee286d815b'];
if(empty($past)) $past=[
  ['title'=>'Web Development Crash Course','event_date'=>date('Y-m-d',strtotime('-30 days')),'attendees'=>34,'location'=>'Lab 2'],
  ['title'=>'Graphic Design for Beginners','event_date'=>date('Y-m-d',strtotime('-60 days')),'attendees'=>41,'location'=>'Studio'],
  ['title'=>'Digital Marketing Masterclass','event_date'=>date('Y-m-d',strtotime('-90 days')),'attendees'=>55,'location'=>'Hall A'],
  ['title'=>'Intro to Freelancing','event_date'=>date('Y-m-d',strtotime('-120 days')),'attendees'=>48,'location'=>'Online'],
];
$pageTitle='Events'; include '_head.php';
?>
<div class="page-hero">
  <div class="wrap page-hero-inner">
    <nav class="breadcrumb"><a href="index.php">Home</a> <i class="bi bi-chevron-right" style="font-size:.55rem"></i> Events</nav>
    <div class="pg-label"><i class="bi bi-calendar3"></i> Programme</div>
    <h1>Club <em>Events</em></h1>
    <p>Workshops, bootcamps and masterclasses to fast-track your digital career. Free for all members.</p>
  </div>
</div>
<section>
  <div class="wrap">
    <div class="tab-group">
      <div class="tab-nav reveal">
        <button class="tab-btn active" data-tab="t-up">Upcoming (<?=count($upcoming)?>)</button>
        <button class="tab-btn" data-tab="t-past">Past Events</button>
      </div>

      <!-- UPCOMING — editorial list style -->
      <div id="t-up" class="tab-pane" data-d="block" style="display:block">
        <div style="border-top:1px solid var(--border)">
          <?php foreach($upcoming as $i=>$ev): ?>
          <div class="reveal" style="transition-delay:<?=$i*.07?>s;padding:1.5rem 0;border-bottom:1px solid var(--border)">
            <div style="display:grid;grid-template-columns:64px 1fr;gap:1.25rem;align-items:start">
              <!-- Date -->
              <div style="background:var(--warm-grey);border:1px solid var(--border);border-radius:var(--r);padding:.65rem .5rem;text-align:center">
                <div style="font-family:var(--font-display);font-size:1.5rem;font-weight:300;color:var(--ink);line-height:1;letter-spacing:-.03em"><?=date('j',strtotime($ev['event_date']))?></div>
                <div style="font-size:.58rem;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)"><?=date('M',strtotime($ev['event_date']))?></div>
              </div>
              <!-- Info -->
              <div>
                <div style="display:flex;flex-wrap:wrap;gap:.5rem;align-items:center;margin-bottom:.5rem">
                  <span class="badge b-green">Free</span>
                  <?php if(!empty($ev['is_online'])): ?><span class="badge b-stone">Online</span><?php endif; ?>
                  <span style="font-size:.72rem;color:var(--dim)"><?=date('g:i A',strtotime($ev['event_date']))?></span>
                </div>
                <div style="font-family:var(--font-display);font-size:1.1rem;font-weight:300;color:var(--ink);margin-bottom:.35rem;letter-spacing:-.01em"><?=e($ev['title'])?></div>
                <?php if(!empty($ev['description'])): ?>
                <p style="font-size:.82rem;color:var(--muted);line-height:1.65;margin-bottom:.65rem"><?=e($ev['description'])?></p>
                <?php endif; ?>
                <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
                  <span style="font-size:.75rem;color:var(--dim);display:flex;align-items:center;gap:.3rem"><i class="bi bi-geo-alt" style="color:var(--gold)"></i><?=e($ev['location']??'TBA')?></span>
                  <a href="join.php" class="btn btn-forest btn-sm"><i class="bi bi-check-circle"></i> RSVP Free</a>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- PAST -->
      <div id="t-past" class="tab-pane" data-d="block" style="display:none">
        <div style="border-top:1px solid var(--border)">
          <?php foreach($past as $i=>$ev): ?>
          <div class="reveal" style="transition-delay:<?=$i*.06?>s;display:flex;align-items:center;gap:1rem;padding:1rem 0;border-bottom:1px solid var(--border)">
            <div style="flex:1;min-width:0">
              <div style="font-weight:600;font-size:.9rem;color:var(--ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?=e($ev['title'])?></div>
              <div style="font-size:.74rem;color:var(--dim);margin-top:.18rem"><?=date('M j, Y',strtotime($ev['event_date']))?><?php if(!empty($ev['attendees'])): ?> · <span style="color:var(--sage);font-weight:600"><?=$ev['attendees']?> attended</span><?php endif; ?></div>
            </div>
            <span class="badge b-stone">Done</span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- WhatsApp nudge -->
    <div class="reveal" style="margin-top:2rem;background:var(--warm-grey);border:1px solid var(--border);border-radius:var(--r-xl);padding:1.4rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem">
      <div>
        <div style="font-weight:600;font-size:.9rem;color:var(--ink)">Never miss an event</div>
        <div style="font-size:.8rem;color:var(--muted)">Join the WhatsApp group — event notices go there first.</div>
      </div>
      <a href="https://chat.whatsapp.com/YOUR-LINK-HERE" target="_blank" class="btn btn-forest btn-sm"><i class="bi bi-whatsapp"></i> Join Group</a>
    </div>
  </div>
</section>
<?php include '_footer.php'; ?>
