<?php declare(strict_types=1); $pageTitle='Events'; $activePage='ev';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $a=$_POST['action']??'';
  try{
    if($a==='add') db()->prepare("INSERT INTO events(title,description,event_date,location,is_online,is_published) VALUES(?,?,?,?,?,1)")->execute([trim($_POST['title']),trim($_POST['description']),trim($_POST['event_date']),trim($_POST['location']),(int)($_POST['is_online']??0)]);
    if($a==='toggle') db()->prepare("UPDATE events SET is_published=1-is_published WHERE id=?")->execute([(int)$_POST['id']]);
    if($a==='delete') db()->prepare("DELETE FROM events WHERE id=?")->execute([(int)$_POST['id']]);
  }catch(Throwable){}
  header('Location: events.php'); exit;
}
include '_layout.php';
try{ $evs=db()->query("SELECT * FROM events ORDER BY event_date DESC")->fetchAll(); }catch(Throwable){ $evs=[]; }
?>
<div class="ac" style="margin-bottom:1.1rem">
  <div class="ach"><div class="act">Add Event</div></div>
  <div style="padding:1rem 1.1rem">
    <form method="POST">
      <input type="hidden" name="action" value="add">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:.75rem">
        <div style="grid-column:1/-1"><label class="f-label">Title *</label><input type="text" name="title" class="ai" style="width:100%" required placeholder="e.g. Digital Skills Bootcamp"></div>
        <div><label class="f-label">Date & Time *</label><input type="datetime-local" name="event_date" class="ai" style="width:100%" required></div>
        <div><label class="f-label">Location</label><input type="text" name="location" class="ai" style="width:100%" placeholder="Lab 3 / Online — Zoom"></div>
      </div>
      <div style="margin-bottom:.85rem;display:flex;align-items:center;gap:.65rem">
        <input type="checkbox" name="is_online" value="1" id="iso" style="width:15px;height:15px;accent-color:var(--forest)">
        <label for="iso" style="font-size:.84rem;font-weight:600;cursor:pointer">Online Event</label>
      </div>
      <div style="margin-bottom:.85rem"><label class="f-label">Description</label><textarea name="description" class="ai" style="width:100%;resize:vertical" rows="2" placeholder="Short description…"></textarea></div>
      <button type="submit" class="btn btn-forest btn-sm"><i class="bi bi-plus-circle"></i> Add Event</button>
    </form>
  </div>
</div>
<div class="ac">
  <div class="ach"><div class="act">All Events</div><div style="font-size:.72rem;color:var(--muted)"><?=count($evs)?> events</div></div>
  <div style="overflow-x:auto">
    <table class="at">
      <thead><tr><th>Title</th><th>Date</th><th>Location</th><th>Type</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php if(empty($evs)): ?><tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--muted)">No events yet.</td></tr>
        <?php else: foreach($evs as $ev): ?>
        <tr>
          <td style="font-weight:600;color:var(--ink)"><?=e($ev['title'])?></td>
          <td style="font-size:.78rem;white-space:nowrap"><?=date('M j Y, g:iA',strtotime($ev['event_date']))?></td>
          <td style="font-size:.78rem;color:var(--muted)"><?=e($ev['location']??'TBA')?></td>
          <td><span class="badge b-stone"><?=$ev['is_online']?'Online':'Physical'?></span></td>
          <td><span class="badge <?=$ev['is_published']?'b-green':'b-gold'?>"><?=$ev['is_published']?'Live':'Hidden'?></span></td>
          <td><div style="display:flex;gap:.28rem">
            <form method="POST" style="display:inline"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?=$ev['id']?>"><button type="submit" class="btn btn-outline btn-sm" style="font-size:.72rem;padding:.34rem .7rem"><i class="bi bi-eye<?=$ev['is_published']?'-slash':''?>"></i></button></form>
            <form method="POST" style="display:inline" onsubmit="return confirm('Delete?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$ev['id']?>"><button type="submit" class="btn btn-sm" style="background:rgba(192,57,43,.07);color:var(--red);border:1px solid rgba(192,57,43,.15);font-size:.72rem;padding:.34rem .7rem"><i class="bi bi-trash"></i></button></form>
          </div></td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '_layout_end.php'; ?>
