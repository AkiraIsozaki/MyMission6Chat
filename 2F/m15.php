<?php
require 'utilities/checkInput.php';
require 'utilities/db_connect.php';
session_start();
//------------chatの取得--------------------------
try {
    $chatnumber = $_SESSION['chatnumber'];//チャットの番号
    $stmt = $pdo->prepare("SELECT * FROM db2 WHERE id = :id");
    $stmt->bindParam(':id', $chatnumber, PDO::PARAM_STR);
    $stmt->execute();
    $chatinfo = $stmt->fetchAll();
    //print_r($chatinfo);
    $chatname = $chatinfo[0]['chatname'];
    $chat_author = $chatinfo[0]['chat_author'];
    //----リプライのdb作成
    $repdb = $chatname . "rep";
    $sql = "create table if not exists $repdb"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "fromid TEXT,"
        . "toid TEXT,"
        . "repcomment TEXT,"
        . "time TEXT"
        .");";
    $stmt = $pdo->query($sql);
} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    // エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
    exit($e->getMessage()); 
}

?>

<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?php echo h($chatname);?></title>
</head>
<body>
<ul>
    <li><a href="m14.php">チャット一覧</a></li>
    <li><a href="../m1.php">トップページへ</a></li>
</ul><hr>
</body>
</html>
<?php
// ---------------------------------------画面表示--------------------
try {
    $stmt = $pdo->prepare("SELECT * FROM $chatname");
    $stmt->execute();
    $talks = $stmt->fetchAll();
    //print_r($talks);
    //replyが詰まった配列を取り出す。
    $stmt = $pdo->prepare("SELECT * FROM $repdb");
    $stmt->execute();
    $reps = $stmt->fetchAll();
    //print_r($reps);
    foreach ($talks as $row) {
        $repary = array();
        $name_id = $row['name'];
        //db1で該当のユーザを照合。
        $stmt = $pdo->prepare("SELECT * FROM db1 WHERE id = :id");
        $stmt->bindParam(':id', $name_id, PDO::PARAM_STR);
        $stmt->execute();
        $userinfo = $stmt->fetchAll();
        $name = $userinfo[0]['name'];
        $pic = $userinfo[0]['picture'];
        $recomment = null;
        $repc = 0;
        
        foreach ($reps as $rip) {
            if ($rip['toid'] == $row['id']){
                //repした人のid取得。db1と照合する
                $replyerid = $rip['fromid'];
                $stmt = $pdo->prepare("SELECT * FROM db1 WHERE id = :id");
                $stmt->bindParam(':id', $replyerid, PDO::PARAM_STR);
                $stmt->execute();
                $userinfo = $stmt->fetchAll();
                $replyername = $userinfo[0]['name'];
                $repc += 1;
                $reppair = array();
                $reppair +=  array('name'=> $replyername);
                $reppair +=  array('comment'=> $rip['repcomment']);
                $reppair +=  array('time'=> $rip['time']);
                $repary[] = $reppair;
            }
        }
?>
            <p><?=h($row['id'])?>　<img src="<?=h($pic)?>" alt="プロフィール写真" height="20">　<?php echo h($name)?>　<br><?php echo h($row['comment'])?>　<br><?php echo h($row['time'])?>
                　イイネの数[<?Php echo h($row['goodness']);?>]<br>【リプライ一覧】<br><?php 
        if (!($repc === 0)) {
            foreach($repary as $rep){
                echo '　=>' . h($rep['name']) . '　' . h($rep['comment']) . '　' . h($rep['time']) . '<br>';
            }
        }?>
<hr></p>
<?php
    }
} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    // エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
    exit($e->getMessage()); 

}

?>