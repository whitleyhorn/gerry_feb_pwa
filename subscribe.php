<?php
require_once 'vendor/autoload.php';
use Dotenv\Dotenv;
if(!isset($_ENV['APP_ENV']) || $_ENV['APP_ENV'] !== 'production'){
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
} 
require_once("db.php");

// Listen for POST requests to /subscribe and store the push subscription endpoint in the database
if ($_SERVER['REQUEST_METHOD'] == 'POST' && strpos($_SERVER['REQUEST_URI'], '/subscribe.php') !== false) {
    $body = file_get_contents('php://input');
    $subscription = json_decode($body, true);
    $endpoint = $subscription['endpoint'];
    $keys = json_encode($subscription['keys']);
    $sql = "INSERT INTO push_subscriptions (endpoint, keys_json) VALUES (:endpoint, :keys)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':endpoint', $endpoint);
    $stmt->bindParam(':keys', $keys);
    $stmt->execute();
}

