<?php
declare(strict_types=1);
require_once __DIR__.'/../config/bootstrap.php';
requireAdmin();
$id=filter_var($_GET['id']??null,FILTER_VALIDATE_INT);
if(!$id||$id<1){http_response_code(404);exit('File not found.');}
try{
  $statement=db()->prepare('SELECT title,original_name,mime_type FROM club_files WHERE id=? LIMIT 1');
  $statement->execute([$id]);
  $file=$statement->fetch();
}catch(Throwable $e){error_log('Admin file preview: '.$e->getMessage());http_response_code(500);exit('The file is temporarily unavailable.');}
if(!$file){http_response_code(404);exit('File not found.');}
$src='file.php?id='.$id;
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="referrer" content="same-origin">
<title><?=e($file['title'])?> — File Preview</title>
<link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/main.css">
<style>
body{margin:0;background:#171915;color:white;font-family:'Figtree',sans-serif}
.preview-head{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.8rem 1rem;background:#0c1a0e}
.preview-title{min-width:0}.preview-title strong,.preview-title span{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.preview-title strong{font-size:.9rem}.preview-title span{font-size:.72rem;color:rgba(255,255,255,.58);margin-top:.2rem}
.preview-stage{height:calc(100vh - 68px);display:flex;align-items:center;justify-content:center;padding:1rem;box-sizing:border-box}
.preview-stage img,.preview-stage video{max-width:100%;max-height:100%;object-fit:contain}
.preview-stage iframe{width:min(100%,1100px);height:100%;border:0;background:white}
.preview-unavailable{text-align:center;max-width:480px;padding:2rem;line-height:1.65}
</style>
</head>
<body>
<header class="preview-head">
  <div class="preview-title"><strong><?=e($file['title'])?></strong><span><?=e($file['original_name'])?></span></div>
  <a class="btn btn-gold btn-sm" href="<?=e($src.'&download=1')?>"><i class="bi bi-download"></i> Download</a>
</header>
<main class="preview-stage">
  <?php if(str_starts_with($file['mime_type'],'image/')): ?>
  <img src="<?=e($src)?>" alt="<?=e($file['title'])?>">
  <?php elseif(str_starts_with($file['mime_type'],'video/')): ?>
  <video src="<?=e($src)?>" controls autoplay playsinline preload="metadata">Your browser cannot play this video format.</video>
  <?php elseif($file['mime_type']==='application/pdf'): ?>
  <iframe src="<?=e($src)?>" title="<?=e($file['title'])?>"></iframe>
  <?php else: ?>
  <div class="preview-unavailable"><i class="bi bi-file-earmark-text" style="font-size:3rem;color:var(--gold)"></i><h1 style="font-family:var(--font-display);font-weight:300">Preview unavailable</h1><p>This document format cannot be displayed in the browser. Download the file to open it.</p><a class="btn btn-gold" href="<?=e($src.'&download=1')?>"><i class="bi bi-download"></i> Download document</a></div>
  <?php endif; ?>
</main>
</body>
</html>
