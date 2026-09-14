<?php
$pageTitle='Gallery'; include '_head.php';
$all=[
  ['photo-1522202176988-66273c2fd55f','events'],['photo-1517048676732-d65bc937f952','team'],
  ['photo-1503676260728-1c00da094a0b','events'],['photo-1540575467063-178a50c2df87','events'],
  ['photo-1531482615713-2afd69097998','events'],['photo-1552664730-d307ca884978','team'],
  ['photo-1519389950473-47ba0277781c','campus'],['photo-1523240795612-9a054b0db644','campus'],
  ['photo-1491438590914-bc09fcaaf77a','team'],['photo-1529156069898-49953e39b3ac','team'],
  ['photo-1498050108023-c5249f4df085','events'],['photo-1515187029135-18ee286d815b','events'],
];
$cats=['all'=>$all,'events'=>[],'team'=>[],'campus'=>[]];
foreach($all as $p) $cats[$p[1]][]=$p;
?>
<div class="page-hero">
  <div class="wrap page-hero-inner">
    <nav class="breadcrumb"><a href="index.php">Home</a> <i class="bi bi-chevron-right" style="font-size:.55rem"></i> Gallery</nav>
    <div class="pg-label"><i class="bi bi-images"></i> Club Life</div>
    <h1>Photo <em>Gallery</em></h1>
    <p>Real moments from workshops, bootcamps and community events. Tap any photo to enlarge.</p>
  </div>
</div>
<section>
  <div class="wrap">
    <div class="tab-group">
      <div class="tab-nav reveal">
        <button class="tab-btn active" data-tab="g-all">All (<?=count($all)?>)</button>
        <button class="tab-btn" data-tab="g-ev">Events</button>
        <button class="tab-btn" data-tab="g-tm">Team</button>
        <button class="tab-btn" data-tab="g-ca">Campus</button>
      </div>
      <?php foreach(['g-all'=>$cats['all'],'g-ev'=>$cats['events'],'g-tm'=>$cats['team'],'g-ca'=>$cats['campus']] as $id=>$items): ?>
      <div id="<?=$id?>" class="tab-pane" data-d="block" style="display:<?=$id==='g-all'?'block':'none'?>">
        <div class="masonry reveal">
          <?php foreach($items as [$pid,$_]): $url="https://images.unsplash.com/$pid"; ?>
          <div class="g-item" data-lb="<?=$url?>?w=1400&q=80"><img src="<?=$url?>?w=600&q=80" alt="Club photo" loading="lazy"><div class="g-hover"><i class="bi bi-zoom-in"></i></div></div>
          <?php endforeach; ?>
        </div>
        <?php if(empty($items)): ?><div style="text-align:center;padding:3rem;color:var(--muted)">No photos yet.</div><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <p style="text-align:center;color:var(--muted);font-size:.8rem;margin-top:1.25rem" class="reveal">Have photos from our events? <a href="contact.php" style="color:var(--sage);font-weight:600;text-decoration:none">Send them →</a></p>
  </div>
</section>
<?php include '_footer.php'; ?>
