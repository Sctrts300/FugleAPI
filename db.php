<?php
$host =     "localhost";
$db =   "webshop";
$user = "root";
$password = "root";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOExeption $error) {
    die("Connection failed: " . $error->getMessage());

}
