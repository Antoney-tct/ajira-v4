<?php
require_once __DIR__.'/config/bootstrap.php';
$pageTitle = $pageTitle ?? 'AJira Digital Club';
$pageDesc  = $pageDesc  ?? 'Official student digital club at Zetech University — training Kenya\'s next generation of digital professionals.';
$rootPath  = $rootPath  ?? './';
$notificationCount=0;
try{
  $notificationWhere=isMember()?"n.audience IN ('public','members')":"n.audience='public'";
  if(isMember()) $notificationCount=(int)db()->query("SELECT COUNT(*) FROM notifications n WHERE $notificationWhere AND (n.expires_at IS NULL OR n.expires_at>NOW()) AND n.created_at>=DATE_SUB(NOW(),INTERVAL 30 DAY) AND NOT EXISTS (SELECT 1 FROM notification_reads r WHERE r.notification_id=n.id AND r.member_id=".(int)$_SESSION['member_id'].")")->fetchColumn();
  else { $all=db()->query("SELECT n.id FROM notifications n WHERE $notificationWhere AND (n.expires_at IS NULL OR n.expires_at>NOW()) AND n.created_at>=DATE_SUB(NOW(),INTERVAL 30 DAY)")->fetchAll(PDO::FETCH_COLUMN); $viewed=array_map('intval',$_SESSION['viewed_notifications']??[]); $notificationCount=count(array_diff(array_map('intval',$all),$viewed)); }
}catch(Throwable){}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="description" content="<?= htmlspecialchars($pageDesc) ?>">
<meta name="theme-color" content="#0C1A0E">
<meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?> — AJira Digital Club">
<meta property="og:description" content="<?= htmlspecialchars($pageDesc) ?>">
<meta property="og:type" content="website">
<title><?= htmlspecialchars($pageTitle) ?> — AJira Digital Club | Zetech University</title>
<link rel="icon" type="image/svg+xml" href="<?= $rootPath ?>assets/images/favicon.svg">
<link rel="apple-touch-icon" href="<?= $rootPath ?>assets/images/favicon.svg">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,600;1,9..144,300;1,9..144,400&family=Figtree:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?= $rootPath ?>assets/css/main.css">
</head>
<body>

<!-- LIGHTBOX -->
<div id="lb"><button id="lb-close" aria-label="Close"><i class="bi bi-x-lg"></i></button><img id="lb-img" src="" alt=""></div>

<!-- CHATBOT -->
<button id="cb-btn" aria-label="Chat with Ajira Bot"><i class="bi bi-chat-text-fill"></i><span class="dot"></span></button>
<div id="cb-box">
  <div class="cb-head">
    <div class="cb-head-l">
      <div class="cb-av">🤖</div>
      <div><div class="cb-name">Ajira Bot</div><div class="cb-status">Online now</div></div>
    </div>
    <button class="cb-x" id="cb-x" aria-label="Close"><i class="bi bi-x-lg"></i></button>
  </div>
  <div class="cb-msgs" id="cb-msgs"></div>
  <div class="cb-quick">
    <button class="cb-q">How to join?</button>
    <button class="cb-q">How to join?</button>
    <button class="cb-q">Events?</button>
    <button class="cb-q">Skills taught?</button>
  </div>
  <div class="cb-input">
    <input type="text" id="cb-inp" placeholder="Ask anything…" autocomplete="off" aria-label="Chat message">
    <button class="cb-send" id="cb-snd" aria-label="Send"><i class="bi bi-send-fill"></i></button>
  </div>
</div>

<!-- WHATSAPP -->
<a href="https://chat.whatsapp.com/YOUR-LINK-HERE" target="_blank" rel="noopener" class="wa" aria-label="Join WhatsApp group"><i class="bi bi-whatsapp"></i></a>

<!-- TOPBAR -->
<div id="topbar">
  <span><strong>Zetech University, Ruiru</strong> — Innovate · Create · Impact</span>
  <span class="tb-dot"></span>
  <a href="<?= $rootPath ?>join.php">Register as a member →</a>
</div>

