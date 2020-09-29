<?php
require 'utilities/db_connect.php';
require 'utilities/checkInput.php';
if (isset($_POST['delete'])) {
    session_start();
    print_r($_SESSION);
    $id = $_SESSION['id'];
    $sql = 'delete from db1 where id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    header("location: m_delete2.php");
}
?>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>チャットやで</title>
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
    <h1>一度アカウントを削除すると元に戻すことはできません。</h1>
    <form method="POST">
        <!--クリックするとウィンドウで本当に消すか確かめる. -->
        <input type="submit" value="削除" name="delete" onClick="disp();">
    </form>
</body>
</html>