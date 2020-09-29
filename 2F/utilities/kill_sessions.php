<?php
//残留分のセッションは死なせる．
session_start(); // セッション開始
$_SESSION = array();// セッションの値を初期化
session_destroy();// セッションを破棄