<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$data = json_decode(file_get_contents("php://input"), true);
$group_id = $data['group_id'] ?? null;
$subject = $data['subject'] ?? null;
$message = $data['message'] ?? 'No details';

if (!$group_id || !$subject) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

// Insert into ticket table
$stmt = $pdo->prepare("INSERT INTO webby_ticket (subject, group_id, created) VALUES (?, ?, NOW())");
$stmt->execute([$subject, $group_id]);
$ticket_id = $pdo->lastInsertId();

echo json_encode(['success' => true, 'ticket_id' => $ticket_id]);
