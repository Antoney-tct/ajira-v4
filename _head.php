<?php
require_once __DIR__.'/config/bootstrap.php';
$pageTitle = $pageTitle ?? 'AJira Digital Club';
$pageDesc  = $pageDesc  ?? 'Official student digital club at Zetech University — training Kenya\'s next generation of digital professionals.';
$rootPath  = $rootPath  ?? './';
$heroImages = [
  'About Us' => 'photo-1522202176988-66273c2fd55f',
  'Our Team' => 'photo-1517048676732-d65bc937f952',
  'Projects' => 'photo-1498050108023-c5249f4df085',
  'Gallery' => 'photo-1531482615713-2afd69097998',
  'Events' => 'photo-1540575467063-178a50c2df87',
  'Join the Club' => 'photo-1523240795612-9a054b0db644',
  'Contact Us' => 'photo-1497366754035-f200968a6e72',
  'Notifications' => 'photo-1511632765486-a01980e01a18',
];
$heroImage = $heroImages[$pageTitle] ?? '';
$notificationCount=0; $notificationItems=[];
try{
  $notificationWhere=isMember()?"n.audience IN ('public','members')":"n.audience='public'";
  if(isMember()){
    $memberId=(int)$_SESSION['member_id'];
    $count=db()->prepare("SELECT COUNT(*) FROM notifications n WHERE $notificationWhere AND (n.expires_at IS NULL OR n.expires_at>NOW()) AND n.created_at>=DATE_SUB(NOW(),INTERVAL 30 DAY) AND NOT EXISTS (SELECT 1 FROM notification_reads r WHERE r.notification_id=n.id AND r.member_id=?)");
    $count->execute([$memberId]); $notificationCount=(int)$count->fetchColumn();
    $items=db()->prepare("SELECT n.id,n.title,n.message,n.created_at,NOT EXISTS (SELECT 1 FROM notification_reads r WHERE r.notification_id=n.id AND r.member_id=?) AS is_unread FROM notifications n WHERE $notificationWhere AND (n.expires_at IS NULL OR n.expires_at>NOW()) ORDER BY n.created_at DESC LIMIT 5");
    $items->execute([$memberId]); $notificationItems=$items->fetchAll();
  }else{
    $viewed=array_map('intval',$_SESSION['viewed_notifications']??[]);
    $all=db()->query("SELECT n.id FROM notifications n WHERE $notificationWhere AND (n.expires_at IS NULL OR n.expires_at>NOW()) AND n.created_at>=DATE_SUB(NOW(),INTERVAL 30 DAY)")->fetchAll(PDO::FETCH_COLUMN);
    $notificationCount=count(array_diff(array_map('intval',$all),$viewed));
    $notificationItems=db()->query("SELECT n.id,n.title,n.message,n.created_at FROM notifications n WHERE $notificationWhere AND (n.expires_at IS NULL OR n.expires_at>NOW()) ORDER BY n.created_at DESC LIMIT 5")->fetchAll();
    foreach($notificationItems as &$item) $item['is_unread']=!in_array((int)$item['id'],$viewed,true);
    unset($item);
  }
}catch(Throwable){}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="description" content="<?= htmlspecialchars($pageDesc) ?>">
<meta name="theme-color" content="#073B2E">
<meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?> — AJira Digital Club">
<meta property="og:description" content="<?= htmlspecialchars($pageDesc) ?>">
<meta property="og:type" content="website">
<title><?= htmlspecialchars($pageTitle) ?> — AJira Digital Club | Zetech University</title>
<link rel="icon" type="image/svg+xml" href="<?= $rootPath ?>assets/images/favicon.svg">
<link rel="apple-touch-icon" href="<?= $rootPath ?>assets/images/favicon.svg">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?= $rootPath ?>assets/css/main.css">
</head>
<body<?= $heroImage ? ' style="--page-hero-image:url(https://images.unsplash.com/'.$heroImage.'?auto=format&amp;fit=crop&amp;w=1800&amp;q=85)"' : '' ?>>

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
<a href="https://chat.whatsapp.com/Lu87qER9F2FKC11jTCktrO?s=cl&amp;p=a&amp;mlu=4&amp;ilr=4" target="_blank" rel="noopener noreferrer" class="wa" aria-label="Join WhatsApp group"><i class="bi bi-whatsapp"></i></a>

<!-- TOPBAR -->
<?php if(!isMember()): ?>
<div id="topbar">
  <span class="tb-dot"></span>
  <a href="<?= $rootPath ?>join.php">Join us →</a>
</div>
<?php endif; ?>

