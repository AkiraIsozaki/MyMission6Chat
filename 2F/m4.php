<?php
require 'utilities/db_connect.php';
require 'utilities/checkInput.php';
session_start();
$name = $_SESSION['name'];
$userid = $_SESSION['userid'];
$email = $_SESSION['email'];
//---------進んでヨシ--------------
if (isset($_POST['go'])) {
    header("location: m5.php");
}
//---------やっぱり訂正する------------
if (isset($_POST['back'])) {
    header("location: m2re.php");
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>アカウント新規作成2</title>
</head>
<body>
    <h4><a href="../m1.php">戻る</a></h4><hr>
    <h3>アカウント作成手順<br>1情報入力<br>2入力情報の確認←イマココ<br>3パスコード認証<br>4登録完了</h3>
    <h1>登録内容をご確認ください</h1>
    <h3><ul>
        <li>ニックネーム:<?=h($name)?></li>
        <li>ユーザid:<?=h($userid)?></li>
        <li>メールアドレス:<?=h($email)?></li>
    </ul> 
    </h3>
    <h2>※上記でよろしければ，送信するを押してください．<br></h2>
    <form method="POST">
        <input type="submit" value="送信する" name="go">
        <input type="submit" value="訂正する"name="back">
    </form>    
</body>
    
</html>