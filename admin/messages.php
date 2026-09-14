<?php declare(strict_types=1); $pageTitle='Messages'; $activePage='msg'; include '_layout.php';
try{ $msgs=db()->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 60")->fetchAll(); db()->query("UPDATE contact_messages SET is_read=1"); }catch(Throwable){ $msgs=[]; }
?>
<div class="ac">
  <div class="ach"><div class="act">Contact Messages</div><div style="font-size:.72rem;color:var(--muted)"><?=count($msgs)?> messages</div></div>
  <div style="overflow-x:auto">
    <table class="at">
      <thead><tr><th>Name</th><th>Email</th><th>Subject</th><th>Message</th><th>Date</th></tr></thead>
      <tbody>
        <?php if(empty($msgs)): ?><tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--muted)">No messages yet.</td></tr>
        <?php else: foreach($msgs as $m): ?>
        <tr style="<?=!$m['is_read']?'background:#FEFCE8':''?>">
          <td style="font-weight:600;color:var(--ink)"><?=e($m['name'])?><?=!$m['is_read']?'<span class="badge b-gold" style="margin-left:.35rem;font-size:.58rem">New</span>':''?></td>
          <td><a href="mailto:<?=e($m['email'])?>" style="color:var(--sage);text-decoration:none;font-size:.78rem"><?=e($m['email'])?></a></td>
          <td style="font-size:.78rem;color:var(--muted)"><?=e($m['subject']??'—')?></td>
          <td style="max-width:200px"><div style="font-size:.78rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:180px" title="<?=e($m['message'])?>"><?=e($m['message'])?></div></td>
          <td style="font-size:.7rem;color:var(--dim);white-space:nowrap"><?=date('M j, g:iA',strtotime($m['created_at']))?></td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '_layout_end.php'; ?>
