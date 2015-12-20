<?php
/**
 * Created by creatorfromhell.
 * Date: 12/19/15
 * Time: 8:34 AM
 * Version: Beta 2
 */
include_once('WebCoreLoader.php');

WebCore::get_module("SQLModule");
$captcha = WebCore::get_module("CaptchaModule");

if($captcha instanceof CaptchaModule) {
    $captcha->get_captcha("l33t", false);
}

WebCore::get_module("CaptchaModule")->get_captcha("testing", false);