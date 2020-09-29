<?php
require 'utilities/checkInput.php';
require 'utilities/db_connect.php';

session_start();
$id = $_SESSION['id'];
//------------chatの取得--------------------------
$chatnumber = $_SESSION['chatnumber'];//チャットの番号
try {
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

//-------------------------------取得した文字列を取得し，挿入できるように編集する．-------------------------------------------------

$comment = checkInput("comment");
$editline = checkInput("editline");
$reply = checkInput("reply");
$replycomment = checkInput("replycomment");
$time = date("Y/m/d　H:i:s");
$iine = checkInput("iine");
$del_post = checkInput("delete");
$edit_post = checkInput("edit");
$follow = checkInput('follow');
$goodcount = 0;
try {
    //---------------------------------新しい行を挿入する．-------------------------------------------------------------------
    if (!($comment === null) && $editline === null) {
        $sql = $pdo->prepare("insert into $chatname  (name, comment, time, goodness) values (:name, :comment, :time, :goodness)");
        $sql->bindParam(':name', $id, PDO::PARAM_STR);
        $sql->bindParam(':comment',$comment, PDO::PARAM_STR);
        $sql->bindParam(':time',$time, PDO::PARAM_STR);
        $sql->bindParam(':goodness',$goodcount, PDO::PARAM_INT);
        $sql->execute();//送信    
    }

    //------------------------------------削除-------------------------------------------
    if (!($del_post === null)) {
        $sql = "select * from $chatname where id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $del_post,PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $writer = $result[0]['name'];//その行をかいたい人のid
        if ($writer == $id){//等しければ消。。
            //chatを消す
            $sql = "delete from $chatname where id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $del_post, PDO::PARAM_INT);
            $stmt->execute();
            //replyも消す
            $sql = "delete from $repdb where id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $del_post, PDO::PARAM_INT);
            $stmt->execute();
        }
        
    }
    //------------------------------------編集したい行を返す.-------------------------------------------
    if (!($edit_post === null)) {
        //編集したい番号の投稿を入力フォームに返す
        $sql = "select * from $chatname where id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id',$edit_post,PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $writer = $result[0]['name'];
        if ($writer == $id) {
            $return_id = $result[0]['id'];
            $return_comment = $result[0]['comment'];
        }
    }
    //------------------------------------編集する----------------------------------------------
    if (!($comment === null) && !($editline === null)) {
        $sql = "update $chatname set comment=:comment, time=:time where id=:id";
        $stmt = $pdo->prepare($sql);
        #name, comment time id　を順にバインドする.
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':time', $time, PDO::PARAM_STR);
        $stmt->bindParam(':id', $editline, PDO::PARAM_INT);
        $stmt->execute();
    }
    //-----------replyする--------------------------
    if (!($reply === null) && !($replycomment === null)) {
        $sql = "INSERT INTO $repdb (fromid, toid, repcomment, time) VALUES (:fromid, :toid, :repcomment, :time)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':fromid', $id, PDO::PARAM_STR);
        $stmt->bindParam(':toid', $reply, PDO::PARAM_STR);
        $stmt->bindParam(':repcomment', $replycomment, PDO::PARAM_STR);
        $stmt->bindParam(':time', $time, PDO::PARAM_STR);
        $stmt->execute();
    }
    //------------いいね---------------------------------------
    if (!($iine === null)) {
        /* 現状のイイネの数を取得して，+1して値を返す。【chatname】 */
        $sql = "select * from $chatname where id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id',$iine,PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        //var_dump($result);
        $goodtemp = $result[0]['goodness'];//いまいくつgoodnessがたまっているか
        $goodto_id = $result[0]['name']; //いいねしたい人のid(db1と連関)

        $goodtemp += 1;
        $sql = $pdo->prepare("update $chatname set  goodness=:goodness where id=:id");
        $sql->bindParam(':goodness',$goodtemp, PDO::PARAM_INT);
        $sql->bindParam(':id',$iine, PDO::PARAM_INT);
        $sql->execute();//送信 

        /* 現状のイイネの数を取得して，+1して値を返す。【db1】 */
        $sql = "select * from db1 where id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id',$goodto_id,PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        //var_dump($result);
        $goodtemp = $result[0]['goodness'];
        $goodtemp += 1;
        $sql = $pdo->prepare("update db1 set goodness=:goodness where id=:id");
        $sql->bindParam(':goodness',$goodtemp, PDO::PARAM_INT);
        $sql->bindParam(':id',$goodto_id, PDO::PARAM_INT);
        $sql->execute();//送信 
    }
    //---------------------フォロワーうんぬん-------------------------------------
    if (!($follow === null)) {
        $sql = "select * from $chatname where id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id',$follow,PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        //var_dump($result);
        $follow_id = $result[0]['name']; //followしたい人のid(db1と連関)
        //重複がないかを調べる。userid と followersのsetがともに，等しいものが一つ以上あった場合はNG(既にフォロー完了)
        $fcheck = 0;
        $sql = 'SELECT * FROM db4'; $stmt = $pdo->query($sql); $results = $stmt->fetchAll();
        foreach ($results as $row){
            if (($row['userid'] == $id && $row['followers'] == $follow_id) || $id == $follow_id) {//両方とも等しい時や，自分自身をフォローしようとしているとき
                $fcheck += 1;
            }
        }
        if ($fcheck === 0){//重複は存在しないとき登録
            $sql = $pdo->prepare("INSERT INTO db4 (userid, followers, created) VALUES (:userid, :followers, :time)");
            $sql->bindParam(':userid', $id, PDO::PARAM_STR);//自分のid
            $sql->bindParam(':followers', $follow_id, PDO::PARAM_STR);//向こうのid
            $sql->bindParam(':time', $time, PDO::PARAM_STR);
            $sql->execute();
            echo '<script language=javascript>alert("登録成功")</script>';
        } else {
            echo '<script language=javascript>alert("既にフォローしています")</script>';
        }    
    }
} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    // エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
    exit($e->getMessage()); 
}
?>

