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
     */
    public function __construct()
    {
    }

    //Funzione per selezionare la sottostringa tra @ e . nel domionio delle email
    function subString($str){
        $subString = substr($str, strpos($str, "@") +1);
        $subString = substr($subString, 0,strpos($subString, "."));
        return $subString;
    }

}