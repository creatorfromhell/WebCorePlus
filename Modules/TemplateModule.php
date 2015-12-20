<?php

/**
 * Created by creatorfromhell.
 * Date: 12/19/15
 * Time: 9:38 PM
 * Version: Beta 2
 */
class TemplateModule extends Module
{
    public $rules = array();

    public function __construct($auto_connect = false)
    {
        $this->set_directory('TemplateModule');
        $this->set_name("TemplateModule");
    }

    public function init_module()
    {
    }

    public function template($template_file, $rules = array(), $return = false) {
        $this->rules = $rules;
        $lines = $this->parse_template($template_file);
        $template = "";
        if(is_array($lines)) {
            foreach ($lines as &$line) {
                $template .= $line;
            }
        }
        if ($return) {
            return $template;
        }
        echo $template;
        return "";
    }

    /*
     * Reads every line in a .tpl file.
     */
    private function read_template($name) {
        $lines = array();
        $file = fopen($name, 'r');
        while(!feof($file)) {
            $lines[] = stream_get_line($file, 30000, "\n");
        }
        return $lines;
    }

    private function parse_template($template_file) {

        if(file_exists($template_file)) {
            $lines = $this->read_template($template_file);
            $filtered = array();
            foreach($lines as &$line) {
                $filtered[] = $this->filter_rules($line);
            }
            return $filtered;
        }
        return "Failed to parse template ".$template_file.".";
    }

    private function filter_rules($string) {
        $matched = array();
        preg_match_all("/\\{([^}]+)\\}/", $string, $matched, PREG_SET_ORDER);
        if(!empty($matched)) {
            foreach ($matched as &$rule) {
                $string = str_replace($rule[0], $this->parse_rule(trim($rule[1], " \t\n\r\0\x0B{}")), $string);
            }
        }
        return $string;
    }

    private function parse_rule($rule) {
        $special_rules = array("include", "function");
        $rule_check = explode("->", $rule);
        if(in_array($rule_check[0], $special_rules)) {
            switch($rule_check[0]) {
                case "include":
                    return $this->include_template($rule);
                case "function":
                    return $this->call_function($rule);
            }
        }
        if(strpos($rule_check[0], "&") !== false && in_array(trim($rule_check[0], "&"), $special_rules)) {
            $rule_check[0] = trim($rule_check[0], "&");
        }
        /*
         * This allows the parsing of include rules passed through the rules array without going over the nesting
         * limit for web servers that use xdebug.
         */
        $rule_value = $this->get_rule(implode("->", $rule_check));
        if(strpos($rule_value, "include->") !== false) {
            $rule_value = trim($rule_value, " \t\n\r\0\x0B{}");
            $location = explode("->", $rule_value)[1];
            if(strpos($location, ".tpl") === false) {
                $location = $location.".tpl";
            }
            $include = new TemplateModule();
            return $include->template($location, $this->rules, true);
        }
        return $this->get_rule(implode("->", $rule_check));
    }

    private function include_template($rule) {
        $location = str_ireplace("include->", "", $rule);
        if(strpos($location, ".tpl") === false) {
            $location = $location.".tpl";
        }
        return $this->template(true, $location);
    }

    private function call_function($rule) {
        $rule_parts = explode("->", $rule);
        if(function_exists($rule_parts[1])) {
            $parameters = array();
            if(count($rule_parts) > 2) {
                $parameters = explode(":", $rule_parts[2]);
            }
            $value = call_user_func_array($rule_parts[1], $parameters);
            if($value != null) {
                return $value;
            }
            return "";
        }

        return "{ ".$rule." }";
    }

    private function get_rule($rule) {
        $value = $this->array_path($this->rules, $rule);
        if(!is_null($value)) {
            return $value;
        }
        return "{ ".$rule." }";
    }

    private function array_path($array, $path, $delimiter = "->") {
        $path_array = explode($delimiter, $path);
        $tmp = $array;

        foreach($path_array as $p) {
            if(!isset($tmp[$p])) {
                return null;
            }
            $tmp = $tmp[$p];
        }
        return $tmp;
    }
}