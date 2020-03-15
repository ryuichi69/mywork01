<?php
class deleteForm
{
    /*消去用の項目*/
    public static function Set($user_denpyo_id,$referer,$referer_date = null)
    {
        $data = '<form action="deleteData.php" class="delete_form" method="post">';
        $data .= '<input type="hidden" name="user_denpyo_id" value='.$user_denpyo_id.'>';
        $data .= '<input type="hidden" name="referer" value='.$referer.'>';
        $data .= '<input type="hidden" name="referer_date" value='.$referer_date.'>';
        $data .= '<input type="button" name="delete_button" class="delete_button btn btn-danger" value="削除" >';
        $data .= '</form>';

        return $data;
    }
}
?>
