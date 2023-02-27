<?php
header('Content-Type: application/json');
require_once("db.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || strpos($_SERVER['REQUEST_URI'], '/check-subscription.php') === false) {
    http_response_code(400);
    echo json_encode(array('status' => 'error', 'message' => 'No subscription data provided'));
    exit;
}

$body = file_get_contents('php://input');
$subscription = json_decode($body, true);
// Check if subscription already exists in the database
$sql = "SELECT * FROM push_subscriptions WHERE endpoint = :endpoint";
$stmt = $db->prepare($sql);
$stmt->execute(array(':endpoint' => $subscription['endpoint']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
    echo json_encode(array('status' => 'success', 'exists' => true));
} else {
    echo json_encode(array('status' => 'success', 'exists' => false));
}

