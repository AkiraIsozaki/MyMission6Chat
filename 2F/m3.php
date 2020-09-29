<?php
require 'utilities/create_dbs.php';
require 'utilities/checkInput.php';

//--------------------------------取得した文字列を取得し，挿入できるように編集する．-------------------------------------------------
$name = checkInput("name");
$userid = checkInput("userid");
$pass_b1 = checkInput("pass1");//入力欄1
$pass_b2 = checkInput("pass2");//入力欄2
$email = checkInput("email");
$password = null;
//この中を最後まで走って，入力値が良くないときは，passwordがnullのままになる
if (!($pass_b1===null) && !($pass_b2===null)){
    if ($pass_b1 === $pass_b2) {
        if (mb_strlen($pass_b1) < 8) {
            echo '<script language=javascript>alert("パスワードは8文字以上としてください")</script>';
        } else {
            $password = password_hash($pass_b1, PASSWORD_DEFAULT);
        }    
    } else {
        echo '<script language=javascript>alert("パスワードが一致していません")</script>';
    }
}

try {//以降の処理は，全ての入力がnullでない時のみ実行
    if (!($name===null) && !($userid===null) && !($password===null) && !($email===null)) {
        //---------------入力されたデータが既存のデータベース内で重複していないか調べる-----------------------------------------
        $sql = 'SELECT * FROM db1';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();//db1全取り出しin配列
        $check = 0; //最後まで0だったら全て重複無し
        foreach ($results as $row) {
            if ($row['userid'] === $userid) {
                $check += 1;
            }
            if ($roppw['email'] === $email) {
                $check += 1;
            }
            if (password_verify($pass_b1, $row['password'])) {
                $check += 1;
            }
        }
        //------------checkが0のときのみ，sessionを保持．この段階で，db10にid,pass,randだけ送っておく．----------------
        if ($check === 0) {
            $random = rand(1000000,9999999);
            require 'utilities/create_db10.php';
            $sql = $pdo->prepare("INSERT INTO db10 (userid, password_d, onetime_pass) VALUES (:userid, :password_d, :onetime_pass)");
            $sql->bindParam(':userid', $userid, PDO::PARAM_STR);
            $sql->bindParam(':password_d', $password, PDO::PARAM_STR);
            $sql->bindParam(':onetime_pass', $random, PDO::PARAM_STR);
            $sql->execute();
            session_start();
            $_SESSION['name'] = $name;
            $_SESSION['userid'] = $userid;
            $_SESSION['email'] = $email;
            header("location: m4.php");
        } else {
            echo '<script language=javascript>alert("その入力値は登録できません")</script>';
        }
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
    <title>アカウント新規作成1</title>
</head>
<body>
    <h4><a href="../m1.php">戻る</a></h4><hr>
    <h1>アカウントつくるべ</h1>
    <h3>アカウント作成手順<br>1情報入力←イマココ<br>2入力情報の確認<br>3パスコード認証<br>4登録完了</h3>
    <h2>1 idとパスワードを登録します</h2>
    <form method="POST">
        <input type="text" name="name" placeholder="ニックネーム"><br>
        <input type="text" name="userid" placeholder="id"><br>
        <input type="password" name="pass1" placeholder="パスワード（8文字以上）"><br>
        <input type="password" name="pass2" placeholder="パスワード（確認用)"><br>
        <input type="email" placeholder="email" name="email"><br>
        <input type="submit" value="送信">
    </form>
    <h3>id及び，パスワードは忘れないようにご注意ください.</h3>
</body>
</html>