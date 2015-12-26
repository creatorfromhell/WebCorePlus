<?php

/**
 * Created by creatorfromhell.
 * Date: 12/15/15
 * Time: 1:09 PM
 * Version: Beta 2
 */

/*
 * definition
 * base_directory
 *
 * Info:
 * Defines the base path used for including/detecting modules & resources.
 */
define('base_directory', rtrim(realpath(__DIR__), '/').'/');

define('module_file', base_directory."/Modules/{name}.php");

class WebCore
{

    /*
     * function
     * exists
     *
     * Parameters:
     * name - string - name of the module
     *
     * Info:
     * Returns true if the specified module exists.
     */
    public static function exists($name) {
        return file_exists(str_replace("{name}", $name, module_file));
    }

    /*
     * function
     * get_module
     *
     * Parameters:
     * name - string/array - name of the desired module(s)
     *
     * Info:
     * Returns the instance, or array of instances of the specified module(s).
     */
    public static function get_module($name, $args = array()) {
        $multiple = is_array($name);
        if($multiple) {
            $return = [];
            foreach($name as $n) {
                if(self::exists($n)) {
                    $return[$n] = self::reflect_get($n, $args[$n]);
                }
            }
            return $return;
        }
        return self::reflect_get($name, $args);
    }

    /*
     * function
     * reflect_get
     *
     * Parameters:
     * name - string - name of the file to retrieve via reflection
     *
     * Info:
     * Returns a new instance of the class($file) using reflection.
     *
     * Throws:
     * ModuleInvalidException
     */
    private static function reflect_get($name, $args = array()) {
        if(self::exists($name)) {
            $location = str_replace("{name}", $name, module_file);

            include_once($location);
            $path_info = pathinfo($location);
            $reflector = new ReflectionClass($path_info['filename']);
            $instance = (is_array($args) && count($args) > 0) ? $reflector->newInstanceArgs($args) : $reflector->newInstance();

            if($instance instanceof Module) {
                foreach($instance->get_depends() as $dependency) {
                    if(self::exists($dependency)) {
                        self::reflect_get($dependency);
                        continue;
                    }
                    throw new MissingDependencyException($name, $dependency);
                }
                $instance->init_module();
                return $instance;
            }
            throw new ModuleInvalidException($name);
        }
        throw new ModuleInvalidException($name);
    }
}