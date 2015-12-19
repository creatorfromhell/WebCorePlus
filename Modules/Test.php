<?php

/**
 * Created by creatorfromhell.
 * Date: 12/19/15
 * Time: 8:48 AM
 * Version: Beta 2
 */
class Test extends Module
{
    public function __construct() {
        $this->set_directory('test');
        $this->set_name("Test");
    }

    public function init_module()
    {
        define('message', "Hello World!");
    }
}