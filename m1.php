<?php
//require 'utilities/db_connect.php';
require '2F/utilities/create_dbs.php';//db作成
require '2F/utilities/kill_sessions.php';//残留分セッションを死なせる
require '2F/utilities/create_db10.php';//db10を作って
require '2F/utilities/kill_db10.php';//消す
require '2F/utilities/checkInput.php';//入力値

//---------------入力に応じて----------------------------------------
if (isset($_POST['login'])) {//1
    header("location: 2F/m2.php");
}
if (isset($_POST['nologin'])) {//2
    header("location: 2F/m14.php");
}
if (isset($_POST['noenter'])) {//3
    echo '<script language=javascript>alert("いくじなし")</script>';    
}
if (isset($_POST['createaccount'])) {//4
    header("location: 2F/m3.php");
}
?>
<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
</head>
<body>
    <h1>ようこそやで</h1>
    <!--1--><form method="POST"><input type="submit" value="ログイン" name="login"> </form>
    <!--2--><form method="POST"><input type="submit" value="ログインせずにみる" name="nologin"></form>
    <!--3--><form method="POST"><input type="submit" value= "入らない" name="noenter"></form>
    <!--4--><form method="POST"><input type="submit" value= "アカウント作成" name="createaccount"></form>
</body>
</html>