<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);  // this removes query string
$method = $_SERVER['REQUEST_METHOD'];

switch (true) {
    case $request === '/tickets' && $method === 'GET':
        require 'endpoints/list_tickets.php';
        break;

    case $request === '/tickets' && $method === 'POST':
        require 'endpoints/create_ticket.php';
        break;

    case preg_match('/\/tickets\/(\d+)/', $request, $matches) && $method === 'PUT':
        $_REQUEST['ticket_id'] = $matches[1];
        require 'endpoints/update_ticket.php';
        break;

    case $request === '/tickets/summary' && $method === 'GET':
        require 'endpoints/ticket_summary.php';
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        break;
}
