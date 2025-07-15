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

function generateTicketCode(): string {
    // e.g. YYMMDD + random 8 characters
    $datePart = date('ymd'); // 6 digits
    $randomPart = substr(strtoupper(bin2hex(random_bytes(5))), 0, 3); // 3 characters
    return $datePart . $randomPart; // total 14 characters
}

// Generate Ticket Code
$ticketCode = generateTicketCode();

// Insert into ticket table
$stmt = $pdo->prepare("INSERT INTO webby_ticket (group_id, created, number, user_id, status_id, dept_id) VALUES (?, NOW(), '$ticketCode', 1, 1, 1)");
$stmt->execute([$group_id]);
$ticket_id = $pdo->lastInsertId();

// Insert subject into webby_ticket__cdata
$stmt = $pdo->prepare("INSERT INTO webby_ticket__cdata (ticket_id, subject) VALUES (?, ?)");
$stmt->execute([$ticket_id, $subject]);

// Insert message into webby_thread
$stmt = $pdo->prepare("
    INSERT INTO webby_thread (object_type, object_id, created)
    VALUES ('T', ?, NOW())
");
$stmt->execute([$ticket_id]);
$thread_id = $pdo->lastInsertId();

// Insert message into webby_thread_entry
$stmt = $pdo->prepare("
    INSERT INTO webby_thread_entry (thread_id, user_id, type, body, format, created)
    VALUES ($thread_id, 1, 'M', '$message', 'html', NOW())
");
$stmt->execute();

echo json_encode(['success' => true, 'ticket_id' => $ticket_id]);
