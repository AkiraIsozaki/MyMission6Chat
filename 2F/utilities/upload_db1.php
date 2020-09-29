<?php
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
    //db10は用無し故，消す．
    require 'create_db10.php'; require 'kill_db10.php';
    $imagename = '何もない時のお写真.png';
    $picture = "utilities/pic/$imagename";
    $goodness = 0;
    $created = date("Y/m/d　H:i:s");
    $sql = $pdo -> prepare("INSERT INTO db1 (name, userid, email, password, picture, goodness, created) VALUES (:name, :userid, :email, :password, :picture, :goodness, :created)");
    $sql->bindParam(':name', $name, PDO::PARAM_STR);
    $sql->bindParam(':userid', $userid, PDO::PARAM_STR);
    $sql->bindParam(':email', $email, PDO::PARAM_STR);
    $sql->bindParam(':password', $pass_h, PDO::PARAM_STR);
    $sql->bindParam(':picture', $picture, PDO::PARAM_STR);
    $sql->bindParam(':goodness', $goodness, PDO::PARAM_INT);
    $sql->bindParam(':created', $created, PDO::PARAM_STR);
    $sql->execute();

} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    // エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
    exit( $e->getMessage() ); 
}


