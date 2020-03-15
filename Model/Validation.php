<?php
class ValidationRule
{
    /**/
    public static function isnull($string=null):bool
    {
        if(strval($string) == "" || strval($string) == null)
        {
            return false;
        }
        return true;
    }
    
    /**/
    public static function isDate($string=null):bool
    {
        if(!preg_match("/[0-9][0-9]{3}-[0-1][0-9]-[0-3][0-9]/",$string))
        {
            return false;
        }
        return true;
    }

    /**/
    public static function isMoney($string=null):bool
    {
        if(!preg_match("/[0-9]{1,10}/",$string))
        {
            return false;
        }
        return true;
    }      
}

?>