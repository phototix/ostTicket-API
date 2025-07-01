<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$group_id = $_REQUEST['group_id'] ?? null;
$subject  = $_REQUEST['subject'] ?? null;
$message  = $_REQUEST['message'] ?? 'No details';

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
