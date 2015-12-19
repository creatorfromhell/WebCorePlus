<?php

/**
 * Created by creatorfromhell.
 * Date: 12/15/15
 * Time: 1:17 PM
 * Version: Beta 2
 */
class ModuleInvalidException extends Exception
{
    public function __construct($name, $code = 0)
    {
        $message = "Module \"".$name."\" either doesn't exist or isn't a child of the Module class.";
        parent::__construct($message, $code);
    }
}