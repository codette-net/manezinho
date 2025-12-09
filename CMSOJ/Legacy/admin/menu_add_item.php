<?php
include 'main.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) { echo json_encode(['success'=>false,'msg'=>'Invalid JSON']); exit; }

$stmt = $pdo->prepare("INSERT INTO menu_items
(section_id,name_en,display_type,description_en,unit_1_label,price_1,unit_2_label,price_2,sort_order,is_active)
VALUES (:section_id,:name_en,:description_en,:unit_1_label,:price_1,:unit_2_label,:price_2,:sort_order,:is_active)");
$stmt->execute($data);
echo json_encode(['success'=>true,'id'=>$pdo->lastInsertId()]);
