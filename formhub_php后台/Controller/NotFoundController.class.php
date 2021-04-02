<?php
namespace Controller;

use \Frame\Libs\BaseController;

class NotFoundController extends BaseController
{
    public function index()
    {
        $this->json(404, '对不起页面不存在');
    }
}
