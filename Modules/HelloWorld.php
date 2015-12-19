<?php

/**
 * Created by creatorfromhell.
 * Date: 12/19/15
 * Time: 8:41 AM
 * Version: Beta 2
 */
class HelloWorld extends Module
{
    public function __construct() {
        $this->set_directory('hello');
        $this->set_name("HelloWorld");
        $this->set_depends("Test");
    }

    public function init_module()
    {
        echo message;
    }
}