<?php
declare(strict_types=1);
require_once __DIR__.'/config/bootstrap.php';
requireMember();
$id=(int)$_SESSION['member_id']; $errors=[]; $notice='';
$s=db()->prepare('SELECT * FROM members WHERE id=? LIMIT 1'); $s->execute([$id]); $member=$s->fetch();
if(!$member){unset($_SESSION['member_id']); redirect('member_login.php');}
$detectMime=static function(string $path):?string{
  if(class_exists('finfo')){
    try{
      $detector=new finfo(FILEINFO_MIME_TYPE);
      $mime=$detector->file($path);
      if(is_string($mime)&&$mime!=='application/octet-stream') return $mime;
    }catch(Throwable $e){ }
  }
  if(function_exists('mime_content_type')){
    $mime=@mime_content_type($path);
    if(is_string($mime)&&$mime!=='application/octet-stream') return $mime;
  }
  $stream=fopen($path,'rb');
  if($stream===false) return null;
  $header=fread($stream,512);
  fclose($stream);
  if(!is_string($header)) return null;
  if(str_starts_with($header,"%PDF-")) return 'application/pdf';
  if(str_starts_with($header,"\xFF\xD8\xFF")) return 'image/jpeg';
  if(str_starts_with($header,"\x89PNG\r\n\x1A\n")) return 'image/png';
  if(str_starts_with($header,'GIF87a')||str_starts_with($header,'GIF89a')) return 'image/gif';
  if(str_starts_with($header,'RIFF')&&substr($header,8,4)==='WEBP') return 'image/webp';
  return null;
};

