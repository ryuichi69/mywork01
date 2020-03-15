<?php 
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
    <link rel="stylesheet" href="Css/oneDayPaid.css">
</head>

<?php
/*各種MySQL接続部分の埋め込み(ログイン情報、ユーザーコード、接続用のMySQLクラスなど)*/
    require_once 'Config/Config.php';        
    include('Model/db.php');
    include('ViewForm/accountForm.php'); 
    include('ViewForm/deleteForm.php');    
?>
<?php
	$mysql = new dbTrnMoney($_SESSION["user_id"]);
        if(isset($_GET['date']))
        {   
            $date = $_GET['date'];

            //円グラフに出力する用のデータ
            $conditions = array("Date" => $_GET['date']);
            $oneDayCategoryData = $mysql->getTotalPaidData("OneDayCategory",$conditions);
            $oneDayData = $mysql->getPaidData("OneDay",$conditions);
            $OneDayTotalData = $mysql->getTotalPaidData("OneDayTotal",$conditions);
        }
        else
        {
            $oneDayCategoryData = array();
            $oneDayData = array();
            $OneDayTotalData = array();
        }
?>
<?php 
if(count($oneDayCategoryData) != 0)
 {    
    $pieChartData = "[";
     foreach($oneDayCategoryData as $value)
    { 
        $pieChartData .='["'.$value['category1'].'",'.$value['paid_money'].'],';
    }
    $pieChartData = substr($pieChartData,0,-1);
    $pieChartData .= "]";
 }
 else
 {
    $pieChartData = "[['',0]]";
 }
 
 ?>

  <body>
    <?php require_once 'ViewCommon/container.php';?>
    <?php require_once 'ViewCommon/jumbutron.php';?>
    <?php /*htmlクラスのrowやcolは、それぞれTwitter BootStrap関連のマニュアルをご確認下さい */ ?>
  <div class="row marketing">
        <div class="col-lg-12">
        <h4>1日の使用金額を確認</h4>
        <form name="iform" action="oneDayPaid.php" method="get">	
	<table class="table">
            <tr>
	　　　 <?php if(!isset($_GET["date"])) { ?>
                	<td>日付</td><td><input type="text" id="datepicker" name="date" value="<?php echo date("Y-m-d");?>"><input type="submit" class="btn btn-success date_submit" value="検索"></td>
	       <?php }else{ ?>
                	<td>日付</td><td><input type="text" id="datepicker" name="date" value="<?php echo $_GET["date"];?>"><input type="submit" class="btn btn-success date_submit" value="検索"></td>
	       <?php } ?>
	       
            </tr>
        </table>
	</form>
       </div>
      <div class="col-lg-12">
          <h4>使用金額</h4>
          <h5>構成比率</h5>
          <div id="jqPlot-circle"></div>
          <h5>使用内訳</h5>
          <table class="table">
            <thead>
            <tr>
             <th>借方勘定科目</th>
             <th style="text-align:right;">金額</th>
            </tr>
            <!--グラフ描画部分-->
           <?php $i=0; ?>
           <?php foreach($OneDayTotalData as $value){ ?>
           <?php $color = ($i % 2 == 0) ? TABLE_COLOR : TABLE_COLOR2 ; ?>
            <tr style=<?php echo $color;?>>
                <td><b>使用金額合計(円)</b></td>
                <td style="text-align:right;"><b><?php echo number_format($value['paid_money']); ?></b></td>
            </tr>	
            <?php $i++; ?>
        <?php }?>
           <?php $i=0; ?>
           <?php foreach($oneDayCategoryData as $value){ ?>
           <?php $color = ($i % 2 == 0) ? TABLE_COLOR : TABLE_COLOR2; ?>
            <tr style=<?php echo $color;?>>
               <td><?php echo AccountForm::setCategory($value['category1']); ?></td>
               <td style="text-align:right;"><?php echo number_format($value['paid_money']); ?></td>
            </tr>	
            <?php $i++; ?>
        <?php }?>
    </table>
        </div>
        <div class="col-lg-12">
          <h4>使用状況</h4>
            <table class="table">
                <thead>
                <tr>
                 <th>仕訳番号</th>
                 <th>借方勘定科目</th>
                 <th style="text-align:right;">金額</th>
                 <th class="mobile_disable" style="text-align:right;">備考</th>
                 <th style="text-align:right;">修正</th>
                 <th style="text-align:right;">削除</th>
                </tr>
                <!--グラフ描画部分-->
               <?php $i=0; ?>
               <?php foreach($oneDayData as $value){ ?>
               <?php $color = ($i % 2 == 0) ? TABLE_COLOR : TABLE_COLOR2 ; ?>
                <tr style=<?php echo $color;?>>
                   <td><?php echo $value['id']; ?></td>
                   <td><?php echo AccountForm::setCategory($value['category1']); ?></td>
                   <td style="text-align:right;"><?php echo number_format($value['user_paid_money']);?></td>
                   <td class="mobile_disable" style="text-align:right;"><?php echo $value['reason']; ?></td>
                   <td style="text-align:right;">
		        <form id="updateForm" name="iform" action="<?php echo INPUT_DATA_URL; ?>" method="POST">	

				<?php /*伝票の新規入力・更新に共通するPOSTパラメーター*/ ?>
				<input type ="hidden" name="referer" value="dayPage">
                                <input type ="hidden" name="referer_date" value="<?php echo $date;?>">
                                <input type ="hidden" name="user_denpyo_id" value="<?php echo $value['user_denpyo_id'];?>">
				<input type ="hidden" name="user_paid_money" value="<?php echo $value['user_paid_money'];?>">
				<input type ="hidden" name="reason" value="<?php echo $value['reason'];?>">
				<input type ="hidden" name="category1" value="<?php echo $value['category1'];?>">
				<input type ="hidden" name="update_datetime" value="<?php echo $value['update_datetime'];?>">
				<input type ="submit" class="btn btn-warning" value="修正">
			</form>
		　　</td>
                   <?php if($date != ""){ ?>
                   <td style="text-align:right;"><?php echo deleteForm::Set($value['user_denpyo_id'],DAY_PAGE,$date);?></td>
                   <?php }else{ ?>
                   <td style="text-align:right;"><?php echo deleteForm::Set($value['user_denpyo_id'],DAY_PAGE);?></td>
                   <?php } ?>                   
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
    
     $(function() {
        $( "#datepicker" ).datepicker({dateFormat: 'yy-mm-dd'});
    });

    //データを一旦グラフ化
    //ここに出力する
    $(document).ready(function()
    {
        data2 = <?php echo $pieChartData;?>;
        plot2 = $.jqplot('jqPlot-circle',[data2],
        {
            seriesColors: ["#FFFFFF","#EEEEEE","#DDDDDD","#CCCCCC","#BBBBBB","#AAAAAA",
                "#999999","#888888","#777777","#666666"],
            seriesDefaults: 
            {
                    renderer: jQuery . jqplot . PieRenderer,
                    rendererOptions: 
                    {
                            padding: 5,
                            dataLabels: 'percent',
                            showDataLabels: true,
                            numberColumns: 2,
                            startAngle: 270//一番大きい要素が上
                    }
            },
            grid: 
            {
                // グラフを囲む枠線の太さ、0で消える
                borderWidth: 1,
                // 背景色を透明に
                background: 'transparent',
                // 影もいらない
                shadow: true,
            }
        });

        window.onresize = function(event) 
        {
            //plot1.replot();
            plot2.replot();
        }
    });
    
    $('.delete_form').click(function()
    {
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
            
  </script>  
</html>