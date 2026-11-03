<?php
declare(strict_types=1);
require_once __DIR__.'/config/bootstrap.php';
$token=$_GET['token']??'';
if(!is_string($token)||!preg_match('/\A[a-f0-9]{64}\z/',$token)){http_response_code(404);exit('File not found.');}
try{
  $statement=db()->prepare('SELECT original_name,stored_name,mime_type,file_size FROM club_files WHERE share_token=? LIMIT 1');
  $statement->execute([$token]);
  $file=$statement->fetch();
}catch(Throwable $e){error_log('Shared file: '.$e->getMessage());http_response_code(500);exit('The shared file is temporarily unavailable.');}
if(!$file||!preg_match('/\A[a-f0-9]{48}\.(pdf|jpg|png|gif|webp|mp4|webm|ogv|docx|xlsx|pptx)\z/',$file['stored_name'])){http_response_code(404);exit('File not found.');}
$mimeByExtension=[
  'pdf'=>'application/pdf',
  'jpg'=>'image/jpeg',
  'png'=>'image/png',
  'gif'=>'image/gif',
  'webp'=>'image/webp',
  'mp4'=>'video/mp4',
  'webm'=>'video/webm',
  'ogv'=>'video/ogg',
  'docx'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
  'xlsx'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
  'pptx'=>'application/vnd.openxmlformats-officedocument.presentationml.presentation',
];
$extension=pathinfo($file['stored_name'],PATHINFO_EXTENSION);
if(($mimeByExtension[$extension]??null)!==$file['mime_type']){http_response_code(404);exit('File not found.');}
$path=__DIR__.'/assets/uploads/club-library/'.$file['stored_name'];
if(!is_file($path)){http_response_code(404);exit('File not found.');}
$downloadName=preg_replace('/[\x00-\x1F\x7F]/u','',$file['original_name'])??'download';
$fallback=preg_replace('/[^A-Za-z0-9._-]/','_',basename($downloadName))?:'download';
header('Content-Type: '.$file['mime_type']);
header('Content-Length: '.filesize($path));
header('Content-Disposition: attachment; filename="'.$fallback.'"; filename*=UTF-8\'\''.rawurlencode($downloadName));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store');
readfile($path);
