<?php
namespace Controller;

use \Frame\Libs\BaseController;
use \Frame\Vendor\Captcha;

final class CaptchaController extends BaseController
{
    public function index()
    {
        $captcha = new Captcha();
        $_SESSION['captcha'] = $captcha->getCode();
        
    }
}
