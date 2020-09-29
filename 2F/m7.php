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
    //print_r($userinfo);
    $name = $userinfo[0]['name'];
    $userid = $userinfo[0]['userid'];
    $email = $userinfo[0]['email'];
    $picture = $userinfo[0]['picture'];
    $goodness = $userinfo[0]['goodness'];
} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    // エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
    exit($e->getMessage()); 
}
//ユーザプロフィール変更
if (isset($_POST['changeinfo'])) {
    header("location: m8.php");
}

//-----------プロフィール写真の変更------------------------------
$message = null;
if (isset($_POST['upload'])) {//送信ボタンが押された場合
    $image = uniqid(mt_rand(), true);//ファイル名をユニーク化
    $image .= '.' . substr(strrchr($_FILES['image']['name'], '.'), 1);//アップロードされたファイルの拡張子を取得
    $file = "utilities/pic/$image";//保存するときの名前
    //db1のpictureを$fileで上書きする。
    try {
        $sql = 'UPDATE db1 SET picture=:picture, modified=:time WHERE id=:id';
        $time = date("Y/m/d H:i:s");
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':picture', $file, PDO::PARAM_STR);
        $stmt->bindParam(':time', $time, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        if (!empty($_FILES['image']['name'])) {//ファイルが選択されていれば$imageにファイル名を代入
            move_uploaded_file($_FILES['image']['tmp_name'], 'utilities/pic/' . $image);//imagesディレクトリにファイル保存
            if (exif_imagetype($file)) {//画像ファイルかのチェック
                $message = '画像をアップロードしました';
                $stmt->execute();
            } else {
                $message = '画像ファイルではありません';
            }
        }
    } catch (PDOException $e) {
        header('Content-Type: text/plain; charset=UTF-8', true, 500);
        // エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
        exit($e->getMessage()); 
    }
}
    
    
if (isset($_POST['back'])) {
    echo '<script language=javascript>alert("残念")</script>';
}
if (isset($_POST['logout'])) {
    require 'utilities/kill_sessions.php';
    header("location: ../m1.php");
}
if (isset($_POST['delete_account'])) {
    header("location: m7_delete.php");
}

if (isset($_POST['gochat'])) {
    header("location: m12.php");
}
if (isset($_POST['myschedule'])){
    header("location: m16.php");
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザページ</title>
</head>
<body>
    <h1>ようこそ</h1>
    <ul>
        <li>お名前：<?=h($name)?></li>
        <li>id：<?=h($userid)?></li>
        <li>メールアドレス：<?=h($email)?></li>
        <li><img src="<?=h($picture)?>" alt="プロフィール写真" height="50"></li>
        <li>イイネされた回数:<?=h($goodness)?></li>
    </ul>
    <form method="POST"><input type="submit" name="gochat" value="チャットに移動"></form>
    <form method="POST">
        <input type="submit" name="changeinfo" value="プロフィール変更">
    </form>
    <form method="POST"><input type="submit" name="logout" value="ログアウト"></form>
    <form method="POST"><input type="submit" name="delete_account" value="アカウントを削除する"></form>
    <form method="POST"><input type="submit" name="myschedule" value="スケジュールだよーん"></form>
    <h1>プロフィール写真の変更</h1>
    <!--送信ボタンが押された場合-->
<?php if (isset($_POST['upload'])): ?>
    <p><?= $message?></p>
    <?php header("Location: " . $_SERVER['PHP_SELF']); ?>

    <p>アップロード画像</p>
        <input type="file" name="image">
        <button><input type="submit" name="upload" value="送信"></button>
    </form>
    <form method="POST">
        <input type="submit" name="back" value="今はしない">
    </form>
<?php else: ?>
    <form method="post" enctype="multipart/form-data">
        <p>アップロード画像</p>
        <input type="file" name="image">
        <button><input type="submit" name="upload" value="送信"></button>
    </form>
    <form method="POST">
        <input type="submit" name="back" value="今はしない">
    </form>
<?php endif;?>
</body>
</html>
<h4>あなたがフォロー中のユーザ<br></h4>

<?php 
try {
    $stmt = $pdo->prepare("SELECT * FROM db4 WHERE userid = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_STR);
    $stmt->execute();
    $followerinfo = $stmt->fetchAll();
} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    // エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
    exit($e->getMessage()); 
}
foreach($followerinfo as $row) {
    $fid = $row['followers'];//followerのid
    //db1と照合
    try {
        $stmt = $pdo->prepare("SELECT * FROM db1 WHERE id = :id");
        $stmt->bindParam(':id', $fid, PDO::PARAM_STR);
        $stmt->execute();
        $userinfo = $stmt->fetchAll();
    } catch (PDOException $e) {
        header('Content-Type: text/plain; charset=UTF-8', true, 500);
        // エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
        exit($e->getMessage()); 
    }
    $fname = $userinfo[0]['name'];
    echo h($fname) . '<br>';
}
?>
<h4>あなたのフォロワー</h4>
<?php
try {
    $stmt = $pdo->prepare("SELECT * FROM db4 WHERE followers = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_STR);
    $stmt->execute();
    $followerinfo = $stmt->fetchAll();
    foreach($followerinfo as $row) {
        $uid = $row['userid'];//followerのid
        //db1と照合
        $stmt = $pdo->prepare("SELECT * FROM db1 WHERE id = :id");
        $stmt->bindParam(':id', $uid, PDO::PARAM_STR);
        $stmt->execute();
        $userinfo = $stmt->fetchAll();
        $fname = $userinfo[0]['name'];
        echo $fname . '<br>';
    }
} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    // エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
    exit($e->getMessage()); 
}

?>