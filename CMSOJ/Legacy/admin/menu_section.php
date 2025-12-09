<?php
include 'main.php';

// Determine mode
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;

// Fetch top-level sections for parent selector
$parents = $pdo->query("SELECT id, name_en, name_pt FROM menu_sections WHERE parent_id IS NULL ORDER BY name_en")->fetchAll(PDO::FETCH_ASSOC);

// Default data
$section = [
    'id' => null,
    'parent_id' => null,
    'name_en' => '',
    'name_pt' => '',
    'description_en' => '',
    'description_pt' => '',
    'sort_order' => 0,
    'is_active' => 1
];

if ($editing) {
    $stmt = $pdo->prepare("SELECT * FROM menu_sections WHERE id = ?");
    $stmt->execute([$id]);
    $section = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$section) exit('Section not found');
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
        'name_en' => trim($_POST['name_en'] ?? ''),
        'name_pt' => trim($_POST['name_pt'] ?? ''),
        'description_en' => trim($_POST['description_en'] ?? ''),
        'description_pt' => trim($_POST['description_pt'] ?? ''),
        'sort_order' => (int)($_POST['sort_order'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];

    if ($editing) {
        $stmt = $pdo->prepare("
            UPDATE menu_sections SET
                parent_id = :parent_id,
                name_en = :name_en,
                name_pt = :name_pt,
                description_en = :description_en,
                description_pt = :description_pt,
                sort_order = :sort_order,
                is_active = :is_active,
                updated_at = NOW()
            WHERE id = :id
        ");
        $data['id'] = $id;
        $stmt->execute($data);
        header('Location: menu_sections.php?success_msg=2');
        exit;
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO menu_sections (parent_id,name_en,name_pt,description_en,description_pt,sort_order,is_active)
            VALUES (:parent_id,:name_en,:name_pt,:description_en,:description_pt,:sort_order,:is_active)
        ");
        $stmt->execute($data);
        header('Location: menu_sections.php?success_msg=1');
        exit;
    }
}
?>

<?=template_admin_header(($editing?'Edit':'Add').' Section', 'menu_sections', 'form')?>

<div class="content-title">
  <div class="title">
    <i class="fa-solid fa-list"></i>
    <div class="txt">
      <h2><?=$editing ? 'Edit Section' : 'Add New Section'?></h2>
      <p>Manage menu categories and subsections in both English and Portuguese.</p>
    </div>
  </div>
</div>

<form method="post" class="content-form">
  <h3>Basic Info</h3>
  <div class="form">
    <label>
      Parent Section
      <select name="parent_id">
        <option value="">— Top Level —</option>
        <?php foreach ($parents as $p): ?>
          <?php if ($editing && $p['id'] == $section['id']) continue; // prevent circular parent ?>
          <option value="<?=$p['id']?>" <?=$section['parent_id']==$p['id']?'selected':''?>>
            <?=$p['name_en']?><?php if($p['name_pt']) echo " / ".$p['name_pt']; ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <div class="form-group">
      <label>Name (EN)
        <input type="text" name="name_en" value="<?=htmlspecialchars($section['name_en'])?>" required>
      </label>
      <label>Name (PT)
        <input type="text" name="name_pt" value="<?=htmlspecialchars($section['name_pt'])?>">
      </label>
    </div>

    <label>Description (EN)
      <textarea name="description_en" rows="3"><?=htmlspecialchars($section['description_en'])?></textarea>
    </label>
    <label>Description (PT)
      <textarea name="description_pt" rows="3"><?=htmlspecialchars($section['description_pt'])?></textarea>
    </label>
  </div>

  <h3>Settings</h3>
  <div class="form">
    <div class="form-group">
      <label>Sort Order
        <input type="number" name="sort_order" value="<?=$section['sort_order']?>" min="0">
      </label>
      <label>Status
        <input type="checkbox" name="is_active" <?=$section['is_active']?'checked':''?>> Active
      </label>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn">Save Section</button>
    <a href="menu_sections.php" class="btn alt">Cancel</a>
  </div>
</form>

<?=template_admin_footer()?>
