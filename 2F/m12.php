<?php
require 'utilities/checkInput.php';
require 'utilities/db_connect.php';
//-----------ログイン時のユーザ情報------------------------
session_start();
$id = $_SESSION['id'];//データベースから呼び出すときに使う
try {
    $stmt = $pdo->prepare("SELECT * FROM db1 WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_STR);
    $stmt->execute();
    $userinfo = $stmt->fetchAll();
    //print_r($userinfo);
    $name = $userinfo[0]['name'];
    $time = date("Y/m/d　H:i:s");

    //db2に登録(チャット名，編者，作成日)
    $chatname = checkInput('chatname');

    if (!($chatname === null) && is_string($chatname)) {//数字はダメ
        $chatname = strval($chatname);
        $sql = "INSERT INTO db2 (chatname, chat_author, created) VALUES (:chatname, :chat_author, :created)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':chatname', $chatname, PDO::PARAM_STR);
        $stmt->bindParam(':chat_author', $id, PDO::PARAM_STR);
        $stmt->bindParam(':created', $time, PDO::PARAM_STR);
        $stmt->execute();
        //作っておく
        $sql = "create table if not exists $chatname"
            ." ("
            . "id INT AUTO_INCREMENT PRIMARY KEY,"
            . "name char(32),"
            . "comment TEXT,"
            . "goodness TEXT,"
            . "time TEXT"
            .");";
        $stmt = $pdo->query($sql);
    }

    //chatの番号を取得し，m13に移行する。
    $chatnumber = checkInput('chatnumber');
    if (!($chatnumber === null)) {
        $_SESSION['chatnumber'] = $chatnumber;
        header("location: m13.php");
    }

    //chat番号を取得し，作成者のみそのchatの削除を許可する。
    $delete = checkInput('delete');
    if (!($delete === null)) {
        $stmt = $pdo->prepare("SELECT * FROM db2 WHERE id = :id");
        $stmt->bindParam(':id', $delete, PDO::PARAM_STR);
        $stmt->execute();
        $chatinfo = $stmt->fetchAll();
        //print_r($chatinfo);
        $chat_author = $chatinfo[0]['chat_author'];
        $dchatname = $chatinfo[0]['chatname'];
        if ($id === $chat_author) {
            //一覧から消す
            $sql = "delete from db2 where id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $delete, PDO::PARAM_INT);
            $stmt->execute();
            //dbそれ自体を消す
            $dchatname = strval($dchatname);
            $sql = "DROP TABLE IF EXISTS $dchatname";
            $stmt = $pdo->query($sql);
        }
    }
} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    // エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
    exit($e->getMessage()); 
}

if (isset($_POST['logout'])) {
    header("location: ../m1.php");
}
if (isset($_POST['back'])) {
    header("location: m7.php");
}

$keywords = checkInput("search");
//print_r($keywords);
$scheck = 0;
try {
    if (!($keywords === null)) {
        $scheck += 1;
        $maxKeywords = 6; // 適当な分割数の上限を設定（無制限にしたい場合は -1）
        $keywords = preg_split('/(?:\p{Z}|\p{Cc})++/u', $keywords, $maxKeywords, PREG_SPLIT_NO_EMPTY);
        //print_r($keywords);
        // キーワードが1つ以上のときだけ実行 
        foreach ($keywords as $keyword) {
            // プレースホルダのLIKE部分を用意
            $holders[] = "((chatname LIKE ? ESCAPE '!') OR (chat_author LIKE ? ESCAPE '!'))";
            // LIKE検索のために「%キーワード%」の形式にする
            $values[] = $values[] = '%' . preg_replace('/(?=[!_%])/', '!', $keyword) . '%';
        }
        //print_r($values);
        // AND条件で結合する
        $sql = 'SELECT * FROM db2 WHERE (' . implode(' AND ', $holders) . ')';
        // 実行
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        $results = $stmt->fetchAll();
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
    <title>チャット一覧</title>
</head>
<body>
    <h1>スレッド一覧</h1>
    <form method="POST">
        <input type="text" name="search" placeholder="キーワードを入力してね">
        <input type="submit" value="検索"><br>
    </form>
    <?php /* 検索がかけられているときだけここを通る*/  
        if (!($scheck === 0)) {
            echo '【検索結果一覧】<br>';
            foreach($results as $row) {
                echo h($row['id']) . '.　' . h($row['chatname']) . '<br>';
            }
        }
    ?>
    <form method="POST">
        <input type="number" name="chatnumber" placeholder="トークに移動する">
        <input type="submit" value="移動"><br>
    </form>
    <h2>chatは自分が作成したものだけ消せます。</h2>
    <form method="POST">
        <input type="number" name="delete" placeholder="削除番号">
        <input type="submit" value="削除"><br>
    </form>
    <form method="POST">【注意】チャット名は数字で始めることはできません<br>
        <input type="text" name="chatname" placeholder="スレッド名">
        <input type="submit" value="作成する">
    </form>

    <form method="POST"><input type="submit" name="logout" value="ログアウト"></form>
    <form method="POST"><input type="submit" name="back" value="ユーザページに戻る"></form>
    <hr>
</body>
</html> 
<?php
echo '<hr>';
echo '<hr>';

require 'show_allchat.php';