<?php
$name = $_SESSION['name'];
$userid = $_SESSION['userid'];
$email = $_SESSION['email'];
require_once 'db_connect.php';
try {
    $sql = 'SELECT * FROM db10';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        $onetime_pass = $row['onetime_pass'];
    }
} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    // エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
    exit( $e->getMessage() ); 
}

require_once 'send_test.php';
$mail->addAddress($email); //受信者（送信先）を追加する
//$mail->addReplyTo('xxxxxxxxxx@xxxxxxxxxx','返信先');
//$mail->addCC('xxxxxxxxxx@xxxxxxxxxx'); // CCで追加
//$mail->addBcc('xxxxxxxxxx@xxxxxxxxxx'); // BCCで追加
$mail->Subject = MAIL_SUBJECT; // メールタイトル
$mail->isHTML(true);    // HTMLフォーマットの場合はコチラを設定します
    
$body = "$name さんがログインしようとしています．パスコードは以下の通りです．<br> $onetime_pass";
$mail->Body = $body; // メール本文