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
     * @name load_configurations
     *
     * Info:
     * @desc Loads this module's configuration file, and merges the values with the defaults provided.
     */
    public function load_configurations() {
        if(!file_exists($this->get_directory())) {
            mkdir($this->get_directory());
        }
        $location = $this->get_directory()."config.ini";

        $configuration_file = new ModuleConfiguration($location, $this->configurations);
        $this->configurations = $configuration_file->read_file();
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
        $this->load_configurations();

        return $this->get_configuration($section, $option);
    }
}

trait ModuleInfo {
    private $name;
    private $depends = array();
    private $configurations = array();
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

    public function get_configurations() {
        return $this->configurations;
    }

    public function set_configurations($configurations) {
        $this->configurations = (is_array($configurations)) ? $configurations : array($configurations);
    }

    public function get_configuration($section, $option) {
        return $this->configurations[$section][$option];
    }

    public function set_configuration($section, $option, $value) {
        $this->configurations[$section][$option] = $value;
    }

    public function get_directory() {
        return $this->directory;
    }

    public function set_directory($directory) {
        $this->directory = $directory;
    }
}