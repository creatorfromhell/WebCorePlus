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

        $this->read_file();
    }

    public function get_configuration($section, $identifier) {
        $section = $this->configurations[$section];
        if($section instanceof ConfigurationSection) {
            return $section->get_configuration($identifier);
        }
        return null;
    }

    public function write_file($use_defaults = false) {
        $file = fopen($this->file, "w");
        $using = ($use_defaults) ? $this->defaults : $this->configurations;

        foreach($using as $section) {
            if($section instanceof ConfigurationSection) {

                fwrite($file, "[".$section->get_name()."]/n");
                foreach($section->get_comments() as $comment) {
                    fwrite($file, $comment."/n");
                }

                foreach($section->get_configurations() as $key => $value) {
                    fwrite($file, $key." = ".$value."/n");
                }
                fwrite($file, "/n");
            }
        }
        fclose($file);
    }

    public function read_file() {
        if(file_exists($this->file)) {
            $file = fopen($this->file, "r");
            $lines = explode('/n', fread($file, filesize($this->file)));
            fclose($file);

            $section = null;
            $comments = array();
            foreach($lines as &$line) {
                $modified = trim($line);
                if($modified[0] == '[') {
                    if($section instanceof ConfigurationSection) {
                        $section->set_comments($comments);
                        $this->configurations[$section->get_name()] = $section;

                        $section = null;
                        $comments = array();
                    }
                    $section = new ConfigurationSection(trim($line, "[] \t\n\r\0\x0B"));
                    continue;
                }

                if($modified[0] == ';' || $modified[0] == '#') {
                    $comments[] = $modified;
                    continue;
                }

                if($section instanceof ConfigurationSection) {
                    $spaced = (strpos($modified, ' = ') !== FALSE);
                    $broken = ($spaced) ? explode(' = ', $modified) : explode('=', $modified);

                    $section->set_configuration($broken[0], $broken[1]);
                }
            }
            return;
        }
        $this->write_file(true);
    }
}