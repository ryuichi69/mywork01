<?php
ini_set("display_errors", On);
error_reporting(E_ALL);
header("Content-type:text/html;charset=UTF-8");
require_once 'Config/Config.php';
include('Model/db.php');
include('Model/Validation.php');
/*************************************/
/*   各種必要な情報群(POST情報)	     　*/
/*************************************/
session_start();

//セッションが無い場合、誤登録を防ぐために強制ログアウトする
if(!isset($_SESSION["user_id"]))
{
    // ログイン画面へ遷移させる
    header("location: logout.php");
}
$referer = $_POST["referer"];

$condition = array();
$condition["user_denpyo_id"] = (isset($_POST["user_denpyo_id"])) ? htmlspecialchars($_POST["user_denpyo_id"]):0;
$condition["user_paid_money"] =  htmlspecialchars($_POST['user_paid_money']);
$condition["reason"] = htmlspecialchars($_POST['reason']);
$condition["update_datetime"] =  htmlspecialchars($_POST['update_datetime']);
$condition["category1"] = htmlspecialchars($_POST['category1']);
$condition["input_time"] = htmlspecialchars($_POST['input_time']);

//サーバーサイド側でもう一度バリデーションを行う
$error_status = 0;
if(ValidationRule::isDate($condition["update_datetime"]) == false)
{
    $error_status = 1;
}

if(ValidationRule::isMoney($condition["user_paid_money"]) == false)
{
    $error_status = 1;
}

if(ValidationRule::isnull($condition["reason"]) == false)
{
    $error_status = 1;
}

//バリデーションが成功したとき
if($error_status == 0)
{
    // トランザクションテーブルを準備
    $dbTrnMoney = new dbTrnMoney($_SESSION["user_id"]);
    
    // データーを保存
    $suucessFlg = $dbTrnMoney->savePaidData($condition);    
}

if($referer == PAID_PAGE)
{
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: ".INPUT_DATA_URL."?error_status=".$error_status);
}
elseif($referer == DAY_PAGE)
{
    header("HTTP/1.1 301 Moved Permanently");
    if($_POST["referer_date"] != "")
    {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: ".ONEDAY_PAID_URL."?date={$_POST["referer_date"]}"."&error_status=".$error_status);   
    }
    else
    {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: ".ONEDAY_PAID_URL);   
    }
}
?>
