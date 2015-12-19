<?php

/**
 * Created by creatorfromhell.
 * Date: 12/15/15
 * Time: 1:14 PM
 * Version: Beta 2
 */
abstract class Module
{
    use ModuleInfo;

    /*
     * function
     * init_module
     *
     * Info:
     * Used to initilize this module. Include any extra classes here
     */
    public abstract function init_module();

    /*
     * function
     * get_directory
     *
     * Info:
     * Returns the path to this module's directory.
     */
    public function get_directory() {
        return base_directory."/Modules/".$this->directory."/";
    }

    /**
     * @type function
     * @name get_config_file
     *
     * Info:
     * @desc Returns the specified configuration value located in this module's config.ini.
     */
    public function get_config_file($defaults = array()) {
        $location = $this->get_directory()."config.ini";

        return new ModuleConfiguration($location, $defaults);
    }

    /*
     * function
     * get_config
     *
     * Parameters:
     * option - the name of the configuration option we're looking for
     *
     * Info:
     * Returns the specified configuration value located in this module's config.ini.
     */
    public function get_config($section, $option) {
        $configuration = $this->get_config_file();
        if($configuration instanceof ModuleConfiguration) {
            return $configuration->get_configuration($section, $option);
        }
        return null;
    }
}

trait ModuleInfo {
    private $name;
    private $depends = array();
    private $directory;

    public function get_name() {
        return $this->name;
    }

    public function set_name($name) {
        $this->name = $name;
    }

    public function get_depends() {
        return $this->depends;
    }

    public function set_depends($depends) {
        $this->depends = (is_array($depends)) ? $depends : array($depends);
    }

    public function get_directory() {
        return $this->directory;
    }

    public function set_directory($directory) {
        $this->directory = $directory;
    }
}