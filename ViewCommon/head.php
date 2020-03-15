<?php
$Css = <<<EOD
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="robots" content="noindex, follow" />
    <link rel="shortcut icon" href="docs-assets/ico/favicon.png">

    <title>支出計算・集計</title>
    <!-- Bootstrap core CSS -->
    <link href="Bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="Bootstrap/docs-assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript" src="Bootstrap/html5jp/graph/line.js"></script>
    <!--datepicker-->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <!-- 円グラフ描画用 -->
    <script type="text/javascript" src="Plugin/jquery.jqplot.min.js"></script>
    <script type="text/javascript" src="Plugin/jqplot.dateAxisRenderer.min.js"></script>
    <script type="text/javascript" src="Plugin/jqplot.pieRenderer.min.js"></script>
    <link href="Plugin/jquery.jqplot.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Css/common.css">
EOD;

echo $Css;
?>
