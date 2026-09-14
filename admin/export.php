<?php
declare(strict_types=1);
require_once __DIR__.'/../config/bootstrap.php';
requireAdmin();
$type=$_GET['type']??'members';
if($type==='members_excel'){
  header('Content-Type: application/vnd.ms-excel; charset=utf-8');
  header('Content-Disposition: attachment; filename="ajira_members_'.date('Y-m-d').'.xls"');
  echo "<table border=\"1\"><tr><th>ID</th><th>Full Name</th><th>Reg Number</th><th>Phone</th><th>Email</th><th>Course/Year</th><th>Type</th><th>Status</th><th>Expires</th><th>Registered</th><th>Activated</th></tr>";
  try{ foreach(db()->query("SELECT * FROM members ORDER BY registered_at DESC") as $r){ $expired=$r['is_active']&&!empty($r['expires_at'])&&strtotime($r['expires_at'])<time(); $status=$expired?'Expired':($r['is_active']?'Active':'Pending'); echo '<tr>'.implode('',array_map(fn($v)=>'<td>'.htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8').'</td>',[$r['id'],$r['full_name'],$r['reg_number'],$r['phone'],$r['email'],$r['course_year'],$r['member_type'],$status,$r['expires_at']??'',$r['registered_at'],$r['activated_at']??''])).'</tr>'; } }catch(Throwable){}
  echo '</table>'; exit;
}
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="ajira_'.$type.'_'.date('Y-m-d').'.csv"');
$out=fopen('php://output','w'); fprintf($out,chr(0xEF).chr(0xBB).chr(0xBF));
try{
  if($type==='members'){
    fputcsv($out,['ID','Full Name','Reg Number','Phone','Email','Course/Year','Type','Status','Expires','Registered','Activated']);
    foreach(db()->query("SELECT * FROM members ORDER BY registered_at DESC") as $r)
      fputcsv($out,[$r['id'],$r['full_name'],$r['reg_number'],$r['phone'],$r['email'],$r['course_year'],$r['member_type'],$r['is_active']?'Active':'Pending',$r['expires_at']??'',$r['registered_at'],$r['activated_at']??'']);
  }else{
    fputcsv($out,['ID','Name','Email','Subject','Message','Read','Date']);
    foreach(db()->query("SELECT * FROM contact_messages ORDER BY created_at DESC") as $r)
      fputcsv($out,[$r['id'],$r['name'],$r['email'],$r['subject'],$r['message'],$r['is_read']?'Yes':'No',$r['created_at']]);
  }
}catch(Throwable){} fclose($out); exit;
