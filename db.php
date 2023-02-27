<?php

$db = (function(){
    // Connection details
    $host = getenv('MYSQLHOST');
    $dbname = getenv('MYSQLDATABASE');
    $username = getenv('MYSQLUSER');
    $password = getenv('MYSQLPASSWORD');
    // Establish database connection using PDO
    return new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
})();