<!-- NAVBAR -->
<nav id="navbar">
  <div class="wrap">
    <div class="nav-inner">
      <a href="<?= $rootPath ?>index.php" class="nav-logo" aria-label="AJira Digital Club">
        <div class="logo-mark">AD</div>
        <div><span class="logo-text-name">AJira Digital Club</span><span class="logo-text-tag">Zetech University · Ruiru</span></div>
      </a>
      <ul class="nav-links">
        <li><a href="<?= $rootPath ?>index.php">Home</a></li>
        <li><a href="<?= $rootPath ?>about.php">About</a></li>
        <li><a href="<?= $rootPath ?>team.php">Team</a></li>
        <li><a href="<?= $rootPath ?>events.php">Events</a></li>
        <li><a href="<?= $rootPath ?>projects.php">Projects</a></li>
        <li><a href="<?= $rootPath ?>gallery.php">Gallery</a></li>
        <li><a href="<?= $rootPath ?>contact.php">Contact</a></li>
        <?php if(isMember()): ?>
        <li><a href="<?= $rootPath ?>notifications.php" class="header-icon-link" aria-label="View notifications" title="Notifications"><i class="bi bi-bell"></i><?php if($notificationCount): ?><span class="header-notification-count"><?=$notificationCount>99?'99+':$notificationCount?></span><?php endif; ?></a></li>
        <li><a href="<?= $rootPath ?>member_profile.php" class="btn-nav-cta" aria-label="Open member profile" title="My profile"><i class="bi bi-person-circle"></i></a></li>
        <?php else: ?>
        <li><a href="<?= $rootPath ?>notifications.php" class="header-icon-link" aria-label="View notifications" title="Notifications"><i class="bi bi-bell"></i><?php if($notificationCount): ?><span class="header-notification-count"><?=$notificationCount>99?'99+':$notificationCount?></span><?php endif; ?></a></li>
        <li><a href="<?= $rootPath ?>join.php" class="btn-nav-cta">Join Now</a></li>
        <li><a href="<?= $rootPath ?>member_login.php" class="btn-nav-cta">Login</a></li>
        <?php endif; ?>
      </ul>
      <button class="nav-toggle" id="navTog" aria-label="Open menu"><span></span><span></span><span></span></button>
    </div>
  </div>
</nav>

<!-- MOBILE DRAWER -->
<div class="drawer" id="drawer">
  <div class="drawer-bg"></div>
  <div class="drawer-panel">
    <div class="drawer-head">
      <a href="<?= $rootPath ?>index.php" class="nav-logo"><div class="logo-mark" style="width:30px;height:30px;font-size:.78rem">AD</div><div><span class="logo-text-name" style="font-size:.82rem">AJira Digital Club</span><span class="logo-text-tag">Zetech · Ruiru</span></div></a>
      <button class="drawer-x" id="drwX" aria-label="Close menu"><i class="bi bi-x-lg"></i></button>
    </div>
    <nav class="drawer-nav">
      <a href="<?= $rootPath ?>index.php"><i class="bi bi-house"></i> Home</a>
      <a href="<?= $rootPath ?>about.php"><i class="bi bi-info-circle"></i> About Us</a>
      <a href="<?= $rootPath ?>team.php"><i class="bi bi-people"></i> Our Team</a>
      <a href="<?= $rootPath ?>events.php"><i class="bi bi-calendar-event"></i> Events</a>
      <a href="<?= $rootPath ?>projects.php"><i class="bi bi-kanban"></i> Projects</a>
      <a href="<?= $rootPath ?>gallery.php"><i class="bi bi-images"></i> Gallery</a>
      <a href="<?= $rootPath ?>contact.php"><i class="bi bi-chat-dots"></i> Contact</a>
    </nav>
    <div class="drawer-cta">
      <?php if(isMember()): ?>
      <a href="<?= $rootPath ?>notifications.php" class="btn btn-outline btn-full" style="margin-bottom:.55rem"><i class="bi bi-bell"></i> Notifications<?php if($notificationCount): ?> (<?=$notificationCount>99?'99+':$notificationCount?>)<?php endif; ?></a>
      <a href="<?= $rootPath ?>member_profile.php" class="btn btn-outline btn-full" aria-label="Open member profile" title="My profile"><i class="bi bi-person-circle"></i> My Profile</a>
      <?php else: ?>
      <a href="<?= $rootPath ?>notifications.php" class="btn btn-outline btn-full" style="margin-bottom:.55rem"><i class="bi bi-bell"></i> Notifications<?php if($notificationCount): ?> (<?=$notificationCount>99?'99+':$notificationCount?>)<?php endif; ?></a>
      <a href="<?= $rootPath ?>join.php" class="btn btn-forest btn-full btn-lg"><i class="bi bi-lightning-charge-fill"></i> Join Now</a>
      <a href="<?= $rootPath ?>member_login.php" class="btn btn-outline btn-full" style="margin-top:.55rem"><i class="bi bi-person-circle"></i> Login</a>
      <?php endif; ?>
    </div>
  </div>
</div>
