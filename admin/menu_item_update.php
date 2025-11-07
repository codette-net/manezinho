<?php
include 'main.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'msg' => 'Invalid request']);
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$field = $_POST['field'] ?? '';
$value = $_POST['value'] ?? '';

$allowed = ['is_active','sort_order','name_en','name_pt','description_en','description_pt',
            'unit_1_label','unit_2_label','price_1','price_2','display_type'];

if (!$id || !in_array($field, $allowed, true)) {
    echo json_encode(['success' => false, 'msg' => 'Invalid field']);
    exit;
}

// Update safely
$stmt = $pdo->prepare("UPDATE menu_items SET `$field` = :val, updated_at = NOW() WHERE id = :id");
$stmt->execute(['val' => $value, 'id' => $id]);

echo json_encode(['success' => true]);