<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?=h($chatname);?></title>
    <script type="text/javascript" charset="UTF-8">
        function disp(){
            if(!window.confirm('本当にいいんですね？')){
                window.alert('キャンセルされました'); 
                return false;
            }
        }
    </script>
</head>
<body>
<ul>
    <li><a href="m12.php">チャット一覧</a></li>
    <li><a href="m7.php">ユーザページ</a></li>
    <li><a href="../m1.php">ログアウト</a></li>
</ul><hr>
    <form method="POST">
        <textarea name="comment" cols="50" rows="5" placeholder="書いてね"><?php if(!empty($return_comment)){echo h($return_comment);}?></textarea>
        <input type="hidden" name="editline" 
                value="<?php if(!empty($return_id)){echo h($return_id);}?>">
            <!-- 編集用に新しい値が返された時は，パスワードを操作しないように，入力ボックスは隠す．-->
        <input type="submit" value="送信">
    </form>

    <form method="POST">
        <input type="number" name="delete" placeholder="削除したい投稿番号">
        <input type="submit" value="削除" onclick="disp();">
    </form>
    <form method="POST">
        <input type="number" name="edit" placeholder="編集したい投稿番号">
        <input type="submit" value="編集">
    </form>
    <form method="POST">
        <input type="number" name="reply" placeholder="リプライしたい投稿番号">
        <input type="text" name="replycomment" placeholder="クソリプ書いてね♪">
        <input type="submit" value="送信">
    </form>
    <form method="POST">
        <input type="number" name="iine" placeholder="いいねしたい投稿番号">
        <input type="submit" value="送信">
    </form>
    <form method="POST">
        <input type="number" name="follow" placeholder="フォロワーにしたい人" >
        <input type="submit" value="送信"><hr>
    </form>
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
    }
}
} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    // エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
    exit($e->getMessage()); 
}
?>
<hr></p>