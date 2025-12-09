<?php
include 'main.php';

// Handle delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM menu_sections WHERE id = ?');
    $stmt->execute([$_GET['delete']]);
    header('Location: menu_sections.php?success_msg=3');
    exit;
}

// Get all sections
$stmt = $pdo->prepare('SELECT * FROM menu_sections ORDER BY parent_id ASC, sort_order ASC');
$stmt->execute();
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

$tree = [];
foreach ($sections as $s) {
    if (!$s['parent_id']) {
        $tree[$s['id']] = $s;
        $tree[$s['id']]['children'] = [];
    } else {
        $tree[$s['parent_id']]['children'][] = $s;
    }
}

if (isset($_GET['success_msg'])) {
    $msg_map = [1=>'Section created successfully!',2=>'Section updated successfully!',3=>'Section deleted successfully!'];
    $success_msg = $msg_map[$_GET['success_msg']] ?? null;
}
?>

<?=template_admin_header('Menu Sections', 'menu_sections', 'view')?>

<style>
.inline-edit { width:70px; padding:3px; border:1px solid #ccc; border-radius:3px; }
.inline-edit:focus { border-color:#007bff; background:#eef7ff; }
.toggle-active { cursor:pointer; }
</style>

<div class="content-title">
  <div class="title">
    <i class="fa-solid fa-list"></i>
    <div class="txt">
      <h2>Menu Sections</h2>
      <p>Manage multilingual sections and subsections.</p>
    </div>
  </div>
  <a href="menu_section.php" class="btn">+ New Section</a>
</div>

<?php if (!empty($success_msg)): ?>
<div class="msg success"><i class="fas fa-check-circle"></i><p><?=$success_msg?></p><i class="fas fa-times"></i></div>
<?php endif; ?>

<div class="content-block">
  <div class="table">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Name (EN)</th>
          <th>Name (PT)</th>
          <th>Parent</th>
          <th>Sort</th>
          <th>Active</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($sections)): ?>
          <tr><td colspan="7" class="no-results">No sections found</td></tr>
        <?php else: ?>
          <?php foreach ($tree as $main): ?>
            <tr data-id="<?=$main['id']?>">
              <td><?=$main['id']?></td>
              <td><strong><?=$main['name_en']?></strong></td>
              <td><?=$main['name_pt']?></td>
              <td>-</td>
              <td><input class="inline-edit" data-field="sort_order" value="<?=$main['sort_order']?>"></td>
              <td><input type="checkbox" class="toggle-active" <?=$main['is_active']?'checked':''?>></td>
              <td>
                <a href="menu_items.php?section_id=<?=$main['id']?>" class="link1">View Items</a>
                <a href="menu_section.php?id=<?=$main['id']?>" class="link1">Edit</a>
                <a href="?delete=<?=$main['id']?>" onclick="return confirm('Delete this section?')" class="link1">Delete</a>
              </td>
            </tr>
            <?php foreach ($main['children'] as $sub): ?>
            <tr data-id="<?=$sub['id']?>">
              <td><?=$sub['id']?></td>
              <td>&nbsp;&nbsp;&nbsp;↳ <?=$sub['name_en']?></td>
              <td><?=$sub['name_pt']?></td>
              <td><?=$main['name_en']?></td>
              <td><input class="inline-edit" data-field="sort_order" value="<?=$sub['sort_order']?>"></td>
              <td><input type="checkbox" class="toggle-active" <?=$sub['is_active']?'checked':''?>></td>
              <td>
                <a href="menu_items.php?section_id=<?=$sub['id']?>" class="link1">View Items</a>
                <a href="menu_section.php?id=<?=$sub['id']?>" class="link1">Edit</a>
                <a href="?delete=<?=$sub['id']?>" onclick="return confirm('Delete this subsection?')" class="link1">Delete</a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
async function updateSection(id, field, value){
  await fetch('menu_section_update.php',{
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:new URLSearchParams({id, field, value})
  });
}

document.querySelectorAll('.inline-edit').forEach(inp=>{
  inp.addEventListener('change', e=>{
    const id = e.target.closest('tr').dataset.id;
    updateSection(id, 'sort_order', e.target.value);
    e.target.style.background='#d1ffd1';
    setTimeout(()=>e.target.style.background='',800);
  });
});
document.querySelectorAll('.toggle-active').forEach(chk=>{
  chk.addEventListener('change', e=>{
    const id = e.target.closest('tr').dataset.id;
    updateSection(id,'is_active', e.target.checked ? 1 : 0);
  });
});
</script>

<?=template_admin_footer()?>
