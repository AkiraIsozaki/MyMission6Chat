<?php
require 'utilities/checkInput.php';
require 'utilities/db_connect.php';

session_start();
$keywords = checkInput("search");
//print_r($keywords);
$scheck = 0;
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
if (isset($_POST['back'])) {
    header("location: ../m1.php");
}
$chatnumber = checkInput("chatnumber");
if (!($chatnumber === null)) {
    $_SESSION['chatnumber'] = $chatnumber;
    header("location: m15.php");
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
                echo $row['id'] . '.　' . $row['chatname'] . '<br>';
            }
        }
    ?>
    <form method="POST">
        <input type="number" name="chatnumber" placeholder="トークに移動する">
        <input type="submit" value="移動"><br>
    </form>
    <form method="POST"><input type="submit" name="back" value="トップページに戻る"></form>
    <hr>
</body>
</html> 
<?php
echo '<hr>';
echo '<hr>';
try {
    $stmt = $pdo->prepare("SELECT * FROM db2");
    $stmt->execute();
    $chatnames = $stmt->fetchAll();
    foreach ($chatnames as $row) {
        $chat_author_id = $row['chat_author'];
        //db1で該当のユーザを照合。
        $stmt = $pdo->prepare("SELECT * FROM db1 WHERE id = :id");
        $stmt->bindParam(':id', $chat_author_id, PDO::PARAM_STR);
        $stmt->execute();
        $userinfo = $stmt->fetchAll();
        $name = $userinfo[0]['name'];
        echo h($row['id']) . '.' . h($row['chatname']) . '　作成者:' . h($name) . '<hr>';
    }
} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    // エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
    exit( $e->getMessage() ); 
}
