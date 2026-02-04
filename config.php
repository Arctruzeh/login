<?php
/* Database credentials. */
$url = getenv('MYSQL_URL') ?: getenv('DATABASE_URL');

if ($url) {
    $dbparts = parse_url($url);
    $hostname = $dbparts['host'];
    $username = $dbparts['user'];
    $password = $dbparts['pass'];
    $database = ltrim($dbparts['path'], '/');
    $port = $dbparts['port'];
} else {
    $hostname = getenv('MYSQLHOST') ?: 'localhost';
    $username = getenv('MYSQLUSER') ?: 'root';
    $password = getenv('MYSQLPASSWORD') ?: '';
    $database = getenv('MYSQLDATABASE') ?: 'demo';
    $port = getenv('MYSQLPORT') ?: 3306;
}

define('DB_SERVER', $hostname);
define('DB_USERNAME', $username);
define('DB_PASSWORD', $password);
define('DB_NAME', $database);
define('DB_PORT', $port);

/* Attempt to connect to MySQL database */
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);

// Check connection
if($mysqli === false){
    error_log("Connection failed: " . $mysqli->connect_error);
    die("ERROR: Could not connect to the database. Please try again later.");
}
?>