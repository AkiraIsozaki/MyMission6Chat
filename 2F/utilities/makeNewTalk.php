<?php
require 'db_connect.php';
require 'checkInput.php';
$tbname = checkInput('tbname');
try {
    if (!($tbname===null)) {
        $sql = "CREATE TABLE IF NOT EXISTS $tbname"
            ." ("
            . "id INT AUTO_INCREMENT PRIMARY KEY,"
            . "message TEXT,"
            . "memberid INT,"
            . "replyid INT,"
            . "goodness INT,"
            . "created TEXT,"
            . "modified TEXT"
            .");";
        $stmt = $pdo->query($sql);
    }
} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    // エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
    exit( $e->getMessage() ); 
}


