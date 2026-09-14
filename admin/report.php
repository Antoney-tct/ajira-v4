<?php
declare(strict_types=1);
require_once __DIR__.'/../config/bootstrap.php';
requireAdmin();
$members=[];
try{$members=db()->query('SELECT * FROM members ORDER BY registered_at DESC')->fetchAll();}catch(Throwable){}
function reportStatus(array $member):string{
  if($member['is_active']&&!empty($member['expires_at'])&&strtotime($member['expires_at'])<time()) return 'Expired';
  return $member['is_active']?'Active':'Pending';
}
?><!doctype html>
<html lang="en"><head><meta charset="utf-8"><title>AJira Members Report</title>
<style>
body{font-family:Arial,sans-serif;color:#18231a;margin:28px;font-size:12px}h1{margin:0 0 5px;font-size:22px}p{color:#647064;margin:0 0 18px}.actions{margin-bottom:18px}button{background:#173d25;color:#fff;border:0;padding:9px 14px;border-radius:4px;cursor:pointer}table{border-collapse:collapse;width:100%}th,td{border:1px solid #cfd8cf;padding:7px;text-align:left}th{background:#173d25;color:white;font-size:11px}tr:nth-child(even){background:#f4f6f2}.status{font-weight:bold}@media print{.actions{display:none}body{margin:12px;font-size:9px}h1{font-size:16px}th,td{padding:4px} @page{size:landscape;margin:10mm}}
</style></head><body>
<div class="actions"><button onclick="window.print()">Print / Save as PDF</button> <button onclick="window.close()">Close</button></div>
<h1>AJira Digital Club - Members Report</h1><p>Generated <?=e(date('F j, Y, g:i A'))?> · <?=count($members)?> members</p>
<table><thead><tr><th>#</th><th>Full name</th><th>Registration no.</th><th>Phone</th><th>Email</th><th>Course / year</th><th>Type</th><th>Status</th><th>Expires</th><th>Registered</th><th>Activated</th></tr></thead><tbody>
<?php foreach($members as $member): ?><tr><td><?=e((string)$member['id'])?></td><td><?=e($member['full_name'])?></td><td><?=e($member['reg_number'])?></td><td><?=e($member['phone'])?></td><td><?=e($member['email'])?></td><td><?=e($member['course_year'])?></td><td><?=e(ucfirst($member['member_type']))?></td><td class="status"><?=e(reportStatus($member))?></td><td><?=!empty($member['expires_at'])?e(date('M j, Y',strtotime($member['expires_at']))):'—'?></td><td><?=e(date('M j, Y',strtotime($member['registered_at'])))?></td><td><?=!empty($member['activated_at'])?e(date('M j, Y',strtotime($member['activated_at']))):'—'?></td></tr><?php endforeach; ?>
<?php if(!$members): ?><tr><td colspan="11">No members found.</td></tr><?php endif; ?></tbody></table>
</body></html>
