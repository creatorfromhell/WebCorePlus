<?php

/**
 * Created by creatorfromhell.
 * Date: 12/26/15
 * Time: 7:28 AM
 * Version: Beta 2
 */
class UtilityModule extends Module
{

    public function __construct()
    {
        $this->set_directory('UtilityModule');
        $this->set_name("UtilityModule");
    }

    public function init_module()
    {
        // TODO: Implement init_module() method.
    }

    public static function str_contains($needle, $haystack) {
        return (strpos($haystack, $needle) !== false);
    }

    public static function upload_file($file, $name, $maxSize = 1000000) {
        global $configuration_values;
        $type = pathinfo(basename($file['name']), PATHINFO_EXTENSION);
        $move = $name.".".$type;
        if(!in_array($type, $configuration_values['file']['allowed_types'])) {
            return;
        }
        if($file['size'] > $maxSize) {
            return;
        }
        if(move_uploaded_file($file['tmp_name'], base_directory.$configuration_values['file']['upload_directory'].$move)) {
            return;
        }
        return;
    }

    public static function check_session($identifier) {
        if($identifier === null) { return false; }
        return isset($_SESSION[$identifier]);
    }

    public static function destroy_session($identifier) {
        if($identifier === null) { return; }
        if(isset($_SESSION[$identifier])) {
            unset($_SESSION[$identifier]);
        }
    }

    /**
     * @param $data
     * @param null $value - The value that should be selected, if any
     * @return string
     */
    public static function to_options($data, $value = null) {
        $return = '';
        foreach($data as &$option) {
            $return .= '<option value="'.$option.'"'.(($value !== null && $value == $option) ? ' selected' : '').'>'.$option.'</option>';
        }
        return $return;
    }

    //Thanks to this comment: http://php.net/manual/en/function.uniqid.php#94959
    public static function generate_uuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

    public static function get_ip() {
        $ip = "";
        if (isset($_SERVER["REMOTE_ADDR"])) {
            $ip = $_SERVER["REMOTE_ADDR"]."";
        } else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"]."";
        } else if ( isset($_SERVER["HTTP_CLIENT_IP"]) ) {
            $ip = $_SERVER["HTTP_CLIENT_IP"]."";
        }
        return $ip;
    }
}