<?php

        class db
        {

                //MySQL接続情報
                protected $connectInfo = "mysql:host=XXXXX;dbname=XXXXX;charset=utf8";
                protected $connectUser = 'XXXXX';
                protected $connectPass = 'XXXXX';
                static protected $userId = null; 

                public $pdoObject;    

                //クエリ格納の為の配列    
                public $sqlQueries = array();

                // PDOオブジェクトをセット
                public function __construct($userId=null)
                {

                    //PDOオブジェクトを定義
                    $this->pdoObject = new PDO($this->connectInfo,$this->connectUser,$this->connectPass,array(PDO::ATTR_EMULATE_PREPARES => false));

                    //PDOのエラー設定を定義
                    //$this->pdoObject->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    //IDをセット
                    self::$userId = $userId; 
                }   
            
        }

        class dbTrnMoney extends db 
	{

                #protected $dbTableName = 'trn_money';
                protected $dbTableName = 'trn_user_2020_money';
            
		/*
                    *DBの結果を配列で出力(件数指定版)
                    *@引数　tblname:テーブル名	
		*/
		function getPaidData($status,$conditions=array()):array
		{

                    // 最新10件を取得
                    $sql = "";
                    $sql .= "SELECT ";
                    $sql .= "     * ";
                    $sql .= "FROM ";
                    $sql .= $this->dbTableName. " ";
                    $sql .= "WHERE ";
                    $sql .= "     user_id = ".parent::$userId;
                    $sql .= " AND final_flg = 0 ";

                    if($status == "QuickEdit")
                    {
                        $sql .= "order by id desc ";
                        $sql .= "limit 10 ";                
                    }
                    else if($status == "OneDay")
                    {
                        $sql .= " AND update_datetime = '{$conditions["Date"]}'";
                    }
                    
                    $query = $this->pdoObject->query($sql);
                    $data = $query->fetchAll(PDO::FETCH_ASSOC);

                    
                    return $data;//最終的な結果を画面表示部分に渡す
		}                

                /**/
                function getTotalPaidData($status,$conditions=array())
                {

                    // 最新10件を取得
                    $sql = "";
                    $sql .= "SELECT ";
                    $sql .= "     sum(user_paid_money) as paid_money ";

                    if($status != "MonthTotal")
                    {
                         $sql .= "     ,category1 ";
                    }
                    
                    //　日付毎に出力する場合、日付も追加する
                    if($status == "MonthTotal")
                    {
                        $sql .= "     ,count(*) as cnt ";
                    }
                    else if($status == "MonthPerDay")
                    {
                        $sql .= "     ,update_datetime as time ";
                    }
                    else if($status == "MonthCategory")
                    {
                        $sql .= "     ,count(*) as cnt ";
                        $sql .= "     ,avg(user_paid_money) ";
                        $sql .= "     ,update_datetime as time ";
                    }
                    
                    $sql .= "FROM ";
                    $sql .= $this->dbTableName. " ";
                    $sql .= "WHERE ";
                    $sql .= "     user_id = ".parent::$userId;
                    $sql .= " AND final_flg = 0 ";
                    
                    if($status == "OneDayTotal")
                    {
                        $sql .= " AND update_datetime = '{$conditions["Date"]}'";
                    }
                    else if($status == "OneDayCategory")
                    {
                        $sql .= " AND update_datetime = '{$conditions["Date"]}'";
                        $sql .= " group by category1 order by paid_money desc ";
                    }

                    else if($status == "MonthTotal")
                    {
                        
                        if($conditions["Category"] != -1 && $conditions["Category"] != "" && $conditions["Category"] != null)
			{
                            $sql .= " AND category1 = '{$conditions["Category"]}' ";
                        }                        
                        
                        $sql .= " AND update_datetime between '{$conditions["startDate"]}' and '{$conditions["endDate"]}' ";
                    }
                    
                    else if($status == "MonthPerDay")
                    {
                        
                        if($conditions["Category"] != -1 && $conditions["Category"] != "" && $conditions["Category"] != null)
			{
                            $sql .= " AND category1 = '{$conditions["Category"]}' ";
                        }
                        
                        $sql .= " AND update_datetime between '{$conditions["startDate"]}' and '{$conditions["endDate"]}' ";
                        $sql .= " group by update_datetime order by update_datetime asc ";                    
                    }
                    
                    else if($status ==" MonthCategory")
                    {
                        
                        if($conditions["Category"] != -1 && $conditions["Category"] != "" && $conditions["Category"] != null)
			{
                            $sql .= " AND category1 = '{$conditions["Category"]}' ";
                        }
                        
                        $sql .= " AND update_datetime between '{$conditions["startDate"]}' and '{$conditions["endDate"]}' ";
                        $sql .= " group by category1 order by paid_money desc ";
                    }
                    
                    $query = $this->pdoObject->query($sql);
                    $data = $query->fetchAll(PDO::FETCH_ASSOC);
                
                return $data;    
                }
                
                //アフィリエイト情報のクエリを配列にセット
                public function savePaidData($conditions)
                {
                    // 各種配列を準備
                    $insertColumn = null;
                    $insertColumnValue = null;
                    $updateColumnValue = null;
                    $PrepareArray = array();

                    // 既に登録されている案件かどうか調べる
                    $checkQuery  = "SELECT count(*) as cnt FROM ".$this->dbTableName." ";
                    $checkQuery .= "WHERE user_id = "."'".parent::$userId."'";
                    $checkQuery .= " AND  user_denpyo_id = "."'".$conditions["user_denpyo_id"]."'";
                    $query = $this->pdoObject->query($checkQuery);
                    $existRecord = $query->fetch(PDO::FETCH_ASSOC);
                    
                    // 新規に登録される案件ならばINSERT文を生成
                    if($existRecord["cnt"] == 0)
                    {

                        // 新しい伝票番号を取得
                        $sql = "";
                        $sql = $sql."SELECT ";
                        $sql = $sql."      IFNULL((max(user_denpyo_id)+1),1) as max_id ";
                        $sql = $sql."FROM ";
                        $sql = $sql.$this->dbTableName." ";
                        $sql = $sql."WHERE user_id = ".parent::$userId;

                        $query = $this->pdoObject->query($sql);
                        $row = $query->fetch(PDO::FETCH_ASSOC);

                        $conditions["user_denpyo_id"] = $row["max_id"];
                        $conditions["user_id"] = parent::$userId;
                        
                        foreach($conditions as $key => $value)
                        {
                            $insertColumn .= ",".$key;
                            $insertColumnValue .= ",:".$key;
                            $PrepareArray[":".$key] = $value;
                        }

                        //先頭の,は除去する
                        $insertColumn = substr($insertColumn, 1);
                        $insertColumnValue = substr($insertColumnValue, 1);

                        //Insert文を生成
                        $Query  = "INSERT INTO ".$this->dbTableName." ";
                        $Query .= "(";
                        $Query .= $insertColumn;
                        $Query .= ") VALUES ";
                        $Query .= "(";
                        $Query .= $insertColumnValue;
                        $Query .= ") ";
                    }
                    // 既にに登録された案件ならばINSERT文を生成
                    else
                    {
                        foreach($conditions as $key => $value)
                        {
                            $updateColumnValue .= ",".$key." = :".$key." ";
                            $PrepareArray[":".$key] = $value;
                        }

                        //先頭の,は除去する
                        $updateColumnValue = substr($updateColumnValue, 1);

                        //UPDATE文を生成
                        $Query  = "UPDATE ".$this->dbTableName." ";
                        $Query .= "SET ".$updateColumnValue;
                        $Query .= "WHERE user_id = "."'".parent::$userId."'"." ";
                        $Query .= " AND  user_denpyo_id = "."'".$conditions["user_denpyo_id"]."'";
                    }

                    //SQL文を実行する
                    $sth = $this->pdoObject->prepare($Query);
                    $sth->execute($PrepareArray);
                }
                              
                /* 入力データを論理削除 */
		function deletePaidData($conditions)
		{

                        /*フラグ1にして伝票を論理削除する*/
                        $updateQuery = "";
                        $updateQuery .= "UPDATE ".$this->dbTableName." "; 
			$updateQuery .= " SET  final_flg = 1 ";
			$updateQuery .= "WHERE user_id = ".parent::$userId;
			$updateQuery .= " AND  user_denpyo_id = ".$conditions["user_denpyo_id"];

                        //SQL文を実行する
                        $this->pdoObject->query($updateQuery);
                        
		}                
                
                /*データを更新する*/
                function updatePaidData($conditions)
                {
                        //今までの伝票を削除する
                        $this->deletePaidData($conditions);
                                
                        //新たに伝票を起こして更新
                        $this->insertPaidData($conditions);
                        
		}	                

	}
?>
