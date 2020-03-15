<?php
$Date = date("Y-m-d");
$StartDate = date("Y-m", strtotime($Date. "-1 months"))."-20";
$EndDate = date("Y-m", strtotime($Date))."-19";

echo <<<EOD
    <div class="container">
      <div class="header">
        <ul class="nav nav-pills pull-right">
          <li><a href="inputData.php">入力</a></li>
          <li><a href="oneDayPaid.php?date={$Date}">日計</a></li>
          <li><a href="totalPaid.php?start_date={$StartDate}&end_date={$EndDate}&category1=-1">累計</a></li>
          <li><a href="logout.php">ログアウト</a></li>
        </ul>
      </div>
     </div>
EOD;


?>
