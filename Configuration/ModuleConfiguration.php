<?php

/**
 * Created by creatorfromhell.
 * Date: 12/19/15
 * Time: 2:32 AM
 * Version: Beta 2
 */
class ModuleConfiguration
{
    private $file;
    private $defaults;
    private $configurations;

    public function __construct($file, $defaults = array()) {
        $this->file = $file;
        $this->defaults = $defaults;
        $this->configurations = array();
    }

    public function write_file($use_defaults = false) {
        $file = fopen($this->file, "w");
        $using = ($use_defaults) ? $this->defaults : $this->configurations;

        foreach($using as $section => $configurations) {
            fwrite($file, "[".$section."]\n");
            foreach($configurations as $key => $value) {
                fwrite($file, $key." = ".$value."\n");
            }
            fwrite($file, "\n");
        }
        fclose($file);
    }

    public function read_file() {
        if(file_exists($this->file)) {
            return parse_ini_file($this->file, true);
        }
        $this->write_file(true);
        return $this->defaults;
    }
}