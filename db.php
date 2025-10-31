<?php
$host =     "localhost";
$db =   "fugle";
$user = "root";
$password = "root";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $error) {
    die("Connection failed: " . $error->getMessage());

};
