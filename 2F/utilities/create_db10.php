<?php
require_once 'db_connect.php';
//----db作成------------------------------------
try {
    $sql = "CREATE TABLE IF NOT EXISTS db10"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "userid TEXT,"
        . "password_d TEXT,"
        . "onetime_pass TEXT"
        .");";
    $stmt = $pdo->query($sql);
} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    // エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
    exit( $e->getMessage() ); 
}