<!-- NAVBAR -->
<nav id="navbar">
  <div class="wrap">
    <div class="nav-inner">
      <a href="<?= $rootPath ?>index.php" class="nav-logo" aria-label="AJira Digital Club home">
        <img class="nav-logo-image" src="<?= $rootPath ?>assets/images/ajira-logo.svg" alt="AJira Digital Club — Zetech University">
      </a>
      <ul class="nav-links">
        <li><a href="<?= $rootPath ?>index.php">Home</a></li>
        <li><a href="<?= $rootPath ?>about.php">About</a></li>
        <li><a href="<?= $rootPath ?>team.php">Team</a></li>
        <li><a href="<?= $rootPath ?>events.php">Events</a></li>
        <li><a href="<?= $rootPath ?>projects.php">Projects</a></li>
        <li><a href="<?= $rootPath ?>gallery.php">Gallery</a></li>
        <li><a href="<?= $rootPath ?>contact.php">Contact</a></li>
      </ul>
      <div class="nav-actions">
        <div class="notification-menu">
          <button type="button" class="header-icon-link notification-toggle" id="notificationToggle" aria-label="View notifications" title="Notifications" aria-controls="notificationDropdown" aria-expanded="false">
            <i class="bi bi-bell"></i><?php if($notificationCount): ?><span class="header-notification-count"><?=$notificationCount>99?'99+':$notificationCount?></span><?php endif; ?>
          </button>
          <div class="notification-dropdown" id="notificationDropdown" aria-labelledby="notificationToggle" hidden>
            <div class="notification-dropdown-head"><strong>Latest updates</strong><a href="<?= $rootPath ?>notifications.php">View all</a></div>
            <?php if(!$notificationItems): ?>
            <div class="notification-dropdown-empty"><i class="bi bi-bell-slash"></i><span>You’re all caught up.</span></div>
            <?php else: foreach($notificationItems as $item): ?>
            <a class="notification-dropdown-item<?=!empty($item['is_unread'])?' is-unread':''?>" href="<?= $rootPath ?>notifications.php">
              <span class="notification-item-dot" aria-hidden="true"></span>
              <span class="notification-item-copy"><strong><?=e($item['title'])?></strong><span><?=e($item['message'])?></span><time><?=e(date('M j, g:i A',strtotime($item['created_at'])))?></time></span>
            </a>
            <?php endforeach; endif; ?>
            <a class="notification-dropdown-footer" href="<?= $rootPath ?>notifications.php">Open notification center <i class="bi bi-arrow-right"></i></a>
          </div>
        </div>
        <?php if(isMember()): ?>
        <a href="<?= $rootPath ?>member_messages.php" class="header-icon-link" aria-label="View messages" title="My messages"><i class="bi bi-chat-dots"></i></a>
        <a href="<?= $rootPath ?>member_profile.php" class="btn-nav-cta"><i class="bi bi-person-circle"></i> My Profile</a>
        <?php else: ?>
        <a href="<?= $rootPath ?>join.php" class="btn-nav-cta">Join Now</a>
        <a href="<?= $rootPath ?>member_login.php" class="btn-nav-cta btn-nav-login">Login</a>
        <?php endif; ?>
      </div>
      <button class="nav-toggle" id="navTog" aria-label="Open menu" aria-controls="drawer" aria-expanded="false"><span></span><span></span><span></span></button>
    </div>
  </div>
</nav>

<!-- MOBILE DRAWER -->
<div class="drawer" id="drawer">
  <div class="drawer-bg"></div>
  <div class="drawer-panel">
    <div class="drawer-head">
      <a href="<?= $rootPath ?>index.php" class="nav-logo" aria-label="AJira Digital Club home"><img class="drawer-logo-image" src="<?= $rootPath ?>assets/images/ajira-icon-only.svg" alt="AJira Digital Club"></a>
      <button class="drawer-x" id="drwX" aria-label="Close menu"><i class="bi bi-x-lg"></i></button>
    </div>
    <nav class="drawer-nav">
      <a href="<?= $rootPath ?>index.php" aria-label="Home" title="Home"><i class="bi bi-house"></i> Home</a>
      <a href="<?= $rootPath ?>about.php" aria-label="About Us" title="About Us"><i class="bi bi-info-circle"></i> About Us</a>
      <a href="<?= $rootPath ?>team.php" aria-label="Our Team" title="Our Team"><i class="bi bi-people"></i> Our Team</a>
      <a href="<?= $rootPath ?>events.php" aria-label="Events" title="Events"><i class="bi bi-calendar-event"></i> Events</a>
      <a href="<?= $rootPath ?>projects.php" aria-label="Projects" title="Projects"><i class="bi bi-kanban"></i> Projects</a>
      <a href="<?= $rootPath ?>gallery.php" aria-label="Gallery" title="Gallery"><i class="bi bi-images"></i> Gallery</a>
      <a href="<?= $rootPath ?>contact.php" aria-label="Contact" title="Contact"><i class="bi bi-chat-dots"></i> Contact</a>
    </nav>
    <div class="drawer-cta">
      <?php if(isMember()): ?>
      <a href="<?= $rootPath ?>notifications.php" class="btn btn-outline btn-full" aria-label="Notifications<?php if($notificationCount): ?> (<?=$notificationCount>99?'99+':$notificationCount?>)<?php endif; ?>" title="Notifications"><i class="bi bi-bell"></i> Notifications<?php if($notificationCount): ?> (<?=$notificationCount>99?'99+':$notificationCount?>)<?php endif; ?></a>
      <a href="<?= $rootPath ?>member_messages.php" class="btn btn-outline btn-full"><i class="bi bi-chat-dots"></i> My messages</a>
      <a href="<?= $rootPath ?>member_profile.php" class="btn btn-outline btn-full" aria-label="My Profile" title="My Profile"><i class="bi bi-person-circle"></i> My Profile</a>
      <?php else: ?>
      <a href="<?= $rootPath ?>notifications.php" class="btn btn-outline btn-full" aria-label="Notifications<?php if($notificationCount): ?> (<?=$notificationCount>99?'99+':$notificationCount?>)<?php endif; ?>" title="Notifications"><i class="bi bi-bell"></i> Notifications<?php if($notificationCount): ?> (<?=$notificationCount>99?'99+':$notificationCount?>)<?php endif; ?></a>
      <a href="<?= $rootPath ?>join.php" class="btn btn-forest btn-full btn-lg" aria-label="Join Now" title="Join Now"><i class="bi bi-lightning-charge-fill"></i> Join Now</a>
      <a href="<?= $rootPath ?>member_login.php" class="btn btn-outline btn-full" aria-label="Login" title="Login"><i class="bi bi-person-circle"></i> Login</a>
      <?php endif; ?>
    </div>
  </div>
</div>
