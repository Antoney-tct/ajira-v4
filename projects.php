<?php
declare(strict_types=1);
require_once __DIR__.'/config/bootstrap.php';
$projects=require __DIR__.'/config/project-showcase.php';
$projectTypes=array_values(array_unique(array_column($projects,'type')));
$projectStatuses=array_values(array_unique(array_column($projects,'status')));
sort($projectTypes,SORT_NATURAL|SORT_FLAG_CASE);
sort($projectStatuses,SORT_NATURAL|SORT_FLAG_CASE);
$pageTitle='Projects'; include '_head.php';
?>
<div class="page-hero">
  <div class="wrap page-hero-inner">
    <nav class="breadcrumb" aria-label="Breadcrumb"><ol><li><a href="index.php"><i class="bi bi-house-door-fill" aria-hidden="true"></i>Home</a></li><li class="breadcrumb-separator" aria-hidden="true"><i class="bi bi-chevron-right"></i></li><li aria-current="page">Projects</li></ol></nav>
    <div class="pg-label"><i class="bi bi-kanban"></i> Work by the club</div>
    <h1>Ideas into <em>projects</em></h1>
    <p>Explore the ideas, collaborations, and community work our members are bringing to life.</p>
  </div>
</div>
<main>
  <section class="projects-page" aria-labelledby="projects-heading">
    <div class="wrap">
      <header class="projects-intro">
        <div>
          <div class="label">Selected work</div>
          <h2 class="t-lg" id="projects-heading">Made with curiosity.<br><em>Built to make a difference.</em></h2>
        </div>
        <p>From digital products to campus initiatives, each project is an opportunity to learn by doing and create something useful together.</p>
      </header>

      <div class="project-filters" aria-label="Filter projects">
        <label class="project-search">
          <i class="bi bi-search" aria-hidden="true"></i>
          <span class="sr-only">Search projects</span>
          <input type="search" id="project-search" placeholder="Search projects" autocomplete="off">
        </label>
        <label class="project-select">
          <span>Project type</span>
          <select id="project-type-filter">
            <option value="">All types</option>
            <?php foreach($projectTypes as $type): ?>
            <option value="<?=e(strtolower($type))?>"><?=e($type)?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label class="project-select">
          <span>Status</span>
          <select id="project-status-filter">
            <option value="">All statuses</option>
            <?php foreach($projectStatuses as $status): ?>
            <option value="<?=e(strtolower($status))?>"><?=e($status)?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <button class="project-reset" id="project-reset" type="button"><i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i> Clear</button>
      </div>

      <div class="project-results" aria-live="polite" aria-atomic="true">
        <span id="project-result-count"><?=count($projects)?> <?=count($projects)===1?'project':'projects'?></span>
        <span>Choose a project to see what the team is working on.</span>
      </div>

      <div class="projects-grid" id="projects-grid">
        <?php foreach($projects as $i=>$project): ?>
        <article class="project-card reveal"
          data-project-card
          data-project-type="<?=e(strtolower($project['type']))?>"
          data-project-status="<?=e(strtolower($project['status']))?>">
          <div class="project-card-image">
            <img src="<?=e($project['image'])?>" alt="" loading="lazy">
            <span class="project-status"><i class="bi bi-circle-fill" aria-hidden="true"></i><?=e($project['status'])?></span>
            <span class="project-image-type"><?=e($project['type'])?></span>
          </div>
          <div class="project-card-body">
            <h3><?=e($project['title'])?></h3>
            <p><?=e($project['description'])?></p>
            <div class="project-tools"><i class="bi bi-tools" aria-hidden="true"></i><span><?=e($project['tools'])?></span></div>
            <button class="project-detail-trigger"
              type="button"
              aria-haspopup="dialog"
              data-project-title="<?=e($project['title'])?>"
              data-project-type="<?=e($project['type'])?>"
              data-project-status="<?=e($project['status'])?>"
              data-project-description="<?=e($project['description'])?>"
              data-project-tools="<?=e($project['tools'])?>"
              data-project-image="<?=e($project['image'])?>">
              Explore project <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
            </button>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
      <div class="project-empty" id="project-empty" hidden>
        <i class="bi bi-search" aria-hidden="true"></i>
        <h3>No matching projects</h3>
        <p>Try another search or clear the selected filters.</p>
      </div>
    </div>
  </section>

  <section class="projects-cta" aria-labelledby="projects-cta-heading">
    <div class="wrap projects-cta-inner">
      <div>
        <div class="label">Bring an idea</div>
        <h2 class="t-lg" id="projects-cta-heading">Have a problem worth <em>solving?</em></h2>
        <p>Share an idea, join a working group, or partner with the club to build something useful.</p>
      </div>
      <a href="contact.php" class="btn btn-gold btn-lg"><i class="bi bi-arrow-up-right" aria-hidden="true"></i> Start a conversation</a>
    </div>
  </section>
</main>

<dialog class="project-dialog" id="project-dialog" aria-labelledby="project-dialog-title">
  <div class="project-dialog-content">
    <form method="dialog" class="project-dialog-dismiss">
      <button type="submit" aria-label="Close project details"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
    </form>
    <img class="project-dialog-image" id="project-dialog-image" src="" alt="">
    <div class="project-dialog-body">
      <div class="project-dialog-meta"><span id="project-dialog-type"></span><span id="project-dialog-status"></span></div>
      <h2 id="project-dialog-title"></h2>
      <p id="project-dialog-description"></p>
      <div class="project-dialog-tools"><i class="bi bi-tools" aria-hidden="true"></i><span id="project-dialog-tools"></span></div>
      <a href="contact.php" class="btn btn-forest">Get involved <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
    </div>
  </div>
</dialog>
<?php include '_footer.php'; ?>
