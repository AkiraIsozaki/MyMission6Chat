<?php
$count_wrong = 0;
session_start();
$name = $_SESSION['name'];
$userid = $_SESSION['userid'];
$email = $_SESSION['email'];
require 'utilities/db_connect.php'; require 'utilities/create_db10.php';
require 'utilities/checkInput.php';require 'utilities/m5_mail.php';
$rand_c = checkInput('rand_c');

if ($rand_c === null) {
    // メール送信の実行
    if (!$mail->send()) {
        echo 'メッセージは送られませんでした！';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
        //header("location: m1.php");
    } else {
        echo '送信完了！';    
    }
} else {
    if ($onetime_pass === $rand_c) {
        require 'utilities/upload_db1.php';
        header("location: m6.php");
    } else {
        echo '<script language=javascript>alert("正しい値を入力してください．")</script>';
        $count_wrong += 1;
    }
    if ($count_wrong > 3) {
        header("location: canceld.php");
    } 
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>アカウント新規作成3</title>
</head>
<body>
    <form method="POST">
        <h4><a href="../m1.php">戻る</a></h4><hr>
        <h4>パスコード認証します</h4>
        <input type="number" name="rand_c" placeholder="メールの数字">
        <input type="submit" value="送信">
    </form>
</body>
    
</html>