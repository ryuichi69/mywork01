<?php
header("Content-type:text/html;charset=UTF-8");
require_once 'Config/Config.php';
include('Model/db.php');

session_start();

//セッションが無い場合、誤登録を防ぐために強制ログアウトする
if(!isset($_SESSION["user_id"]))
{
    // ログイン画面へ遷移させる
    header("location: logout.php");
}

$condition["user_denpyo_id"] = $_POST['user_denpyo_id'];
$referer = $_POST['referer'];

/*このタイミングでMySQLを動かす！*/
$dbTrnMoney = new dbTrnMoney($_SESSION["user_id"]);
$data_delete = $dbTrnMoney->deletePaidData($condition);

if($referer == PAID_PAGE)
{
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: ".INPUT_DATA_URL);
}
elseif($referer == DAY_PAGE)
{
    header("HTTP/1.1 301 Moved Permanently");
    if($_POST["referer_date"] != "")
    {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: ".ONEDAY_PAID_URL."?date={$_POST["referer_date"]}");   
    }
    else
    {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: ".ONEDAY_PAID_URL);   
    }
}
?>