<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$ticket_id = $_REQUEST['ticket_id'] ?? null;
$data = json_decode(file_get_contents("php://input"), true);
$status_id = $data['status_id'] ?? null;
$subject = $data['subject'] ?? null;

if (!$ticket_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Ticket ID required']);
    exit;
}

$update = [];
$params = [];

if ($status_id) {
    $update[] = "status_id = ?";
    $params[] = $status_id;
}
if ($subject) {
    $update[] = "subject = ?";
    $params[] = $subject;
}

if (!$update) {
    echo json_encode(['error' => 'Nothing to update']);
    exit;
}

$params[] = $ticket_id;
$sql = "UPDATE webby_ticket SET " . implode(", ", $update) . " WHERE ticket_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

echo json_encode(['success' => true]);
