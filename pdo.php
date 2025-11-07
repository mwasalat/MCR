<?php

$dsn = "mysql:host=10.2.8.251:22;dbname=frc_new";
$user = "root";
$passwd = "Test@1234";
try {
$pdo = new PDO($dsn, $user, $passwd);

$stm = $pdo->query("SELECT VERSION()");

$version = $stm->fetch();

echo $version[0] . PHP_EOL;
} catch(Exception $e) {
            echo $e->getMessage();
        }