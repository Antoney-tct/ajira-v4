<?php
declare(strict_types=1);
$pageTitle='File Library'; $activePage='lib';
require_once __DIR__.'/../config/bootstrap.php';
requireAdmin();

$errors=[]; $notice='';
if(empty($_SESSION['library_csrf'])) $_SESSION['library_csrf']=bin2hex(random_bytes(32));
$csrf=$_SESSION['library_csrf'];
$storageDir=__DIR__.'/../assets/uploads/club-library';
$maxFileSize=20*1024*1024;
$maxFolderFiles=100;
$maxFolderSize=500*1024*1024;
$accepted=[
  'pdf'=>['pdf','application/pdf'],
  'jpg'=>['jpg','image/jpeg'],
  'jpeg'=>['jpg','image/jpeg'],
  'png'=>['png','image/png'],
  'gif'=>['gif','image/gif'],
  'webp'=>['webp','image/webp'],
  'mp4'=>['mp4','video/mp4'],
  'webm'=>['webm','video/webm'],
  'ogv'=>['ogv','video/ogg'],
  'docx'=>['docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
  'xlsx'=>['xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
  'pptx'=>['pptx','application/vnd.openxmlformats-officedocument.presentationml.presentation'],
];
$mimeAliases=[
  'application/vnd.openxmlformats-officedocument.wordprocessingml.document'=>['application/zip'],
  'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'=>['application/zip'],
  'application/vnd.openxmlformats-officedocument.presentationml.presentation'=>['application/zip'],
];
$detectMime=static function(string $path):?string{
  if(class_exists('finfo')){
    $detector=new finfo(FILEINFO_MIME_TYPE);
    $mime=$detector->file($path);
    if(is_string($mime)&&$mime!=='application/octet-stream') return $mime;
  }elseif(function_exists('mime_content_type')){
    $mime=mime_content_type($path);
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
  if(substr($header,4,4)==='ftyp') return 'video/mp4';
  if(str_starts_with($header,"\x1A\x45\xDF\xA3")) return 'video/webm';
  if(str_starts_with($header,'OggS')) return 'video/ogg';
  if(str_starts_with($header,"PK\x03\x04")||str_starts_with($header,"PK\x05\x06")||str_starts_with($header,"PK\x07\x08")) return 'application/zip';
  return null;
};

if($_SERVER['REQUEST_METHOD']==='POST'){
  $postedCsrf=$_POST['csrf']??null;
  if(!is_string($postedCsrf)||!hash_equals($csrf,$postedCsrf)) $errors[]='Your session expired. Refresh the page and try again.';
  else{
    $action=$_POST['action']??'';
    try{
      if($action==='create_folder'){
        $folderName=trim($_POST['folder_name']??'');
        $parentPath=trim($_POST['current_folder']??'','/');
        if(strlen($folderName)<1||strlen($folderName)>120||$folderName==='.'||$folderName==='..'||!preg_match('/\A[\pL\pN _.-]+\z/u',$folderName)) $errors[]='Use a folder name up to 120 characters with letters, numbers, spaces, dashes, underscores, or periods.';
        if($parentPath!==''&&!preg_match('/\A[\pL\pN _.-]+(?:\/[\pL\pN _.-]+)*\z/u',$parentPath)) $errors[]='The parent folder path is invalid.';
        if(!$errors){
          $newPath=($parentPath!==''?$parentPath.'/':'').$folderName;
          if(strlen($newPath)>1000) $errors[]='The folder path is too long.';
          else{
            if($parentPath!==''){
              $parent=db()->prepare('SELECT 1 FROM club_folders WHERE folder_path=?');
              $parent->execute([$parentPath]);
              if(!$parent->fetchColumn()) $errors[]='The parent folder no longer exists.';
            }
            if(!$errors){
              $exists=db()->prepare('SELECT 1 FROM club_folders WHERE folder_path=?');
              $exists->execute([$newPath]);
              if($exists->fetchColumn()) $errors[]='A folder with that name already exists here.';
              else{db()->prepare('INSERT INTO club_folders(folder_path) VALUES(?)')->execute([$newPath]);$notice='Folder created.';}
            }
          }
        }
      }elseif($action==='upload'){
        $title=trim($_POST['title']??'');
        $description=trim($_POST['description']??'');
        $targetFolder=trim($_POST['target_folder']??'','/');
        if($targetFolder!==''&&!preg_match('/\A[\pL\pN _.-]+(?:\/[\pL\pN _.-]+)*\z/u',$targetFolder)) $errors[]='The destination folder is invalid.';
        if($targetFolder!==''&&!$errors){
          $target=db()->prepare('SELECT 1 FROM club_folders WHERE folder_path=?');
          $target->execute([$targetFolder]);
          if(!$target->fetchColumn()) $errors[]='The destination folder no longer exists.';
        }
        $uploads=[];
        if(!empty($_FILES['document'])&&$_FILES['document']['error']!==UPLOAD_ERR_NO_FILE){
          $uploads[]=[
            'name'=>$_FILES['document']['name'],
            'tmp_name'=>$_FILES['document']['tmp_name'],
            'size'=>$_FILES['document']['size'],
            'error'=>$_FILES['document']['error'],
          ];
        }
        $folderUpload=$_FILES['folder_files']??null;
        if($folderUpload&&is_array($folderUpload['name']??null)){
          foreach($folderUpload['name'] as $index=>$name){
            if(($folderUpload['error'][$index]??UPLOAD_ERR_NO_FILE)===UPLOAD_ERR_NO_FILE) continue;
            $uploads[]=[
              'name'=>$name,
              'tmp_name'=>$folderUpload['tmp_name'][$index]??'',
              'size'=>$folderUpload['size'][$index]??0,
              'error'=>$folderUpload['error'][$index]??UPLOAD_ERR_NO_FILE,
            ];
          }
        }
        if($title!==''&&strlen($title)>160) $errors[]='Title must be 160 characters or fewer.';
        if(strlen($description)>1000) $errors[]='Description must be 1,000 characters or fewer.';
        if(!$uploads) $errors[]='Choose a file or a folder to upload.';
        if(count($uploads)>$maxFolderFiles) $errors[]='Choose no more than 100 files at a time.';
        $totalSize=0;
        foreach($uploads as $upload){
          $totalSize+=(int)$upload['size'];
          if($upload['error']!==UPLOAD_ERR_OK){$errors[]=$upload['error']===UPLOAD_ERR_INI_SIZE||$upload['error']===UPLOAD_ERR_FORM_SIZE?'A file exceeds the server upload limit.':'One or more files could not be uploaded. Check the server upload limits and try again.';break;}
          $extension=strtolower(pathinfo((string)$upload['name'],PATHINFO_EXTENSION));
          $sizeLimit=in_array($extension,['mp4','webm','ogv'],true)?100*1024*1024:$maxFileSize;
          if((int)$upload['size']<1||(int)$upload['size']>$sizeLimit){$errors[]=in_array($extension,['mp4','webm','ogv'],true)?'Videos must be 100 MB or smaller.':'Each file must be smaller than 20 MB.';break;}
          if(!is_uploaded_file($upload['tmp_name'])){$errors[]='An uploaded file could not be verified.';break;}
        }
        if($totalSize>$maxFolderSize) $errors[]='The combined folder upload must be 500 MB or smaller.';
        if(!$errors&&!is_dir($storageDir)&&!mkdir($storageDir,0750,true)&&!is_dir($storageDir)) throw new RuntimeException('Could not create the file library storage directory. Check folder permissions.');
        if(!$errors&&!is_writable($storageDir)) throw new RuntimeException('The file library storage folder is not writable by PHP. Check folder permissions.');
        $uploaded=0;
        if(!$errors){
          foreach($uploads as $upload){
            $relativePath=str_replace('\\','/',$upload['name']);
            $segments=array_values(array_filter(explode('/',$relativePath),static fn(string $part):bool=>$part!==''&&$part!=='.'&&$part!=='..'));
            $originalName=basename($relativePath);
            $originalName=preg_replace('/[\x00-\x1F\x7F]/u','',$originalName)??'';
            $originalName=substr($originalName,0,255);
            if($originalName===''){$errors[]='A file had an invalid name and was skipped.';continue;}
            $relativeFolder=implode('/',array_slice($segments,0,-1));
            $folderPath=trim(($targetFolder!==''?$targetFolder.'/':'').$relativeFolder,'/');
            $folderPath=substr(preg_replace('/[\x00-\x1F\x7F]/u','',$folderPath)??'',0,1000);
            $extension=strtolower(pathinfo($originalName,PATHINFO_EXTENSION));
            $mime=$detectMime($upload['tmp_name']);
            $rule=$accepted[$extension]??null;
            $validMime=$rule&&($mime===$rule[1]||in_array($mime,$mimeAliases[$rule[1]]??[],true));
            if(!$rule||!$validMime){$errors[]='Skipped '.$originalName.': use a PDF, image, MP4, WebM, OGV, DOCX, XLSX, or PPTX file.';continue;}
            $storedName=bin2hex(random_bytes(24)).'.'.$rule[0];
            $destination=$storageDir.'/'.$storedName;
            if(!move_uploaded_file($upload['tmp_name'],$destination)){$errors[]='Could not store '.$originalName.'. Check folder permissions.';continue;}
            $fileTitle=$title!==''?$title:substr(pathinfo($originalName,PATHINFO_FILENAME),0,160);
            try{
              if($folderPath!==''){
                $parts=explode('/',$folderPath);
                $folderStatement=db()->prepare('INSERT IGNORE INTO club_folders(folder_path) VALUES(?)');
                $path='';
                foreach($parts as $part){$path=($path!==''?$path.'/':'').$part;$folderStatement->execute([$path]);}
              }
              db()->prepare('INSERT INTO club_files(title,description,original_name,folder_path,stored_name,mime_type,file_size,uploaded_by) VALUES(?,?,?,?,?,?,?,?)')
                ->execute([$fileTitle,$description?:null,$originalName,$folderPath,$storedName,$rule[1],(int)$upload['size'],(int)$_SESSION['admin_id']]);
              $uploaded++;
            }catch(PDOException $e){
              if(is_file($destination)&&!unlink($destination)) error_log('Club library: failed to remove upload after database error: '.$destination);
              error_log('Club library database error: '.$e->getMessage());
              if(in_array((string)$e->getCode(),['42S02','42S22'],true)) throw new RuntimeException('The file library database is missing its folder column or table. Run the updated migration_club_library.sql in phpMyAdmin.');
              throw new RuntimeException('The database could not save '.$originalName.'. Check the database connection and retry.');
            }catch(Throwable $e){
              if(is_file($destination)&&!unlink($destination)) error_log('Club library: failed to remove upload after database error: '.$destination);
              throw $e;
            }
          }
          if($uploaded) $notice=$uploaded.' file'.($uploaded===1?'':'s').' uploaded. Files are private until you create share links.';
        }
      }elseif($action==='share'||$action==='revoke'){
        $id=filter_var($_POST['id']??null,FILTER_VALIDATE_INT);
        if(!$id||$id<1) $errors[]='Choose a valid file.';
        else{
          $token=$action==='share'?bin2hex(random_bytes(32)):null;
          $statement=db()->prepare('UPDATE club_files SET share_token=? WHERE id=?');
          $statement->execute([$token,$id]);
          if($statement->rowCount()) $notice=$action==='share'?'Share link created.':'Sharing disabled; the old link no longer works.';
          else $errors[]='File not found or sharing status unchanged.';
        }
      }elseif($action==='delete'){
        $id=filter_var($_POST['id']??null,FILTER_VALIDATE_INT);
        if(!$id||$id<1) $errors[]='Choose a valid file.';
        else{
          $statement=db()->prepare('SELECT stored_name FROM club_files WHERE id=?');
          $statement->execute([$id]);
          $item=$statement->fetch();
          if(!$item) $errors[]='File not found.';
          elseif(!preg_match('/\A[a-f0-9]{48}\.(pdf|jpg|png|gif|webp|mp4|webm|ogv|docx|xlsx|pptx)\z/',$item['stored_name'])) throw new RuntimeException('Stored filename failed validation.');
          else{
            db()->prepare('DELETE FROM club_files WHERE id=?')->execute([$id]);
            $path=$storageDir.'/'.$item['stored_name'];
            if(is_file($path)&&!unlink($path)){error_log('Club library: failed to remove deleted file: '.$path);$errors[]='File record deleted, but the stored file could not be removed.';}
            else $notice='File deleted.';
          }
        }
      }else $errors[]='Choose a valid library action.';
    }catch(Throwable $e){
      error_log('Club library: '.$e->getMessage());
      $errors[]=$e->getMessage()?:'The file library action failed. Check the database migration and storage folder permissions.';
    }
  }
}

try{$allFiles=db()->query('SELECT id,title,description,original_name,folder_path,stored_name,mime_type,file_size,share_token,created_at FROM club_files ORDER BY folder_path,original_name')->fetchAll();}
catch(Throwable $e){error_log('Club library: '.$e->getMessage());$allFiles=[];$errors[]='Could not load the file library. Run migration_club_library.sql first.';}
$currentFolder=trim((string)($_POST['current_folder']??$_POST['target_folder']??$_GET['folder']??''),'/');
if($currentFolder!==''&&!preg_match('/\A[\pL\pN _.-]+(?:\/[\pL\pN _.-]+)*\z/u',$currentFolder)) $currentFolder='';
$folderPaths=[];
try{
  foreach(db()->query('SELECT folder_path FROM club_folders')->fetchAll(PDO::FETCH_COLUMN) as $path) $folderPaths[(string)$path]=true;
}catch(Throwable $e){error_log('Club library folders: '.$e->getMessage());$errors[]='Folder management is unavailable. Run the updated migration_club_library.sql.';}
foreach($allFiles as $file){
  $path=(string)$file['folder_path'];
  if($path==='') continue;
  $parts=explode('/',$path);$nested='';
  foreach($parts as $part){$nested=($nested!==''?$nested.'/':'').$part;$folderPaths[$nested]=true;}
}
$currentExists=$currentFolder===''||isset($folderPaths[$currentFolder]);
if(!$currentExists){$errors[]='That folder could not be found.';$currentFolder='';}
$childFolders=[];
$prefix=$currentFolder!==''?$currentFolder.'/':'';
foreach(array_keys($folderPaths) as $path){
  if(!str_starts_with($path,$prefix)||$path===$currentFolder) continue;
  $remainder=substr($path,strlen($prefix));
  if($remainder!==''&&!str_contains($remainder,'/')) $childFolders[$path]=$remainder;
}
asort($childFolders,SORT_NATURAL|SORT_FLAG_CASE);
$files=array_values(array_filter($allFiles,static fn(array $file):bool=>(string)$file['folder_path']===$currentFolder));
$parentFolder=str_contains($currentFolder,'/')?substr($currentFolder,0,strrpos($currentFolder,'/')):'';
$host=$_SERVER['HTTP_HOST']??'localhost';
if(!preg_match('/\A[a-z0-9.-]+(?::\d{1,5})?\z/i',$host)) $host='localhost';
$scheme=(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')?'https':'http';
$basePath=rtrim(dirname(dirname($_SERVER['SCRIPT_NAME']??'/admin/library.php')),'/');
$shareBase=$scheme.'://'.$host.$basePath.'/share.php?token=';
include '_layout.php';
?>
<div class="ac" style="margin-bottom:1.1rem">
  <div class="ach"><div class="act">Create a library folder</div><div style="font-size:.72rem;color:var(--muted)">Creates a folder inside <?= $currentFolder!==''?e($currentFolder):'the library root' ?></div></div>
  <div style="padding:1rem 1.1rem">
    <form method="POST" style="display:flex;align-items:end;gap:.65rem;flex-wrap:wrap">
      <input type="hidden" name="csrf" value="<?=e($csrf)?>"><input type="hidden" name="action" value="create_folder"><input type="hidden" name="current_folder" value="<?=e($currentFolder)?>">
      <div style="flex:1;min-width:200px"><label class="f-label">Folder name</label><input class="ai" style="width:100%" name="folder_name" maxlength="120" required placeholder="e.g. Meeting Minutes"></div>
      <button class="btn btn-outline btn-sm" type="submit"><i class="bi bi-folder-plus"></i> Create folder</button>
    </form>
  </div>
</div>
<div class="ac" style="margin-bottom:1.1rem">
  <div class="ach"><div class="act">Upload files or a folder</div><div style="font-size:.72rem;color:var(--muted)">PDFs, images, and Office documents up to 20 MB; MP4/WebM/OGV videos up to 100 MB each</div></div>
  <div style="padding:1rem 1.1rem">
    <?php if($notice): ?><div style="background:var(--warm-grey);border:1px solid var(--border);padding:.7rem .85rem;border-radius:var(--r);font-size:.82rem;font-weight:600;margin-bottom:1rem"><?=e($notice)?></div><?php endif; ?>
    <?php if($errors): ?><div style="background:rgba(192,57,43,.06);padding:.7rem .85rem;border-radius:var(--r);font-size:.82rem;color:var(--red);margin-bottom:1rem"><?php foreach($errors as $error): ?><div><?=e($error)?></div><?php endforeach; ?></div><?php endif; ?>
    <form method="POST" enctype="multipart/form-data" style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
      <input type="hidden" name="csrf" value="<?=e($csrf)?>"><input type="hidden" name="action" value="upload"><input type="hidden" name="target_folder" value="<?=e($currentFolder)?>"><input type="hidden" name="current_folder" value="<?=e($currentFolder)?>">
      <div><label class="f-label">Title (optional)</label><input class="ai" style="width:100%" name="title" maxlength="160" placeholder="Defaults to each file name"></div>
      <div><label class="f-label">One file</label><input class="ai" style="width:100%" type="file" name="document" accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,.mp4,.webm,.ogv,.docx,.xlsx,.pptx"></div>
      <div style="grid-column:1/-1"><label class="f-label">Or choose a folder from your computer</label><input class="ai" style="width:100%" type="file" name="folder_files[]" accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,.mp4,.webm,.ogv,.docx,.xlsx,.pptx" webkitdirectory directory multiple><div style="font-size:.68rem;color:var(--dim);margin-top:.25rem">Folder paths are preserved in the library. Up to 100 files and 500 MB total. Server upload limits also apply.</div></div>
      <div style="grid-column:1/-1"><label class="f-label">Description (optional)</label><textarea class="ai" style="width:100%;resize:vertical" name="description" maxlength="1000" rows="2"></textarea></div>
      <button class="btn btn-forest btn-sm" type="submit" style="grid-column:1/-1;width:fit-content"><i class="bi bi-upload"></i> Upload files</button>
    </form>
  </div>
</div>
<div class="ac" style="margin-bottom:1.1rem">
  <div class="ach"><div class="act">Folders</div><div style="font-size:.72rem;color:var(--muted)"><?=count($childFolders)?> folders</div></div>
  <div style="padding:1rem;display:flex;gap:.55rem;flex-wrap:wrap">
    <?php if($currentFolder!==''): ?><a class="btn btn-outline btn-sm" href="library.php?folder=<?=rawurlencode($parentFolder)?>"><i class="bi bi-arrow-up"></i> <?= $parentFolder!==''?e(basename($parentFolder)):'Library root' ?></a><?php endif; ?>
    <?php foreach($childFolders as $path=>$name): ?><a class="btn btn-outline btn-sm" href="library.php?folder=<?=rawurlencode($path)?>"><i class="bi bi-folder2"></i> <?=e($name)?></a><?php endforeach; ?>
    <?php if(!$childFolders&&$currentFolder===''): ?><span style="font-size:.8rem;color:var(--muted)">No folders yet. Create one above or upload a folder from your computer.</span><?php endif; ?>
  </div>
</div>
<div class="ac"><div class="ach"><div class="act"><?= $currentFolder!==''?e($currentFolder):'Library root' ?></div><div style="font-size:.72rem;color:var(--muted)"><?=count($files)?> files</div></div>
  <div style="overflow-x:auto"><table class="at"><thead><tr><th>File</th><th>Size</th><th>Uploaded</th><th>Sharing</th><th>Actions</th></tr></thead><tbody>
  <?php if(!$files): ?><tr><td colspan="5" style="padding:2rem;text-align:center;color:var(--muted)">No files in this folder yet.</td></tr><?php else: foreach($files as $file): ?>
  <tr>
    <td><a href="preview.php?id=<?=$file['id']?>" target="_blank" rel="noopener" style="font-weight:600;color:var(--ink);text-decoration:none"><?=e($file['title'])?></a><div style="font-size:.72rem;color:var(--muted);margin-top:.2rem"><?=e($file['original_name'])?> · <a href="preview.php?id=<?=$file['id']?>" target="_blank" rel="noopener" style="color:var(--sage)">View file</a></div><?php if($file['description']): ?><div style="font-size:.72rem;color:var(--dim);margin-top:.2rem"><?=e($file['description'])?></div><?php endif; ?></td>
    <td style="white-space:nowrap"><?=number_format((int)$file['file_size']/1024,0)?> KB</td>
    <td style="white-space:nowrap"><?=e(date('M j, Y',strtotime($file['created_at'])))?></td>
    <td><?php if($file['share_token']): ?><span class="badge b-green">Shared</span><input class="ai" readonly value="<?=e($shareBase.$file['share_token'])?>" onclick="this.select()" style="display:block;max-width:260px;margin-top:.35rem;font-size:.68rem"><?php else: ?><span class="badge b-stone">Private</span><?php endif; ?></td>
    <td><div style="display:flex;gap:.35rem;flex-wrap:wrap">
      <form method="POST"><input type="hidden" name="csrf" value="<?=e($csrf)?>"><input type="hidden" name="current_folder" value="<?=e($currentFolder)?>"><input type="hidden" name="id" value="<?=$file['id']?>"><input type="hidden" name="action" value="<?=$file['share_token']?'revoke':'share'?>"><button class="btn btn-outline btn-sm" type="submit"><i class="bi bi-<?=$file['share_token']?'link-45deg':'share'?>"></i> <?=$file['share_token']?'Revoke link':'Create link'?></button></form>
      <form method="POST" onsubmit="return confirm('Delete this file and revoke its share link?')"><input type="hidden" name="csrf" value="<?=e($csrf)?>"><input type="hidden" name="current_folder" value="<?=e($currentFolder)?>"><input type="hidden" name="id" value="<?=$file['id']?>"><input type="hidden" name="action" value="delete"><button class="btn btn-sm" type="submit" style="background:rgba(192,57,43,.07);color:var(--red);border:1px solid rgba(192,57,43,.15)"><i class="bi bi-trash"></i> Delete</button></form>
    </div></td>
  </tr>
  <?php endforeach; endif; ?>
  </tbody></table></div>
</div>
<?php include '_layout_end.php'; ?>
