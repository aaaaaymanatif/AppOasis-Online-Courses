<?php

// Check if a specific constant or variable is defined
if (!defined('AppOasis_APP_CONFIG')) {
    // If the constant or variable is not defined, exit with an error message or redirect
    exit('Access denied');
}

// Rest of the configuration file content

// Database configuration
$dbHost = 'localhost';
$dbName = 'your_database_name';
$dbUser = 'your_username';
$dbPassword = 'your_password';

// Encryption key
$encryptionKey = '@AppOasis2023_p#4Ry*WK9nq2!vT';

//Database Initialization
$servername = "localhost";
$username = "root";
$password = "";
$database = "appoasis";

// Return the configuration array
return [
    'db_host' => $dbHost,
    'db_name' => $dbName,
    'db_user' => $dbUser,
    'db_password' => $dbPassword,
    'encryption_key' => $encryptionKey,
    'servername' => $servername,
    'username' => $username,
    'password' => $password,
    'database' => $database
];

?>