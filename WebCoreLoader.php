<?php
/**
 * Created by creatorfromhell.
 * Date: 12/19/15
 * Time: 8:34 AM
 * Version: Beta 2
 */
function webcore_loader($class) {
    $root = rtrim(realpath(__DIR__), '/').'/';
    if(file_exists($root."Configuration/".$class.".php")) {
        require_once($root."Configuration/".$class.".php");
        return true;
    } else if(file_exists($root."Exceptions/".$class.".php")) {
        require_once($root."Exceptions/".$class.".php");
        return true;
    } else if(file_exists($root."Modules/".$class.".php")) {
        require_once($root."Modules/".$class.".php");
        return true;
    } else if(file_exists($root.$class.".php")) {
        require_once($root.$class.".php");
        return true;
    }
    return false;
}

spl_autoload_register('webcore_loader');