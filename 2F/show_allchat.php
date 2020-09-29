<?php
require 'utilities/db_connect.php';
//require 'utilities/checkInput.php';
//session_start();
$id = $_SESSION['id'];//データベースから呼び出すときに使う
try {
    $stmt = $pdo->prepare("SELECT * FROM db2");
    $stmt->execute();
    $chatnames = $stmt->fetchAll();
    foreach ($chatnames as $row) {
        $chat_author_id = $row['chat_author'];
        //db1で該当のユーザを照合。
        $stmt = $pdo->prepare("SELECT * FROM db1 WHERE id = :id");
        $stmt->bindParam(':id', $chat_author_id, PDO::PARAM_STR);
        $stmt->execute();
        $userinfo = $stmt->fetchAll();
        $name = $userinfo[0]['name'];
        echo h($row['id']) . '.' . h($row['chatname']) . '　作成者:' . h($name) . '<hr>';
    }

} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    // エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
    exit($e->getMessage()); 
}

