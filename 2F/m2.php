<?php
require 'utilities/db_connect.php';//データベースに接続
require 'utilities/checkInput.php';//入力値チェック
$erro_check = 0;//最後まで0だと文句を言わない
//------------入力値取得-----------------
$idc = checkInput('idc');
$passc = checkInput('passc');

try {//dbに関して。
    if (!($idc === null) && !($passc === null)){//両方とも入力されているとき
        $sql = 'SELECT * FROM db1';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row) {
            if ($row['userid'] === $idc && password_verify($passc,$row['password'])){//idとパスワードが正しい
                $email = $row['email'];
                session_start();
                $_SESSION['id'] = $row['id'];//セッションに保持．ユーザ情報と紐づけ
                require_once 'utilities/send_test.php';
                $mail->addAddress($email); //受信者（送信先）を追加する
                $mail->Subject = MAIL_SUBJECT; // メールタイトル
                $mail->isHTML(true);    // HTMLフォーマットの場合はコチラを設定します
                $body = "ログインされたよー";
                $mail->Body = $body; // メール本文
                // メール送信の実行
                if (!$mail->send()) {//失敗時
                    echo '<script language=javascript>alert("メールが送られませんでした．")</script>';    
                    header("location: ../m1.php");
                } else {
                    echo '送信完了！';
                    header("location: m7.php");
                }
            } else {//idとパスワードの少なくとも一方が正しくないとき
                $erro_check += 1;
            }
        }
    }
    if (!($erro_check===0)) {
        echo '<script language=javascript>alert("入力値を確認してください")</script>';    
    }
} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    exit($e->getMessage()); 
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
</head>
<body>
<form method="POST">
    <h2><a href="../m1.php">トップページに戻る</a></h2><hr>
    <input type="text" name="idc" placeholder="id">
    <input type="password" name="passc" placeholder="パスワード">
    <input type="submit" value="送信">
    <h2>アカウントを持っていない方は<a href="m3.php">こちら</a>からつくってどうぞ</h2>
</form>
</body>
</html>