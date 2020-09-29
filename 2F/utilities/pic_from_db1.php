<?php
require 'db_connect.php';
require 'checkInput.php';
function picdb1($key) {
    $sql = "select * from $db1 where id=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id',$key,PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
}