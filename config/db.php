<?php
$host = 'localhost';
$db   ='cycle_store';
$user = 'root';
$pass = '';
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) { die('DB connection failed: ' . mysqli_connect_error()); }
session_start();
