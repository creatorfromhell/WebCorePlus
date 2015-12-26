<?php

/**
 * Created by creatorfromhell.
 * Date: 12/25/15
 * Time: 10:26 AM
 * Version: Beta 2
 */
class PermissionsModule extends Module
{
    private $sql;

    public function __construct()
    {
        $this->set_directory('PermissionsModule');
        $this->set_name("PermissionsModule");
        $this->set_depends("SQLModule");

        $this->set_configurations(array(
            "Main" => array(
                "default_group" => "Guest"
            )
        ));
    }

    public function init_module()
    {
        $this->sql = WebCore::get_module("SQLModule");
    }
}