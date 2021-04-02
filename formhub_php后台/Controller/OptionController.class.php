<?php
namespace Controller;

use \Frame\Libs\BaseController;
use \Model\OptionModel;
use \Model\WenjuanModel;
use \Model\QuestionModel;
use \Model\UserModel;
final class OptionController extends BaseController
{
    public $wTable = 'fb_options';

    public function index()
    {
        
        $this->denyAccess();
        $this->id = ID;
     
        self::$questionModelObj = QuestionModel::getInstance();
        self::$userModelObj = UserModel::getInstance();
        self::$optionModelObj = OptionModel::getInstance();
        self::$modelObj = WenjuanModel::getInstance();
       
      
        $this->u = HEADER['Username'];
        $this->p = HEADER['Password'];
        if (METHOD === 'POST') { // 添加
            $this->checkUser(self::$userModelObj, self::$questionModelObj, true);
            $this->add();
        } elseif (METHOD === 'DELETE') { // 删除
            $this->checkUser(self::$userModelObj, self::$optionModelObj, true);
            $this->delete();
        } elseif (METHOD === 'PUT') { // 修改
            $this->checkUser(self::$userModelObj, self::$optionModelObj, true);
            $this->edit();
        }
    }

    public function add()
    {

        $dataArr = (json_decode($_POST["data"], true));
      
        foreach($dataArr as $item) {
            $tLen = mb_strlen($item['title']);
            if (!($tLen >= 1 && $$tLen <= 255)) {
                $this->json(500, '1字<=问题标题<=255字');
                die();
            }
            $item['user_id'] = $_SESSION['uid'];
            // 插入选项
            if (!self::$optionModelObj->insert($item)) {
                $this->json(500, '插入失败');
            }
        }
        $this->json(200, '插入成功');

    }

    public function edit()
    {
    
        $dataArr = json_decode($this->getBody()['data'], true);
        
        if (self::$optionModelObj->update($dataArr, "'" . $this->id . "'")) {
            $this->json(200, '修改选项成功');
        } else {
            $this->json(500, '修改选项失败');
        }
    }
    public function delete()
    {

        
        if (self::$optionModelObj->delete($this->wTable, ID)) {
            $this->json(200, '删除成功');
        } else {
            $this->json(500, '删除失败');
        }
    }
}
