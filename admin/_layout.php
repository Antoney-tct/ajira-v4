<?php
declare(strict_types=1);
require_once __DIR__.'/../config/bootstrap.php';
requireAdmin();
if(isset($_GET['logout'])){session_destroy();redirect('login.php');}
$pg=$activePage??'';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="theme-color" content="#073B2E">
<title><?=e($pageTitle??'Admin')?> — AJira Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="icon" type="image/svg+xml" href="../assets/images/favicon.svg">
<link rel="stylesheet" href="../assets/css/main.css">
<style>
body{background:var(--warm-grey);font-family:'Figtree',sans-serif}
body::before{display:none}

/* Layout */
.aw{display:flex;min-height:100vh}

/* Sidebar */
.sb{width:40vw;background:var(--forest);display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;z-index:200;overflow-y:auto;transition:transform .3s var(--spring)}
.sb-brand{padding:1rem 1.1rem .9rem;border-bottom:1px solid rgba(255,255,255,.07);display:flex;align-items:center;gap:.6rem}
.sb-mark{width:32px;height:32px;padding:3px;border-radius:8px;background:white;border:1px solid rgba(255,255,255,.2);object-fit:contain;flex-shrink:0}
.sb-name{font-family:var(--font-display);font-size:.85rem;font-weight:300;color:white;display:block;line-height:1.15}
.sb-sub{font-size:.54rem;color:rgba(255,255,255,.22);text-transform:uppercase;letter-spacing:.07em;display:block}
.sb-sec{font-size:.54rem;font-weight:600;text-transform:uppercase;letter-spacing:.13em;color:rgba(255,255,255,.18);padding:.9rem 1.1rem .3rem;display:block}
.sb-a{display:flex;align-items:center;gap:.55rem;padding:.55rem 1.1rem;color:rgba(255,255,255,.42);text-decoration:none;font-size:.8rem;font-weight:500;transition:all .15s;border-left:2px solid transparent}
.sb-a i{font-size:.88rem;width:15px;text-align:center;flex-shrink:0}
.sb-a:hover{color:rgba(255,255,255,.78);background:rgba(255,255,255,.04)}
.sb-a.on{color:var(--gold-2);background:rgba(212,168,67,.07);border-left-color:var(--gold)}
.sb-foot{margin-top:auto;padding:.85rem 1.1rem;border-top:1px solid rgba(255,255,255,.06)}
.sb-who{font-size:.66rem;color:rgba(255,255,255,.25);margin-bottom:.55rem}
.sb-who strong{color:rgba(255,255,255,.5);display:block}

/* Main */
.main{margin-left:40vw;flex:1;display:flex;flex-direction:column;min-height:100vh}
.topb{background:white;border-bottom:1px solid var(--border);padding:.75rem 1.4rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;position:sticky;top:0;z-index:100;box-shadow:0 1px 6px rgba(26,26,24,.04)}
.topb-title{font-family:var(--font-display);font-size:1rem;font-weight:300;color:var(--ink)}
.topb-sub{font-size:.66rem;color:var(--dim);margin-top:.05rem}
.ct{padding:1.25rem}

/* Admin cards */
.ac{background:white;border-radius:var(--r-lg);border:1px solid var(--border);box-shadow:0 1px 4px rgba(26,26,24,.04)}
.ach{padding:.82rem 1.15rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem}
.act{font-family:var(--font-display);font-size:.9rem;font-weight:300;color:var(--ink)}

