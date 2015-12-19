<?php

/**
 * Created by creatorfromhell.
 * Date: 12/19/15
 * Time: 3:20 AM
 * Version: Beta 2
 */
class ConfigurationSection {
    private $name;
    private $comments;
    private $configurations;

    public function __construct($name = null)
    {
        $this->name = $name;
    }

    public function add_comment($comment) {
        $comments[] = $comment;
    }

    public function set_configuration($identifier, $value) {
        $configurations[$identifier] = $value;
    }

    public function get_configuration($identifier) {
        return $this->configurations[$identifier];
    }

    public function get_name() {
        return $this->name;
    }

    public function set_name($name) {
        $this->name = $name;
    }

    public function get_comments() {
        return $this->comments;
    }

    public function set_comments($comments) {
        $this->comments = $comments;
    }

    public function get_configurations() {
        return $this->configurations;
    }

    public function set_configurations($configurations) {
        $this->configurations = $configurations;
    }
}