<?php

require 'db_connect.php';

//----db作成------------------------------------
try {
    $sql = "CREATE TABLE IF NOT EXISTS db1"
        . " ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "userid TEXT,"
        . "email TEXT,"
        . "password TEXT,"
        . "picture TEXT,"
        . "goodness TEXT,"
        . "created TEXT,"
        . "modified TEXT"
        . ");";
    $stmt = $pdo->query($sql);

    $sql = "CREATE TABLE IF NOT EXISTS db2"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "chatname char(32),"
        . "chat_author TEXT,"
        . "created TEXT,"
        . "modified TEXT"
        .");";
    $stmt = $pdo->query($sql);
        
    $sql = "CREATE TABLE IF NOT EXISTS db3"
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
        
    $sql = "CREATE TABLE IF NOT EXISTS db4"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "userid INT,"
        . "followers INT,"
        . "created TEXT,"
        . "modified TEXT"
        .");";
    $stmt = $pdo->query($sql);

    $sql = "CREATE TABLE IF NOT EXISTS db5"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "username INT,"
        . "date TEXT,"
        . "schedule TEXT,"
        . "created TEXT,"
        . "modified TEXT"
        .");";
    $stmt = $pdo->query($sql);

        
        /*
        $sql ='SHOW CREATE TABLE db1';
        $result = $pdo->query($sql);
        foreach ($result as $row){
            echo $row[1];
        }
        echo "<hr>";
        $sql ='SHOW CREATE TABLE db2';
        $result = $pdo->query($sql);
        foreach ($result as $row){
            echo $row[1];
        }
        echo "<hr>";
        $sql ='SHOW CREATE TABLE db3';
        $result = $pdo->query($sql);
        foreach ($result as $row){
            echo $row[1];
        }
        echo "<hr>";
        $sql ='SHOW CREATE TABLE db4';
        $result = $pdo->query($sql);
        foreach ($result as $row){
            echo $row[1];
        }
        echo "<hr>";
        */
} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    // エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
    exit($e->getMessage()); 
}

