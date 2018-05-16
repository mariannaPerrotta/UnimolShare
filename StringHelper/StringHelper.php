<?php
/**
 * Created by PhpStorm.
 * User: Andrea
 * Date: 16/05/18
 * Time: 23:59
 */

class StringHelper
{

    /**
     * StringHelper constructor.
     * @param string $char
     */
    public function __construct()
    {
    }


    function subString($str){
        $subString = substr($str, strpos($str, "@") +1);
        $subString = substr($subString, 0,strpos($subString, "."));
        return $subString;
    }

}