<?php

/**
 * Created by creatorfromhell.
 * Date: 12/15/15
 * Time: 1:19 PM
 * Version: Beta 2
 */
class MissingDependencyException extends Exception
{
    public function __construct($name, $dependency, $code = 0)
    {
        $message = "Module \"".$name."\" requires module \"".$dependency."\", which could not be found!";
        parent::__construct($message, $code);
    }
}