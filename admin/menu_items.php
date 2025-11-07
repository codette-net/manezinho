<?php
include 'main.php';

$section_id = $_GET['section_id'] ?? 0;
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? 'all';

// Filters
$where = [];
$params = [];
if ($section_id) {
  $where[] = 'i.section_id = :sid';
  $params['sid'] = $section_id;
}
if ($search) {
  $where[] = '(i.name_en LIKE :search OR i.description_en LIKE :search OR i.description_pt LIKE :search)';
  $params['search'] = "%$search%";
}
if ($status !== 'all') {
  $where[] = 'i.is_active = :active';
  $params['active'] = $status == '1' ? 1 : 0;
}
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("
    SELECT i.*, s.name_en AS section_name
    FROM menu_items i
    LEFT JOIN menu_sections s ON s.id = i.section_id
    $where_sql
    ORDER BY s.id
");
$stmt->execute($params);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all sections for the dropdown (for new row)
$sections = $pdo->query("SELECT id, name_en FROM menu_sections WHERE is_active=1 ORDER BY parent_id")->fetchAll(PDO::FETCH_ASSOC);
?>

<?= template_admin_header('Menu Items', 'menu_items', 'view') ?>

<style>
  td input.inline-edit {
    width: 90%;
    padding: 3px;
    border: 1px solid #ccc;
    border-radius: 3px;
  }

  td input.inline-edit:focus {
    border-color: #007bff;
    background: #eef7ff;
  }

  td.truncate {
    max-width: 200px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  td.truncate:hover {
    white-space: normal;
    overflow: visible;
    background: #f5f5f5;
    position: relative;
    z-index: 1;
  }

  .modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.4);
    align-items: center;
    justify-content: center;
  }

  .modal.active {
    display: flex;
  }

  .modal-box {
    background: white;
    padding: 20px;
    border-radius: 10px;
    width: 500px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
  }

  .modal-box textarea {
    width: 100%;
    height: 80px;
  }

  .modal-close {
    float: right;
    cursor: pointer;
  }

  .save-msg {
    color: green;
    font-size: 0.9em;
    display: none;
    margin-left: 5px;
  }

  .add-row input,
  .add-row select {
    width: 100%;
    padding: 3px;
  }

  .editable-name {
    transition: all 300ms ease-in-out;
  }
</style>

<div class="content-title">
  <div class="title">
    <i class="fa-solid fa-utensils"></i>
    <div class="txt">
      <h2>Menu Items</h2>
      <p>Quick edit, add, and manage multilingual items inline.</p>
    </div>
  </div>
</div>

<div class="content-block">
  <div class="table">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Section</th>
          <th>EN Name</th>
          <th>PT Name</th>
          <th>Type</th>
          <th>EN Desc</th>
          <th>PT Desc</th>
          <th>Unit 1</th>
          <th>Price 1</th>
          <th>Unit 2</th>
          <th>Price 2</th>
          <th>Sort</th>
          <th>Active</th>
          <th></th>
        </tr>
      </thead>

      <!-- Add-new row -->
      <tbody>
        <tr class="add-row">
          <td>New</td>
          <td>
            <select id="new_section_id">
              <?php foreach ($sections as $s): ?>
                <option value="<?= $s['id'] ?>"><?= $s['name_en'] ?></option>
              <?php endforeach; ?>
            </select>
          </td>
          <td><input id="new_name_en" placeholder="Name"></td>
          <td><input id="new_name_pt" placeholder="Name"></td>
          <td>
            <select id="new_display_type">
              <option value="item">Normal Item</option>
              <option value="thead">Header Row (Table Head)</option>
              <option value="th">Subheader Row (Inside Table)</option>
              <option value="divider">Divider (Colspan)</option>
            </select>
          </td>
          <td><input id="new_desc_en" placeholder="Short description (EN)"></td>
          <td><input id="new_desc_pt" placeholder="Short description (EN)"></td>
          <td><input id="new_unit_1" placeholder="Unit 1"></td>
          <td><input id="new_price_1" placeholder="Price 1"></td>
          <td><input id="new_unit_2" placeholder="Unit 2"></td>
          <td><input id="new_price_2" placeholder="Price 2"></td>
          <td><input id="new_sort" type="number" value="0"></td>
          <td><input type="checkbox" id="new_active" checked></td>
          <td><button id="btnAddNew" class="btn">+ Add</button></td>
        </tr>
      </tbody>

      <tbody>
        <?php foreach ($items as $i): ?>
          <tr data-id="<?= $i['id'] ?>" data-descen="<?= htmlspecialchars($i['description_en']) ?>" data-descpt="<?= htmlspecialchars($i['description_pt']) ?>">
            <td><?= $i['id'] ?></td>
            <td><?= $i['section_name'] ?></td>
            <td class="truncate">
              <button class="btn-edit" title="Edit text">✎</button>
              <span class="editable-name"><?= $i['name_en'] ?></span>
            </td>
            <td class="truncate">
              <span class="editable-name-pt">
                <?= $i['name_pt'] ?>

              </span>
            </td>
            <td>
              <select class="inline-edit" data-field="display_type">
                <option value="item" <?= $i['display_type'] == 'item' ? 'selected' : '' ?>>item</option>
                <option value="thead" <?= $i['display_type'] == 'thead' ? 'selected' : '' ?>>thead</option>
                <option value="th" <?= $i['display_type'] == 'th' ? 'selected' : '' ?>>th</option>
                <option value="divider" <?= $i['display_type'] == 'divider' ? 'selected' : '' ?>>divider</option>
              </select>
            </td>

            <td class="truncate"><?= $i['description_en'] ?></td>
            <td class="truncate"><?= $i['description_pt'] ?></td>
            <td><input class="inline-edit" data-field="unit_1_label" value="<?= $i['unit_1_label'] ?>"></td>
            <td><input class="inline-edit" data-field="price_1" value="<?= $i['price_1'] ?>"></td>
            <td><input class="inline-edit" data-field="unit_2_label" value="<?= $i['unit_2_label'] ?>"></td>
            <td><input class="inline-edit" data-field="price_2" value="<?= $i['price_2'] ?>"></td>
            <td><input class="inline-edit" data-field="sort_order" value="<?= $i['sort_order'] ?>" type="number" style="width:60px"></td>
            <td><input type="checkbox" class="toggle-active" <?= $i['is_active'] ? 'checked' : '' ?>></td>
            <td><a href="menu_item.php?id=<?= $i['id'] ?>" class="link1">Full Edit</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal -->
