<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$group_id = $_GET['group_id'] ?? null;
if (!$group_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing group_id']);
    exit;
}

$stmt = $pdo->prepare("
    SELECT
        t.ticket_id,
        t.number,
        c.subject,
        t.status_id,
        t.created
    FROM webby_ticket t
    LEFT JOIN webby_ticket__cdata c ON t.ticket_id = c.ticket_id
    WHERE t.group_id = ?
");

$stmt->execute([$group_id]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['tickets' => $tickets]);