/* Table */
.at{width:100%;border-collapse:collapse;font-size:.78rem}
.at th{background:var(--warm-grey);padding:.5rem .85rem;text-align:left;font-size:.6rem;font-weight:600;letter-spacing:.09em;text-transform:uppercase;color:var(--dim);border-bottom:1px solid var(--border)}
.at td{padding:.68rem .85rem;border-bottom:1px solid #F2EFE8;vertical-align:middle}
.at tr:last-child td{border-bottom:none}
.at tr:hover td{background:#FAFAF6}

/* Input */
.ai{background:var(--warm-grey);border:1.5px solid var(--border);color:var(--ink);border-radius:7px;padding:.52rem .82rem;font-family:'Figtree',sans-serif;font-size:.8rem;outline:none;transition:border-color .18s}
.ai:focus{border-color:var(--forest)}

/* Stat chip */
.sc{background:white;border:1px solid var(--border);border-radius:var(--r-lg);padding:1rem;position:relative;overflow:hidden;box-shadow:0 1px 4px rgba(26,26,24,.04)}
.sc-bar{position:absolute;top:0;left:0;bottom:0;width:2px;border-radius:2px 0 0 2px}

/* Mobile */
.sb-tog{display:none;position:fixed;bottom:1.25rem;left:1.25rem;width:44px;height:44px;border-radius:50%;background:var(--forest);color:var(--gold);border:none;font-size:1.05rem;z-index:300;cursor:pointer;box-shadow:var(--sh-lg);align-items:center;justify-content:center}

@media(max-width:860px){
  .sb{transform:translateX(-100%)}
  .sb.mo{transform:translateX(0)}
  .main{margin-left:0}
  .sb-tog{display:flex}
  .ct{padding:.9rem}
  .at{font-size:.72rem}
  .at th,.at td{padding:.5rem .6rem}
}
</style>
</head>
<body>
<div class="aw">
<aside class="sb" id="sb">
  <div class="sb-brand">
    <img class="sb-mark" src="../assets/images/ajira-icon-only.svg" alt="AJira Digital Club">
    <div><span class="sb-name">AJira Admin</span><span class="sb-sub">Zetech University</span></div>
  </div>
  <span class="sb-sec">Main</span>
  <a href="index.php"    class="sb-a <?=$pg==='dash'?'on':''?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
  <a href="members.php"  class="sb-a <?=$pg==='mem'?'on':''?>"><i class="bi bi-people"></i> Members</a>
  <span class="sb-sec">Club</span>
  <a href="events.php"   class="sb-a <?=$pg==='ev'?'on':''?>"><i class="bi bi-calendar3"></i> Events</a>
  <a href="notifications.php" class="sb-a <?=$pg==='notif'?'on':''?>"><i class="bi bi-bell"></i> Notifications</a>
  <a href="library.php" class="sb-a <?=$pg==='lib'?'on':''?>"><i class="bi bi-folder2-open"></i> File Library</a>
  <a href="messages.php" class="sb-a <?=$pg==='msg'?'on':''?>"><i class="bi bi-envelope"></i> Messages</a>
  <a href="export.php"   class="sb-a"><i class="bi bi-download"></i> Export CSV</a>
  <span class="sb-sec">System</span>
  <a href="settings.php" class="sb-a <?=$pg==='set'?'on':''?>"><i class="bi bi-gear"></i> Settings</a>
  <a href="../index.php" class="sb-a" target="_blank"><i class="bi bi-box-arrow-up-right"></i> View Site</a>
  <div class="sb-foot">
    <div class="sb-who">Logged in as<strong><?=e($_SESSION['admin_user']??'admin')?></strong></div>
    <a href="?logout=1" class="sb-a" style="color:rgba(220,80,80,.65);border-left:none;border-radius:7px" onmouseenter="this.style.background='rgba(220,80,80,.07)'" onmouseleave="this.style.background=''">
      <i class="bi bi-box-arrow-right"></i> Sign Out
    </a>
  </div>
</aside>

<main class="main">
  <div class="topb">
    <div><div class="topb-title"><?=e($pageTitle??'Dashboard')?></div><div class="topb-sub"><?=date('D, M j Y · g:i A')?></div></div>
    <span style="font-size:.72rem;color:var(--dim)"><?=e($_SESSION['admin_user']??'')?></span>
  </div>
  <div class="ct">
