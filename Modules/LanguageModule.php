<?php

/**
 * Created by creatorfromhell.
 * Date: 12/20/15
 * Time: 8:34 PM
 * Version: Beta 2
 */
class LanguageModule extends Module
{

    public $languages = array();

    public function __construct()
    {
        $this->set_directory('LanguageModule');
        $this->set_name("LanguageModule");


        $this->set_configurations(array(
            "Main" => array(
                "path" => $this->get_directory()."languages",
                "default_language" => "en",
                "black_list" => "",
                "time_format" => "d-m-Y"
            )
        ));
    }

    public function init_module()
    {
        $this->load_all();
    }

    public function reload($language) {
        $this->save($language);
        $this->load($language);
    }

    public function exists($language) {
        return in_array($language, $this->languages);
    }

    public function get_value($language, $path) {
        $language = $this->languages[$language];
        if($language instanceof SimpleXMLElement) {
            $value = (string)$language->xpath(str_ireplace("->", "/", $path))[0];
            return $value;
        }
        return $path;
    }

    private function save($language) {
        $language = $this->languages[$language];
        if($language instanceof SimpleXMLElement) {
            $language->asXML($this->get_config("Main", "path")."/".$language.".xml");
        }
    }

    private function load_all() {

        foreach(glob($this->get_config("Main", "path")."/*.xml") as $theme) {
            $path_info = pathinfo($theme);
            $this->load($path_info['filename']);
        }
    }

    /**
     * @param $name
     */
    public function load($language) {
        $file = @simplexml_load_file($this->get_config("Main", "path")."/".$language.".xml", null, true);
        $this->languages[(string)$file->xpath("short")[0]] = $file;
    }
}