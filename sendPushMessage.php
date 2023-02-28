<?php
require_once 'vendor/autoload.php';
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Dotenv\Dotenv;
if(!isset($_ENV['APP_ENV']) || $_ENV['APP_ENV'] !== 'production'){
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
} 
require_once 'db.php';


// Send push notifications to all subscribed clients
function sendPushMessage($payload, $vapid, $db) {
    $sql = "SELECT endpoint, keys_json FROM push_subscriptions";
    $stmt = $db->query($sql);
    $rows = $stmt->fetchAll();
    foreach($rows as $row){
        $keys = json_decode($row['keys_json'], true);
        $subscription = Subscription::create([
            'endpoint' => $row['endpoint'],
            'publicKey' => $keys['p256dh'],
            'authToken' => $keys['auth'],
        ]);
        $subject = 'mailto:'.$_ENV['PERSONAL_EMAIL'];
        $webPush = new WebPush(array(
            'VAPID' => array(
                'subject' => $subject,
                'publicKey' => $vapid['public'],
                'privateKey' => $vapid['private']
            ),
        ));

        $webPush->sendOneNotification($subscription, json_encode($payload));
        echo "Notification sent";
    }
}

$payload = [    
    'notification' => [
        'title' => 'Incoming call',        
        'body' => 'Incoming call',        
        'icon' => 'public/icon.png',        
        'vibrate' => [200, 100, 200, 100, 200, 100, 200],
        'actions' => [            [                
            'action' => 'answer',                
            'icon' => 'public/answer.png',                
            'title' => 'Answer',            
        ],
        [                
            'action' => 'cancel',                
            'icon' => 'public/cancel.png',                
            'title' => 'Cancel',            
        ],
        ],
        'data' => [            
            'primaryKey' => 1,        
        ],
    ],
];

$vapid = ['public' => $_ENV['VAPID_PUBLIC'], 'private' => $_ENV['VAPID_PRIVATE']];
sendPushMessage($payload, $vapid, $db);
