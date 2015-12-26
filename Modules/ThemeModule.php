<?php

/**
 * Created by creatorfromhell.
 * Date: 12/20/15
 * Time: 8:34 PM
 * Version: Beta 2
 */
class ThemeModule extends Module
{

    public $themes = array();

    public function __construct()
    {
        $this->set_directory('ThemeModule');
        $this->set_name("ThemeModule");


        $this->set_configurations(array(
            "Main" => array(
                "path" => $this->get_directory()."themes",
                "default_theme" => "Default"
            )
        ));
    }

    public function init_module()
    {
        $this->load_all();
    }

    public function reload($theme) {
        $this->save($theme);
        $this->load($theme);
    }

    public function exists($theme) {
        return in_array($theme, $this->themes);
    }

    public function get_template($theme, $template) {
        $theme_directory = (string)$this->themes[$theme]->directory;
        $directory = $this->get_config("Main", "path")."/".$theme_directory."/templates/";
        $template_location = $directory.$template;
        if(file_exists($template_location)) {
            return $template_location;
        }
        if(isset($this->themes[$this->get_config("Main", "default_theme")])) {
            $theme_directory = (string)$this->themes[$theme]->directory;
            return $this->get_config("Main", "path")."/".$theme_directory."/templates/".$template;
        }
        return $template;
    }

    public function get_includes($theme) {
        $theme = $this->themes[$theme];
        $includes = array();

        foreach(glob($this->get_config("Main", "path")."/".(string)$theme->directory."/js/*.js") as $js) {
            $includes[] = '<script src="'.$js.'?'.time().'" type="text/javascript"></script>';
        }

        foreach(glob($this->get_config("Main", "path")."/".(string)$theme->directory."/css/*.css") as $css) {
            $includes[] = '<link href="'.$css.'?'.time().'" rel="stylesheet" type="text/css" />';
        }
        return $includes;
    }

    private function save($theme) {
        $theme = $this->themes[$theme];
        if($theme instanceof SimpleXMLElement) {
            $theme->asXML($this->get_config("Main", "path")."/".$theme.".xml");
        }
    }

    private function load_all() {
        foreach(glob($this->get_config("Main", "path")."/*.xml") as $theme) {
            $path_info = pathinfo($theme);
            $this->load($path_info['filename']);
        }
    }

    public function load($theme) {
        $file = @simplexml_load_file($this->get_config("Main", "path")."/".$theme.".xml", null, true);
        $this->themes[$theme] = $file;
    }
}