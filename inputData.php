<?php 
ini_set("display_errors", On);
error_reporting(E_ALL);
//セッション開始
session_cache_limiter('nocache');
session_start();

//セッションが無い場合
if(!isset($_SESSION["user_id"]))
{
    // ログイン画面へ遷移させる
    header("location: login.php");
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>    
    <?php require_once 'ViewCommon/head.php';?>
    <link rel="stylesheet" href="Css/inputData.css">
</head>

<?php
        /*各種MySQL接続部分の埋め込み(ログイン情報、ユーザーコード、接続用のMySQLクラスなど)*/
 	require_once 'Config/Config.php';
        include('Model/db.php');
        include('ViewForm/accountForm.php');
        include('ViewForm/deleteForm.php');
?>
<?php
	$dbTrnMoney = new dbTrnMoney($_SESSION["user_id"]);
	$paidData = $dbTrnMoney->getPaidData("QuickEdit");
?>

  <script type="text/javascript">
  /*入力時間計測用の関数(ここから)*/
  function getCurrentTime()
  {
        var date = new Date();
        return Math.floor( date.getTime() / 100 );
  }
  
  function writeStartTime()
  {
      document.getElementById("start_time").value = getCurrentTime();
  }
  
  function writeInputTime()
  {
      document.getElementById("end_time").value = getCurrentTime();
      document.getElementById("input_time").value = (getCurrentTime() - document.getElementById("start_time").value)/10;
  }
  /*入力時間計測用の関数(ここまで)*/
  </script>   

  
  <body onload="writeStartTime()">
    <?php require_once 'ViewCommon/container.php';?>
    <?php require_once 'ViewCommon/jumbutron.php';?>
  <div class="row marketing">
      
        <?php if(isset($_GET["error_status"])){ ?>
            <?php if($_GET["error_status"] == 1){?>
              <div class="alert alert-danger" role="alert">データ入力エラーです。</div>  
            <?php }else if($_GET["error_status"] == 0){?>
              <div class="alert alert-success" role="alert">入力に成功しました。</div>
            <?php }?>
        <?php }?> 
              
        <div class="col-lg-12">
        <h4>支出(費用)の入力</h4>
        <form id="paidForm" name="iform" action="insertData.php" method="POST">	

	<?php /*伝票の新規入力・更新に共通するPOSTパラメーター*/ ?>
        <?php if(!isset($_POST["referer"])){ ?>    
            <input type ="hidden" name="referer" value="<?php echo PAID_PAGE;?>">           
        <?php }elseif($_POST["referer"] == DAY_PAGE){ ?>    
            <input type ="hidden" name="referer" value="<?php echo DAY_PAGE;?>">           
            <input type ="hidden" name="referer_date" value="<?php echo $_POST["referer_date"];?>">           
        <?php }else{ ?>         
            <input type ="hidden" name="referer" value="<?php echo PAID_PAGE;?>">           
        <?php } ?>              
                
        <input type="hidden" id="start_time" name="start_time" value="0">
        <input type="hidden" id="end_time" name="end_time" value="0">
        <input type="hidden" id="input_time" name="input_time"value="0.0">
	<input type ="hidden" name="user_get_money" value="0">

	<?php /*単に新規項目を入力するとき*/ ?>
	<?php if(!isset($_POST["user_denpyo_id"])){ ?>
        <table class="table">
            <tr>
               <td>日時</td><td><input type="text" id="paidDate" class="input_form" name="update_datetime" value="<?php echo date("Y-m-d");?>" onchange="writeInputTime();"></td>
            </tr>
            <tr>
               <td>支出</td><td><input type="number" id="paidMoney" class="input_form" name="user_paid_money" maxlength="10" value="0" onKeyUp="this.value=this.value.replace(/[^0-9]+/,'')" onkeydown="writeInputTime();"></td>
            </tr>
            <tr>
                <td>内訳</td><td><input type="text" id="paidReason" class="input_form" name="reason" maxlength="20" value="入力なし" onkeydown="writeInputTime();"></td>
            </tr>
            <tr>
                <td>借方科目</td><td><?php echo AccountForm::setSelectForm(-1);?></td>
            </tr>
            <tr>
               <td>送信</td><td><input type="button" value="保存する" onclick="formSubmit();" class="submit_button btn btn-success input_form"></td>
            </tr>         
        </table>

	<?php /*伝票内容を修正するとき*/ ?>
	<?php }else{ ?>
	<input type ="hidden" name="user_denpyo_id" value="<?php echo $_POST["user_denpyo_id"];?>">
        <table class="table">
            <tr>
               <td>仕訳番号</td><td><?php echo $_POST["user_denpyo_id"];?></td>
            </tr>
            <tr>
               <td>日時</td><td><input type="text" id="paidDate" class="input_form" name="update_datetime" value="<?php echo $_POST["update_datetime"];?>" onchange="writeInputTime();"></td>
            </tr>
            <tr>
               <td>支出</td><td><input type="number" id="paidMoney" class="input_form" name="user_paid_money" maxlength="10" value="<?php echo $_POST["user_paid_money"];?>" onKeyUp="this.value=this.value.replace(/[^0-9]+/,'')" onkeydown="writeInputTime();"></td>
            </tr>
            <tr>
                <td>内訳</td><td><input type="text" id="paidReason" class="input_form" name="reason" maxlength="20" value="<?php echo $_POST["reason"];?>" onkeydown="writeInputTime();"></td>
            </tr>
            <tr>
                <td>借方科目</td><td><?php echo AccountForm::setSelectForm($_POST["category1"]);?></td>
            </tr>
            <tr>
               <td>送信</td><td><input type="button" value="保存する" onclick="formSubmit();" class="submit_button btn btn-success input_form"></td>
            </tr>              
        </table>

	<?php } ?>

	</form>
       </div>
        <div class="col-lg-12">
          <h4>クイック修正</h4>
        <table class="table">
            <thead>
            <tr>
             <th>仕訳番号</th>
             <th>日時</th>
             <th style="text-align:right;">借方勘定科目</th>
             <th class="mobile_disable" style="text-align:right;">内訳</th>
             <th style="text-align:right;">金額</th>
             <th style="text-align:right;">修正</th>
             <th class="mobile_disable" style="text-align:right;">削除</th>
            </tr>
            <!--グラフ描画部分-->

           <?php $i=0; ?>
           <?php foreach($paidData as $value){ ?>

           <?php $color = ($i % 2 == 0) ? TABLE_COLOR : " " ; ?>
            <tr style=<?php echo $color;?>>
               <td><?php echo $value['user_denpyo_id']; ?></td>
               <td><?php echo date("Y年m月d日",strtotime($value['update_datetime']));?></td>
               <td style="text-align:right;"><?php echo AccountForm::setCategory($value['category1']); ?></td>
               <td class="mobile_disable" style="text-align:right;"><?php echo $value['reason']; ?></td>
               <td style="text-align:right;"><?php echo number_format($value['user_paid_money']); ?></td>
                   <td style="text-align:right;">
                       <form id="updateForm" name="iform" action="<?php echo INPUT_DATA_URL;?>" method="POST">	

				<?php /*伝票の新規入力・更新に共通するPOSTパラメーター*/ ?>
				<input type ="hidden" name="user_denpyo_id" value="<?php echo $value['user_denpyo_id'];?>">
				<input type ="hidden" name="user_paid_money" value="<?php echo $value['user_paid_money'];?>">
				<input type ="hidden" name="reason" value="<?php echo $value['reason'];?>">
				<input type ="hidden" name="category1" value="<?php echo $value['category1'];?>">
				<input type ="hidden" name="update_datetime" value="<?php echo $value['update_datetime'];?>">
				<input type ="hidden" name="referer" value="<?php echo PAID_PAGE;?>">
                                <input type ="submit" class="btn btn-warning" value="修正">
			</form>
		　　</td>

               <td class="mobile_disable" style="text-align:right;"><?php echo deleteForm::Set($value['user_denpyo_id'],PAID_PAGE);?></td>
            </tr>	
            <?php $i++; ?>
        <?php }?>
    </table>
        </div>
      </div>

    <?php require_once 'ViewCommon/footer.php';?>

    </div> <!-- /container -->
  </body>
  <script type="text/javascript">
  $('.delete_form').click(function(){
    if(!confirm('本当に削除しますか？'))
    {
        /* キャンセルの時の処理 */
        return false;
    }
    else
    {
        $(this).submit();
    }    
    }); 
 
   $(function() {
    $( "#paidDate" ).datepicker({dateFormat: 'yy-mm-dd'});
  });
  
  /*submit(Enter)の防止*/           
  $(function() 
  {
    $(document).on("keypress", "input:not(.allow_submit)", function(event) 
    {
        return event.which !== 13;
    });
  });

  /*未入力項目の確認*/
  function formSubmit()
  {
	/*yyyy-mm-dd hh:mm:ss形式の正規表現*/
        var validateRegPattern = /[0-9][0-9]{3}-[0-1][0-9]-[0-3][0-9]/;
        
        /*日付欄に不正な文字が入力された時*/
	var paidDate = document.getElementById("paidDate").value;
        
	if(!paidDate.match(validateRegPattern))
	{
		//空欄のアナウンスを流す
		alert("yyyy-mm-dd形式にて日付を入力して下さい");		

		//フォーカスを移す
		document.getElementById("paidDate").focus();

		//処理を抜ける
		return false;
	}	

	/*日付欄が未入力の場合*/
	if(document.getElementById("paidDate").value == "" || document.getElementById("paidDate").value == null)
	{
		//空欄のアナウンスを流す
		alert("日付欄が空欄です");		

		//フォーカスを移す
		document.getElementById("paidDate").focus();

		//処理を抜ける
		return false;
	}	

	/*支出欄が未入力の場合*/
	if(document.getElementById("paidMoney").value == "" || document.getElementById("paidMoney").value == null)
	{
		//空欄のアナウンスを流す
		alert("支出欄が空欄です");		

		//フォーカスを移す
		document.getElementById("paidMoney").focus();

		//処理を抜ける
		return false;
	}	


	/*内訳欄が未入力の場合*/
	if(document.getElementById("paidReason").value == "" || document.getElementById("paidReason").value == null)
	{
		//空欄のアナウンスを流す
		alert("内訳欄が空欄です");		

		//フォーカスを移す
		document.getElementById("paidReason").focus();

		//処理を抜ける
		return false;
	}

	//全部バリデーションが済んだらDBへデータを投げる
	$("#paidForm").submit();

  }

  </script>    
</html>
