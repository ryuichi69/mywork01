<?php

class accountForm
{

    //勘定科目データをredisから読み込み、配列の形で返す
    private static function getAccountData() 
    {
            //Redis接続情報
            $redis = new Redis();
            $redis->connect('127.0.0.1',6379) or die("勘定科目データが読み込めません。管理者に連絡して下さい。");
            $redis->select(2);

            //Redisから勘定科目データを読み込み、PHP配列の形に直す
            $account = $redis->hGetAll('account');

            // 現状は勘定科目データがredis上にない場合は終了とする 
            if(count($account) == 0)
            {
                    print "勘定科目データがありません。管理者に連絡してください。";
                    exit;
            }
            //PHPの配列を返す	
            return $account;
    }

    // 入力画面用の勘定科目のプルダウンを生成
    public static function setSelectForm($selected)
    {
        // 勘定科目の配列を読み込む
        $category = accountForm::getAccountData();

        // プルダウンを生成(デザインもここで直す) 
        $data = '<select name="category1" onchange="writeInputTime();"  style="min-width:180px;min-height: 25px;">';
        //$data .= '<option value="">'."全ての勘定科目".'</option>';

        //プルダウンの選択項目を生成する
        foreach ($category as $key => $value) 
        {
            if ($key == $selected) 
            {
                $data .= '  <option value="' . $key . '" selected="selected">' . $value . '</option>';
            }
            else
            {
                $data .= '  <option value="' . $key . '">' . $value . '</option>';
            }
        }
        $data .= '</select>';
        
        return $data;
    }

    // 集計画面のプルダウンを生成
    // 基本的な動作は上の関数を参照
    public static function setSelectForm2($selected) 
    {
        $category = accountForm::getAccountData();
        $data = '<select name="category1" style="min-width:180px;">';
        $data .= '<option value="-1">'."全ての勘定科目".'</option>';

        foreach ($category as $key => $value) 
        {
            if ($key == $selected)
            {
                $data .= '  <option value="' . $key . '" selected="selected">' . $value . '</option>';
            }
            else
            {
                $data .= '  <option value="' . $key . '">' . $value . '</option>';
            }
        }
        $data .= '</select>';

        return $data;
    }

    // 使用した金額の勘定科目を画面上に表示するための関数
    public static function setCategory($value)
    {
        $category = accountForm::getAccountData();
        return $category[$value];
    }
    
}
?>
