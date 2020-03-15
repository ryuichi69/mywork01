<?php 
    session_start();
    // セッション変数を全て解除する
    $_SESSION = array();
    // セッションを切断するにはセッションクッキーも削除する。

    setcookie(session_name(), '', time() - 86400);
    session_destroy();
    session_commit();
    // login.phpへ遷移させる
    //sleep(7);
    header("location: login.php");

?>