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
    <link rel="stylesheet" href="Css/totalPaid.css">
</head>

<?php
    /*各種MySQL接続部分の埋め込み(ログイン情報、ユーザーコード、接続用のMySQLクラスなど)*/
    require_once 'Config/Config.php';        
    include('Model/db.php');
    include('ViewForm/accountForm.php');  
?>
<?php
    $mysql = new dbTrnMoney($_SESSION["user_id"]);

    if(isset($_GET['start_date']) && isset($_GET['end_date']))
    {
        $Category = (isset($_GET['category1'])) ? $_GET['category1'] : -1; 
        $conditions = array("Category" => $Category , "startDate" => $_GET['start_date'],"endDate" => $_GET['end_date']);

        //勘定科目毎の使用状況向けのデーター
        $MonthCategoryData = $mysql->getTotalPaidData("MonthCategory",$conditions);

        //1日毎の使用状況向けのデーター
        $MonthPerDayData = $mysql->getTotalPaidData("MonthPerDay",$conditions);

        //合計金額を出力するデーター
        $MonthTotalData = $mysql->getTotalPaidData("MonthTotal",$conditions);
    }
    else
    {
        $MonthCategoryData = array();
        $MonthPerDayData = array();
        $MonthTotalData = array();
    }

     //　円グラフグラフ部分の配列を準備する
     if(count($MonthCategoryData) != 0)
     {    
        $pieChartData = "[";
         foreach($MonthCategoryData as $value)
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

     //　折れ線グラフ部分の配列を準備する
     if(count($MonthPerDayData) != 0)
     {    
        $lineGraphData = "[";
        foreach($MonthPerDayData as $value)
        { 
            $lineGraphData .='["'.date("Y-m-d",strtotime($value['time'])).'",'.$value['paid_money'].'],';
        }
        $lineGraphData = substr($lineGraphData,0,-1);
        $lineGraphData .= "]";
     }
     else
     {
        $lineGraphData = "[[0,0]]";
     }
     
 ?>
  
  <body>
    <?php require_once 'ViewCommon/container.php';?>
    <?php require_once 'ViewCommon/jumbutron.php';?>
  <div class="row marketing">
        <div class="col-lg-12">
        <h4>使用合計金額を確認</h4>
        <form name="iform" action="totalPaid.php" method="get">	
	<table class="table">

	    <?php if(!isset($_GET["start_date"])){ ?>
            <tr>
               <td>検索開始日</td><td><input type="text" id="datepicker" name="start_date" value="0000-00-00"></td>
            </tr>
            <tr>
               <td>検索終了日</td><td><input type="text" id="datepicker2" name="end_date" value="0000-00-00"></td>
            </tr>
            <tr>
               <td>借方勘定科目</td><td><?php echo AccountForm::setSelectForm2(-1);?></td>
            </tr>
	    <?php }else{ ?>
            <tr>
               <td>検索開始日</td><td><input type="text" id="datepicker" name="start_date" value="<?php echo $_GET["start_date"];?>"></td>
            </tr>
            <tr>
               <td>検索終了日</td><td><input type="text" id="datepicker2" name="end_date" value="<?php echo $_GET["end_date"];?>"></td>
            </tr>
            <tr>
               <td>借方勘定科目</td><td><?php echo AccountForm::setSelectForm2($_GET["category1"]);?></td>
            </tr>
	    <?php } ?>

            <tr>
               <td colspan="2" style="text-align:center;"><input type="submit" value="検索" class="submit_button btn btn-success"></td>
            </tr>
        </table>
	</form>
       </div>
      <div class="col-lg-12">
          <h4>勘定科目毎の使用状況</h4>
          <h5>構成比率</h5>
          <div id="jqPlot-circle"></div>
          <h5>使用内訳</h5>
          <table class="table">
            <thead>
            <tr>
             <th>借方勘定科目</th>
             <th style="text-align:right;"><b>合計金額</b></th>
             <th style="text-align:right;">件数</th>
             <th style="text-align:right;">1件毎</th>
            </tr>
            <tr>
             <td></td>
             <td style="text-align:right;"><b><?php echo @number_format($MonthTotalData[0]['paid_money']);?></b></td>
             <td style="text-align:right;"><b><?php echo @number_format($MonthTotalData[0]['cnt']);?></b></td>
             <td style="text-align:right;">=支出÷件数</td>
            </tr>
           <?php $i=0; ?>
           <?php foreach($MonthCategoryData as $value){ ?>
           <?php $color = ($i % 2 == 0) ? TABLE_COLOR : TABLE_COLOR2 ; ?>
            <tr style=<?php echo $color;?>>
               <td><a href="<?php echo TOTAL_PAID_URL."?start_date={$_GET['start_date']}&end_date={$_GET['end_date']}&category1={$value['category1']}";?>"><?php echo AccountForm::setCategory($value['category1']); ?></a></td>
               <td style="text-align:right;"><b><?php echo @number_format($value['paid_money']); ?></b></td>               
               <td style="text-align:right;"><?php echo $value['cnt']; ?></td>               
               <td style="text-align:right;"><?php echo @number_format($value['avg(user_paid_money)']); ?></td>
            </tr>	
            <?php $i++; ?>
        <?php }?>
    </table>
        </div>
        <div class="col-lg-12">
       <h4>1日毎の使用状況</h4>
       <div id="jqPlot-sample"></div>
       <table class="table">
            <thead>
            <tr>
             <th>日付</th>
             <th style="text-align:right;">合計金額</th>
            </tr>            
            <tr>
                <td><b>総合計金額</b></td>
                <td style="text-align:right;"><b><?php echo @number_format($MonthTotalData[0]['paid_money']);?></b></td>
            </tr>
           <?php $i=0; ?>
           <?php foreach($MonthPerDayData as $value){ ?>            
           <?php $color = ($i % 2 == 0) ? TABLE_COLOR : TABLE_COLOR2; ?>
            <tr style=<?php echo $color;?>>
                <td><a href="<?php echo ONEDAY_PAID_URL."?date=".date("Y-m-d",strtotime($value['time']));?>" target="_blank"><?php echo date("Y年m月d日",strtotime($value['time']));?></a></td>
               <td style="text-align:right;"><?php echo @number_format($value['paid_money']); ?></td>
            </tr>	
            <?php $i++; ?>
        <?php }?>
    </table>
        </div>
    </div>

    <?php require_once 'ViewCommon/footer.php';?>
    </div> <!-- /container -->
 
    <!--グラフ描画部分　-->
    <script type="text/javascript">
        $(function() {
          $( "#datepicker" ).datepicker({dateFormat: 'yy-mm-dd'});
        });
        $(function() {
          $( "#datepicker2" ).datepicker({dateFormat: 'yy-mm-dd'});
        });
        
        //データを一旦グラフ化
        //ここに出力する
        $(document).ready(function()
        {
            data = <?php echo $lineGraphData;?>;
            plot1 = $.jqplot( 'jqPlot-sample', [data],
            {
                seriesColors: ["#999999"],
                axes:{
                    xaxis:{
                        renderer: jQuery . jqplot . DateAxisRenderer,
                        min: '<?php echo $_GET['start_date'];?>',
                        max: '<?php echo $_GET['end_date'];?>',
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
                plot1.replot();
                plot2.replot();
            }
  });
  </script>
  </body>
</html>