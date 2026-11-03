<?php
declare(strict_types=1);
require_once __DIR__.'/../config/bootstrap.php';
requireAdmin();

$id=filter_var($_GET['id']??null,FILTER_VALIDATE_INT);
if(!$id||$id<1){http_response_code(404);exit('File not found.');}
try{
  $statement=db()->prepare('SELECT original_name,stored_name,mime_type,file_size FROM club_files WHERE id=? LIMIT 1');
  $statement->execute([$id]);
  $file=$statement->fetch();
}catch(Throwable $e){error_log('Admin file preview: '.$e->getMessage());http_response_code(500);exit('The file is temporarily unavailable.');}
$extensions=[
  'pdf'=>'application/pdf','jpg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp',
  'mp4'=>'video/mp4','webm'=>'video/webm','ogv'=>'video/ogg',
  'docx'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
  'xlsx'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
  'pptx'=>'application/vnd.openxmlformats-officedocument.presentationml.presentation',
];
if(!$file||!preg_match('/\A[a-f0-9]{48}\.(pdf|jpg|png|gif|webp|mp4|webm|ogv|docx|xlsx|pptx)\z/',$file['stored_name'])){http_response_code(404);exit('File not found.');}
$extension=pathinfo($file['stored_name'],PATHINFO_EXTENSION);
if(($extensions[$extension]??null)!==$file['mime_type']){http_response_code(404);exit('File not found.');}
$path=__DIR__.'/../assets/uploads/club-library/'.$file['stored_name'];
if(!is_file($path)){http_response_code(404);exit('File not found.');}
$size=filesize($path);
if($size===false){http_response_code(500);exit('The file could not be read.');}
$name=preg_replace('/[\x00-\x1F\x7F]/u','',$file['original_name'])??'download';
$fallback=preg_replace('/[^A-Za-z0-9._-]/','_',basename($name))?:'download';
$download=isset($_GET['download'])||!in_array($file['mime_type'],['application/pdf','image/jpeg','image/png','image/gif','image/webp','video/mp4','video/webm','video/ogg'],true);
$start=0;$end=$size-1;$status=200;
if(!$download&&isset($_SERVER['HTTP_RANGE'])){
  if(!preg_match('/\Abytes=(\d*)-(\d*)\z/',trim($_SERVER['HTTP_RANGE']),$range)||($range[1]===''&&$range[2]==='')){
    http_response_code(416);header('Content-Range: bytes */'.$size);exit;
  }
  if($range[1]===''){
    $length=(int)$range[2];
    if($length<1){http_response_code(416);header('Content-Range: bytes */'.$size);exit;}
    $start=max(0,$size-$length);
  }else{
    $start=(int)$range[1];
    $end=$range[2]!==''?min((int)$range[2],$size-1):$size-1;
  }
  if($start>=$size||$start>$end){http_response_code(416);header('Content-Range: bytes */'.$size);exit;}
  $status=206;
}
http_response_code($status);
header('Content-Type: '.$file['mime_type']);
header('Content-Length: '.($end-$start+1));
header('Content-Disposition: '.($download?'attachment':'inline').'; filename="'.$fallback.'"; filename*=UTF-8\'\''.rawurlencode($name));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store');
header('Accept-Ranges: bytes');
if($status===206) header('Content-Range: bytes '.$start.'-'.$end.'/'.$size);
if($_SERVER['REQUEST_METHOD']==='HEAD') exit;
$stream=fopen($path,'rb');
if($stream===false){http_response_code(500);exit;}
if($start>0) fseek($stream,$start);
$remaining=$end-$start+1;
while($remaining>0&&!feof($stream)){
  $chunk=fread($stream,min(8192,$remaining));
  if($chunk===false||$chunk==='') break;
  echo $chunk;
  $remaining-=strlen($chunk);
  if(connection_aborted()) break;
}
fclose($stream);
