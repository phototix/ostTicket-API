<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$group_id = $_GET['group_id'] ?? null;
if (!$group_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing group_id']);
    exit;
}

$sql = "SELECT 
    COUNT(*) AS total,
    SUM(status_id = 1) AS open,
    SUM(status_id = 3) AS closed
FROM webby_ticket
WHERE group_id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$group_id]);
$summary = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode(['summary' => $summary]);
