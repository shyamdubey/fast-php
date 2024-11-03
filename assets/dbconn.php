<?php
date_default_timezone_set('Asia/Kolkata');

require_once __DIR__."/DbConnection.php";
$dbConnection = new DbConnection;

$conn = $dbConnection->getConnection();
$now = date("Y-m-d H:i:s", time());