if($_SERVER['REQUEST_METHOD']==='POST'){
  $action=$_POST['action']??'';
  if($action==='details'){
    $phone=trim($_POST['phone']??''); $email=strtolower(trim($_POST['email']??'')); $course=trim($_POST['course_year']??''); $bio=trim($_POST['bio']??'');
    if(!preg_match('/^(07|01)\d{8}$/',$phone)) $errors[]='Enter a valid phone number.';
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[]='Enter a valid email address.';
    if(strlen($course)<3) $errors[]='Course and year is required.';
    if(strlen($bio)>500) $errors[]='Your bio must be 500 characters or fewer.';
    if(!$errors){db()->prepare('UPDATE members SET phone=?,email=?,course_year=?,bio=? WHERE id=?')->execute([$phone,$email,$course,$bio?:null,$id]);$notice='Profile details updated.'; $s->execute([$id]);$member=$s->fetch();}
  }
  if($action==='photo'){
    $file=$_FILES['profile_image']??null; $allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp']; $mime='';
    if(!$file||$file['error']!==UPLOAD_ERR_OK) $errors[]='Choose a profile image to upload.';
    if($file&&$file['error']===UPLOAD_ERR_OK){$mime=$detectMime($file['tmp_name']);if(!isset($allowed[$mime])||$file['size']>3*1024*1024||@getimagesize($file['tmp_name'])===false)$errors[]='Use a valid JPG, PNG, or WebP image up to 3MB.';}
    if(!$errors){$dir=__DIR__.'/assets/uploads/profile-images';if(!is_dir($dir))mkdir($dir,0750,true);$name=$id.'_'.bin2hex(random_bytes(12)).'.'.$allowed[$mime];if(move_uploaded_file($file['tmp_name'],$dir.'/'.$name)){if(!empty($member['profile_image'])&&preg_match('/^[0-9a-f_]+\.(jpg|png|webp)$/i',$member['profile_image']))@unlink($dir.'/'.$member['profile_image']);db()->prepare('UPDATE members SET profile_image=? WHERE id=?')->execute([$name,$id]);$notice='Profile photo updated.';$s->execute([$id]);$member=$s->fetch();}else$errors[]='The profile image could not be saved.';}
  }
}
$expired=$member['is_active']&&!empty($member['expires_at'])&&strtotime($member['expires_at'])<time(); $memberStatus=$expired?'Expired':($member['is_active']?'Active':'Pending review');
$ns=db()->prepare('SELECT * FROM member_notifications WHERE member_id=? ORDER BY created_at DESC LIMIT 8'); $ns->execute([$id]); $notifications=$ns->fetchAll();
$pageTitle='My Member Profile'; include '_head.php';
?>
<section class="member-profile-page"><div class="wrap member-profile-wrap" style="max-width:820px">
  <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap"><div><div class="pg-label"><i class="bi bi-person-circle"></i> Member portal</div><h1 style="font-family:var(--font-display);font-weight:300;font-size:2rem;margin:.35rem 0 0">Hello, <em><?=e($member['full_name'])?></em></h1></div><div style="display:flex;gap:.5rem;flex-wrap:wrap"><a class="btn btn-outline btn-sm" href="member_messages.php"><i class="bi bi-chat-dots"></i> My messages</a><a class="btn btn-outline btn-sm" href="member_logout.php">Sign out</a></div></div>
  <?php if($notice): ?><div style="background:var(--warm-grey);border:1px solid var(--border);padding:.8rem 1rem;border-radius:var(--r);margin-bottom:1rem;color:var(--ink);font-size:.84rem;font-weight:600"><?=e($notice)?></div><?php endif; ?>
  <?php if($errors): ?><div style="background:rgba(192,57,43,.06);padding:.8rem 1rem;border-radius:var(--r);margin-bottom:1rem;color:var(--red);font-size:.84rem"><?php foreach($errors as $error): ?><div><?=e($error)?></div><?php endforeach; ?></div><?php endif; ?>
  <div class="card" style="padding:1.35rem;margin-bottom:1rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
    <?php if(!empty($member['profile_image'])): ?><img src="assets/uploads/profile-images/<?=e($member['profile_image'])?>" alt="Profile photo" style="width:88px;height:88px;border-radius:50%;object-fit:cover;border:3px solid var(--warm-grey)"><?php else: ?><div style="width:88px;height:88px;border-radius:50%;background:var(--forest);color:var(--gold);display:flex;align-items:center;justify-content:center;font-family:var(--font-display);font-size:1.8rem"><?=e(strtoupper(substr($member['full_name'],0,1)))?></div><?php endif; ?>
    <div style="flex:1;min-width:210px"><div style="font-family:var(--font-display);font-size:1.35rem;color:var(--ink)"><?=e($member['full_name'])?></div><div style="font-size:.8rem;color:var(--muted);margin-top:.2rem"><?=e($member['course_year'])?> · <?=e($member['reg_number'])?></div><?php if($member['bio']): ?><p style="font-size:.8rem;color:var(--muted);line-height:1.6;margin:.5rem 0 0"><?=e($member['bio'])?></p><?php endif; ?></div>
    <form method="POST" enctype="multipart/form-data"><input type="hidden" name="action" value="photo"><label class="btn btn-outline btn-sm" style="cursor:pointer"><i class="bi bi-camera"></i> Change photo<input type="file" name="profile_image" accept="image/jpeg,image/png,image/webp" hidden onchange="this.form.submit()"></label></form>
  </div>
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.75rem;margin-bottom:1rem">
    <div class="card" style="padding:1rem"><div class="label">Membership</div><strong style="display:block;margin-top:.3rem;color:var(--ink)"><?=$memberStatus?></strong></div>
    <div class="card" style="padding:1rem"><div class="label">Membership type</div><strong style="display:block;margin-top:.3rem;color:var(--ink)"><?=e(ucfirst($member['member_type']))?></strong></div>
    <div class="card" style="padding:1rem"><div class="label">Valid until</div><strong style="display:block;margin-top:.3rem;color:var(--ink)"><?=!empty($member['expires_at'])?e(date('M j, Y',strtotime($member['expires_at']))):'Admin review'?></strong></div>
  </div>
  <div class="card" style="padding:1.35rem;margin-bottom:1rem"><div style="font-family:var(--font-display);font-size:1.15rem;color:var(--ink);margin-bottom:.8rem">Profile details</div><form method="POST" style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem"><input type="hidden" name="action" value="details"><div><label class="f-label">Phone</label><input class="f-input" name="phone" value="<?=e($member['phone'])?>" required></div><div><label class="f-label">Email</label><input class="f-input" type="email" name="email" value="<?=e($member['email'])?>" required></div><div style="grid-column:1/-1"><label class="f-label">Course & Year</label><input class="f-input" name="course_year" value="<?=e($member['course_year'])?>" required></div><div style="grid-column:1/-1"><label class="f-label">Short bio</label><textarea class="f-input" name="bio" rows="3" maxlength="500" placeholder="Tell the club a little about your interests..."><?=e($member['bio']??'')?></textarea></div><button class="btn btn-forest btn-sm" type="submit" style="grid-column:1/-1"><i class="bi bi-save"></i> Save details</button></form></div>
  <div class="card" style="padding:1.35rem"><div style="font-family:var(--font-display);font-size:1.15rem;color:var(--ink);margin-bottom:.8rem">Notifications</div><?php if(!$notifications): ?><p style="color:var(--muted);font-size:.82rem">No notifications yet.</p><?php else: foreach($notifications as $n): ?><div style="padding:.7rem 0;border-top:1px solid var(--border)"><strong style="font-size:.84rem;color:var(--ink)"><?=e($n['title'])?></strong><div style="font-size:.8rem;color:var(--muted);margin-top:.2rem"><?=e($n['message'])?></div><div style="font-size:.68rem;color:var(--dim);margin-top:.3rem"><?=e($n['created_at'])?></div></div><?php endforeach; endif; ?></div>
</div></section>
<?php include '_footer.php'; ?>