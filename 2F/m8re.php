<?php
require 'utilities/checkInput.php';
require 'utilities/db_connect.php';
require 'utilities/create_db10.php'; require 'utilities/kill_db10.php';
//-----------sessionよびだし----------------
session_start();
$name = $_SESSION['name'];
$userid = $_SESSION['userid'];
$email = $_SESSION['email'];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>再設定の直し</title>
</head>
<body>
    <h4><a href="m1.php">戻る</a></h4><hr>
    <h1>再入力してください</h1>
    <form method="POST">
        <input type="text" name="name" placeholder="ニックネーム" value="<?php echo h($name);?>"><br>
        <input type="text" name="userid" placeholder="id" value="<?php echo h($userid);?>"><br>
        <input type="password" name="pass1" placeholder="パスワード（8文字以上"><br>
        <input type="password" name="pass2" placeholder="パスワード（確認用)"><br>
        <input type="email" placeholder="email" name="email"><br>
        <input type="submit" value="送信">
    </form>
    <h3>id及び，パスワードは忘れないようにご注意ください.</h3>
</body>
</html>

<?php
//値の取得
$name = checkInput("name");
$userid = checkInput("userid");
$pass_b1 = checkInput("pass1");//入力欄1
$pass_b2 = checkInput("pass2");//入力欄2
$email = checkInput("email");
$password = null;
//この中を最後まで走って，入力値が良くないときは，passwordがnullのままになる
if (!($pass_b1===null) && !($pass_b2===null)){
    if ($pass_b1 === $pass_b2) {
        if(mb_strlen($pass_b1) < 8) {
            echo '<script language=javascript>alert("パスワードは8文字以上としてください")</script>';
        }else{
            $password = password_hash($pass_b1, PASSWORD_DEFAULT);
        }    
    } else {
        echo '<script language=javascript>alert("パスワードが一致していません")</script>';
    }
}
try {//以降の処理は，全ての入力がnullでない時のみ実行
    if (!($name === null) && !($userid === null) && !($password === null) && !($email === null)) {
        //---------------入力されたデータが既存のdb1内passwordで重複していないか調べる-----------------------------------------
        $sql = 'SELECT * FROM db1';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        $check = 0; //最後まで0だったら全て重複無し
        foreach ($results as $row) {
            if ($row['userid'] === $userid) {
                $check += 1;
            }
            
            if (password_verify($pass_b1, $row['pass'])) {
                $check += 1;
            }
        }
        //------------checkが0のときのみ，sessionを保持．この段階で，db10にid,pass,randは送っておく．----------------
        if ($check === 0) {
            $random = rand(1000000,9999999);
            require '/public_html/New_tech/m6/sep15/2F/utilities/create_db10.php';
            $sql = $pdo->prepare("INSERT INTO db10 (userid, password_d, onetime_pass) VALUES (:userid, :password_d, :onetime_pass)");
            $sql->bindParam(':userid', $userid, PDO::PARAM_STR);
            $sql->bindParam(':password_d', $password, PDO::PARAM_STR);
            $sql->bindParam(':onetime_pass', $random, PDO::PARAM_STR);
            $sql->execute();
            //セッションを消して，上書き
            require '/public_html/New_tech/m6/sep15/2F/utilities/kill_sessions.php';
            session_start();
            $_SESSION['name'] = $name;
            $_SESSION['userid'] = $userid;
            $_SESSION['email'] = $email;
            header("location: m9.php");
        } else {
            echo '<script language=javascript>alert("その入力値は登録できません")</script>';
        }
    } 
} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    // エラー内容は本番環境ではログファイルに記録して， Webブラウザには出さないほうが望ましい
    exit($e->getMessage()); 
}


