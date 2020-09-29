<?php
require_once 'db_connect.php';
try {
    $sql = 'DROP TABLE db10';
    $stmt = $pdo->query($sql);
} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    // エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
    exit( $e->getMessage() ); 
}

