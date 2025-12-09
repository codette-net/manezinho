<?php
include 'main.php';

// Determine if we’re editing or adding
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;

// Fetch sections for dropdown
$sections = $pdo->query("SELECT id, name_en FROM menu_sections ORDER BY parent_id, name_en")->fetchAll(PDO::FETCH_ASSOC);

// Default blank item
$item = [
  'id' => null,
  'section_id' => '',
  'display_type' => 'item',
  'name_en' => '',
  'description_en' => '',
  'description_pt' => '',
  'unit_1_label' => '',
  'price_1' => '',
  'unit_2_label' => '',
  'price_2' => '',
  'sort_order' => 0,
  'is_active' => 1
];

if ($editing) {
  $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
  $stmt->execute([$id]);
  $item = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$item) exit('Item not found');
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = [
    'section_id' => $_POST['section_id'] ?? null,
    'name_en' => trim($_POST['name_en'] ?? ''),
    'description_en' => trim($_POST['description_en'] ?? ''),
    'description_pt' => trim($_POST['description_pt'] ?? ''),
    'unit_1_label' => trim($_POST['unit_1_label'] ?? ''),
    'price_1' => trim($_POST['price_1'] ?? ''),
    'unit_2_label' => trim($_POST['unit_2_label'] ?? ''),
    'price_2' => trim($_POST['price_2'] ?? ''),
    'sort_order' => (int)($_POST['sort_order'] ?? 0),
    'is_active' => isset($_POST['is_active']) ? 1 : 0
  ];

  if ($editing) {
    $stmt = $pdo->prepare("
            UPDATE menu_items SET
                section_id = :section_id,
                name_en = :name_en,
                description_en = :description_en,
                description_pt = :description_pt,
                unit_1_label = :unit_1_label,
                price_1 = :price_1,
                unit_2_label = :unit_2_label,
                price_2 = :price_2,
                sort_order = :sort_order,
                is_active = :is_active,
                updated_at = NOW()
            WHERE id = :id
        ");
    $data['id'] = $id;
    $stmt->execute($data);
    header('Location: menu_items.php?success_msg=2');
    exit;
  } else {
    $stmt = $pdo->prepare("
            INSERT INTO menu_items
            (section_id,name_en,description_en,description_pt,unit_1_label,price_1,unit_2_label,price_2,sort_order,is_active)
            VALUES (:section_id,:name_en,:description_en,:description_pt,:unit_1_label,:price_1,:unit_2_label,:price_2,:sort_order,:is_active)
        ");
    $stmt->execute($data);
    header('Location: menu_items.php?success_msg=1');
    exit;
  }
}
?>

<?= template_admin_header(($editing ? 'Edit' : 'Add') . ' Item', 'menu_items', 'form') ?>

<div class="content-title">
  <div class="title">
    <i class="fa-solid fa-utensils"></i>
    <div class="txt">
      <h2><?= $editing ? 'Edit Item' : 'Add New Item' ?></h2>
      <p>Complete all fields for multilingual support and pricing.</p>
    </div>
  </div>
</div>

<form method="post" class="content-form">
  <h3>Basic Info</h3>
  <div class="form responsive-width-100">
    <div class="form-group">
      <label>
        Display Type
        <select name="display_type">
          <option value="item" <?= $item['display_type'] == 'item' ? 'selected' : '' ?>>Normal Item</option>
          <option value="thead" <?= $item['display_type'] == 'thead' ? 'selected' : '' ?>>Header Row (Table Head)</option>
          <option value="th" <?= $item['display_type'] == 'th' ? 'selected' : '' ?>>Subheader Row (Inside Table)</option>
          <option value="divider" <?= $item['display_type'] == 'divider' ? 'selected' : '' ?>>Divider (Colspan)</option>
        </select>
      </label>

      <label>
        Section
        <select name="section_id" required>
          <option value="">Select a section...</option>
          <?php foreach ($sections as $s): ?>
            <option value="<?= $s['id'] ?>" <?= $item['section_id'] == $s['id'] ? 'selected' : '' ?>><?= $s['name_en'] ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>
        Name (EN)
        <input type="text" name="name_en" value="<?= htmlspecialchars($item['name_en']) ?>" required>
      </label>
    </div>

    <div class="form-group">
      <label>
        Description (EN)
        <textarea name="description_en" rows="3"><?= htmlspecialchars($item['description_en']) ?></textarea>
      </label>
      <label>
        Description (PT)
        <textarea name="description_pt" rows="3"><?= htmlspecialchars($item['description_pt']) ?></textarea>
      </label>
    </div>
  </div>

  <h3>Units & Prices</h3>
  <div class="form">
    <div class="form-group">
      <label>Unit 1
        <input type="text" name="unit_1_label" value="<?= htmlspecialchars($item['unit_1_label']) ?>">
      </label>
      <label>Price 1
        <input type="text" name="price_1" value="<?= htmlspecialchars($item['price_1']) ?>">
      </label>
    </div>

    <div class="form-group">
      <label>Unit 2
        <input type="text" name="unit_2_label" value="<?= htmlspecialchars($item['unit_2_label']) ?>">
      </label>
      <label>Price 2
        <input type="text" name="price_2" value="<?= htmlspecialchars($item['price_2']) ?>">
      </label>
    </div>
  </div>

  <h3>Settings</h3>
  <div class="form">
    <div class="form-group">
      <label>Sort Order
        <input type="number" name="sort_order" value="<?= $item['sort_order'] ?>" min="0">
      </label>
      <label>Status
        <input type="checkbox" name="is_active" <?= $item['is_active'] ? 'checked' : '' ?>> Active
      </label>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn">Save Item</button>
    <a href="menu_items.php" class="btn alt">Cancel</a>
  </div>
</form>

<?= template_admin_footer() ?>