<?php
declare(strict_types=1);
$env=dirname(__DIR__).'/.env';
if(file_exists($env)) foreach(file($env,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $l){
  $l=trim($l); if(!$l||$l[0]==='#'||!str_contains($l,'=')) continue;
  [$k,$v]=array_map('trim',explode('=',$l,2)); $v=trim($v,'"\'');
  if(!isset($_ENV[$k])){$_ENV[$k]=$v;putenv("$k=$v");}
}
if(session_status()===PHP_SESSION_NONE) session_start(['cookie_httponly'=>true,'cookie_samesite'=>'Lax']);
function db():PDO{
  static $p=null; if($p) return $p;
  $dsn=sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',$_ENV['DB_HOST']??'localhost',$_ENV['DB_PORT']??'3306',$_ENV['DB_NAME']??'ajira_v4');
  try{ $p=new PDO($dsn,$_ENV['DB_USER']??'root',$_ENV['DB_PASS']??'',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]); }
  catch(PDOException $e){ error_log('DB:'.$e->getMessage()); http_response_code(500); die('<p style="font-family:sans-serif;padding:2rem;color:#991B1B">Database connection failed. Check .env file.</p>'); }
  return $p;
}
function e(string $s):string{return htmlspecialchars($s,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function redirect(string $u):never{header("Location:$u");exit;}
function isAdmin():bool{return !empty($_SESSION['admin_id']);}
function requireAdmin():void{if(!isAdmin()) redirect('../admin/login.php');}
function isMember():bool{return !empty($_SESSION['member_id']);}
function requireMember():void{if(!isMember()) redirect('member_login.php');}
function memberNotification(int $memberId,string $title,string $message):void{
  db()->prepare('INSERT INTO member_notifications(member_id,title,message) VALUES(?,?,?)')->execute([$memberId,$title,$message]);
}
function paymentQrUrl(string $method,int $amount,string $reg):string{
  $destination=$method==='paybill'?($_ENV['PAYBILL_NUMBER']??'XXXXXXX'):($_ENV['TILL_NUMBER']??'XXXXXXX');
  $label=$_ENV['QR_PAYMENT_LABEL']??'AJira Digital Club payment';
  $payload="$label | $method: $destination | Amount: KES $amount | Account: $reg";
  return 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data='.rawurlencode($payload);
}
