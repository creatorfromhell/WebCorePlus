<?php

/**
 * Created by creatorfromhell.
 * Date: 12/19/15
 * Time: 9:47 PM
 * Version: Beta 2
 */
class CaptchaModule extends Module
{
    /**
     * @var null|string
     */
    private $code = null;
    /**
     * @var null
     */
    private $image = null;

    public function __construct($auto_connect = false)
    {
        $this->set_directory('CaptchaModule');
        $this->set_name("CaptchaModule");
    }

    public function init_module()
    {
    }

    public function get_captcha($code = null, $return = true) {
        $this->code = ($code !== null) ? $code : $this->generate_code();
        $this->generate_image();

        if($return) {
            return $this->return_image();
        }
        $this->print_image();
        return "";
    }

    public function get_code() {
        return $this->code;
    }

    /**
     * @return string
     */
    private function generate_code() {
        $valid_chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+123456789";
        $c = "";
        for($i = 0; $i < 6; $i++) {
            $c .= $valid_chars[rand(0, strlen($valid_chars) - 1)];
        }
        return $c;
    }

    /**
     *
     */
    private function generate_image() {
        if($this->code === null) {
            $this->code = $this->generate_code();
        }
        $this->image = imagecreate(95, 35);
        $colorText = imagecolorallocate($this->image, 255, 255, 255);
        $colorBG = imagecolorallocate($this->image, 82, 139, 185);
        $colorRect = imagecolorallocate($this->image, 163, 163, 163);
        imagefill($this->image, 0, 0, $colorBG);
        imagestring($this->image, 10, 20, 10, $this->code, $colorText);
        imageline($this->image, 20, 12, 38, 20, $colorRect);
        imageline($this->image, 50, 15, 80, 15, $colorRect);
        imageline($this->image, 70, 0, 30, 35, $colorRect);
    }

    /**
     * @return string
     */
    private function get_base64() {
        ob_start();
        imagejpeg($this->image, NULL, 100);
        $bytes = ob_get_clean();
        return base64_encode($bytes);
    }

    /**
     *
     */
    private function print_image() {
        echo "<img id='captcha_image' src='data:image/jpeg;base64,".$this->get_base64()."' />";
    }

    /**
     * @return string
     */
    private function return_image() {
        return "<img id='captcha_image' src='data:image/jpeg;base64,".$this->get_base64()."' />";
    }
}