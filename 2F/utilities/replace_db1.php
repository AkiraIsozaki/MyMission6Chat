<?php
$id = $_SESSION['id'];
$name = $_SESSION['name'];
$userid = $_SESSION['userid'];
$email = $_SESSION['email'];
require 'db_connect.php';
try {
    //db10に一時記憶されているハッシュ値を手元に保持する．
    $sql = 'SELECT * FROM db10';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        $pass_h = $row['password_d'];
    }
} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    // エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
    exit( $e->getMessage() ); 
}



//db10は用無し故，消す．
require 'create_db10.php'; require 'kill_db10.php';

try {
    $modified = date("Y/m/d　H:i:s");
    $sql = 'UPDATE db1 SET name=:name, userid=:userid, email=:email, password=:password, modified=:time where id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':password', $pass_h, PDO::PARAM_STR);
    $stmt->bindParam(':time', $modified, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_STR);
    $stmt->execute();
} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    // エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
    exit( $e->getMessage() ); 
}