<div class="modal" id="textModal">
  <div class="modal-box">
    <span class="modal-close">×</span>
    <h3>Edit Item Text</h3>
    <label>Name (EN)</label>
    <input type="text" id="modalName" style="width:100%">
    <label for="">Name (PT)</label>
    <input type="text" id="modalNamePt" style="width:100%">
    <label>Description (EN)</label>
    <textarea id="modalDescEn"></textarea>
    <label>Description (PT)</label>
    <textarea id="modalDescPt"></textarea>
    <button id="modalSave" class="btn">Save</button>
  </div>
</div>

<script>
  async function updateField(id, field, value) {
    const res = await fetch('menu_item_update.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({
        id,
        field,
        value
      })
    });
    const data = await res.json();
    return data.success;
  }

  // Inline updates
  document.querySelectorAll('.inline-edit').forEach(inp => {
    inp.addEventListener('change', async e => {
      const tr = e.target.closest('tr');
      const id = tr.dataset.id;
      const field = e.target.dataset.field;
      const value = e.target.value;
      const ok = await updateField(id, field, value);
      if (ok) {
        e.target.style.background = '#d1ffd1';
        setTimeout(() => e.target.style.background = '', 800);
      }
    });
  });

  // Active toggle
  document.querySelectorAll('.toggle-active').forEach(chk => {
    chk.addEventListener('change', e => {
      const tr = e.target.closest('tr');
      updateField(tr.dataset.id, 'is_active', e.target.checked ? 1 : 0);
    });
  });

  // Modal editing
  const modal = document.getElementById('textModal');
  let currentRow = null;
  document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', e => {
      currentRow = e.target.closest('tr');
      modal.classList.add('active');
      document.getElementById('modalName').value = currentRow.querySelector('.editable-name').textContent.trim();
      document.getElementById('modalNamePt').value = currentRow.querySelector('.editable-name-pt').textContent.trim();
      document.getElementById('modalDescEn').value = currentRow.dataset.descen || '';
      document.getElementById('modalDescPt').value = currentRow.dataset.descpt || '';
    });
  });
  document.querySelector('.modal-close').onclick = () => modal.classList.remove('active');
  document.getElementById('modalSave').onclick = async () => {
    const id = currentRow.dataset.id;
    const name = document.getElementById('modalName').value;
    const namePt = document.getElementById('modalNamePt').value;
    const descEn = document.getElementById('modalDescEn').value;
    const descPt = document.getElementById('modalDescPt').value;

    await updateField(id, 'name_en', name);
    await updateField(id, 'name_pt', namePt);
    await updateField(id, 'description_en', descEn);
    await updateField(id, 'description_pt', descPt);
    modal.classList.remove('active');
    currentRow.querySelector('.editable-name').textContent = name;
    currentRow.dataset.descen = descEn;
    currentRow.dataset.descpt = descPt;
    currentRow.children[3].textContent = descEn.substring(0, 80);
    currentRow.children[4].textContent = descPt.substring(0, 80);
    currentRow.style.background = '#d1ffd1';
    setTimeout(() => currentRow.style.background = '', 2000);
  };

  // Add new row
  document.getElementById('btnAddNew').addEventListener('click', async () => {
    const data = {
      section_id: document.getElementById('new_section_id').value,
      name_en: document.getElementById('new_name').value,
      description_en: document.getElementById('new_desc_en').value,
      unit_1_label: document.getElementById('new_unit_1').value,
      price_1: document.getElementById('new_price_1').value,
      unit_2_label: document.getElementById('new_unit_2').value,
      price_2: document.getElementById('new_price_2').value,
      sort_order: document.getElementById('new_sort').value,
      is_active: document.getElementById('new_active').checked ? 1 : 0
    };
    const res = await fetch('menu_item_add.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    });
    const js = await res.json();
    if (js.success) location.reload();
  });
</script>

<?= template_admin_footer() ?>