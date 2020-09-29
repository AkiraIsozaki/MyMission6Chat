<?php
require 'utilities/db_connect.php';
require 'utilities/checkInput.php';


//------------dbからuserinfoを呼び出し--------------------
session_start();
$id = $_SESSION['id'];//データベースから呼び出すときに使う
try {
    $stmt = $pdo->prepare("SELECT * FROM db1 WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_STR);
    $stmt->execute();
    $userinfo = $stmt->fetchAll();
} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    // エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
    exit($e->getMessage()); 
}
$name = $userinfo[0]['name'];
//----------入力値の取得------------------------
$date = checkInput("date");
$sche = checkInput("sche");
$time = date("Y/m/d　H:i:s");
$delete = checkInput("delete");
$editnumber = checkInput("editnumber");
$editdate = checkInput("editdate");
$editsche = checkInput("editsche");

try {
    //-------------予定の登録------------------------
    if (!($date===null) && !($sche===null)) {
        $sql = $pdo->prepare("INSERT INTO db5 (username, date, schedule, created) VALUES (:name, :date, :schedule, :time)");
        $sql->bindParam(':name', $id, PDO::PARAM_INT);
        $sql->bindParam(':date', $date, PDO::PARAM_STR);
        $sql->bindParam(':schedule', $sche, PDO::PARAM_STR);
        $sql->bindParam(':time',$time , PDO::PARAM_STR);
        $sql->execute();
    }

    //----------------予定の削除---------------------
    if (!($delete === null)) {
        $stmt = $pdo->prepare("SELECT username FROM db5 WHERE id=:id");
        $stmt->bindParam(':id', $delete, PDO::PARAM_STR);
        $stmt->execute();
        $userinfo = $stmt->fetchAll();
        $userid = $userinfo[0]['username'];//削除希望したい投稿番号の投稿を書いた人のid
        if ($userid == $id) {             //自分のidと等しければ削除する
            $sql = 'delete from db5 where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $delete, PDO::PARAM_INT);
            $stmt->execute();
            
        }
        
    }
    //--------予定の変更-------------------
    if (!($editnumber === null) && !($editdate === null) && !($editsche === null)) {
        $stmt = $pdo->prepare("SELECT username FROM db5 WHERE id=:id");
        $stmt->bindParam(':id', $editnumber, PDO::PARAM_STR);
        $stmt->execute();
        $userinfo = $stmt->fetchAll();
        $userid = $userinfo[0]['username'];//編集したい投稿番号の投稿を書いた人のid
        if ($userid == $id) {             //自分のidと等しければ編集する
            $sql = 'update db5 set date=:date, schedule=:schedule, modified=:modified where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':date', $editdate, PDO::PARAM_STR);
            $stmt->bindParam(':schedule', $editsche, PDO::PARAM_STR);
            $stmt->bindParam(':modified', $time, PDO::PARAM_STR);
            $stmt->bindParam(':id', $editnumber, PDO::PARAM_INT);
            $stmt->execute();
        }
    }
} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    // エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
    exit($e->getMessage()); 
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?php echo h($name).'さんのスケジュール'; ?>スケジュール</title>
</head>
<body>
<ul>
    <li><a href="m12.php">チャット一覧</a></li>
    <li><a href="m7.php">ユーザページ</a></li>
    <li><a href="../m1.php">ログアウト</a></li>
</ul><hr>
<form method="POST">
    <input type="date" name="date">
    <input type="text" name="sche" placeholder="スケジュール">
    <input type="submit" value="登録する">
</form>
<form method="POST">
    <input type="number" name="delete" placeholder="削除したい番号">
    <input type="submit" value="削除">
</form>
<form method="POST">
    <input type="number" name="editnumber" placeholder="変更したい番号">
    <input type="date" name="editdate">
    <input type="text" name="editsche" placeholder="変更のスケジュール">
    <input type="submit" value="変更">
</form>
<hr>
</body>
</html>

<?php
try {
    $stmt = $pdo->prepare("SELECT * FROM db5 WHERE username=:id");
    $stmt->bindParam(':id', $id, PDO::PARAM_STR);
    $stmt->execute();
    $scheduleinfo = $stmt->fetchAll();
} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    // エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
    exit($e->getMessage()); 
}

//print_r($scheduleinfo);
foreach ($scheduleinfo as $row) {
    echo h($row['id']) . '．' . h($name) . '：' . h($row['date']) . 'の予定：' . h($row['schedule']) . '　登録日時：' . h($row['created']) . '<br>';
}