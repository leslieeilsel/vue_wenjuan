<?php
namespace Controller;

use \Frame\Libs\BaseController;
use \Model\QuestionModel;
use \Model\WenjuanModel;
use \Model\UserModel;

final class QuestionController extends BaseController
{
    public $wTable = 'fb_questions';

    public function index()
    {
        $this->denyAccess();
        $this->id = ID;
        self::$questionModelObj = QuestionModel::getInstance();
        self::$modelObj = WenjuanModel::getInstance();
        self::$userModelObj = UserModel::getInstance();
        $this->checkUser(self::$userModelObj, self::$questionModelObj, true);
        $this->u = HEADER['Username'];
        $this->p = HEADER['Password'];
        if (METHOD === 'POST') { // 添加
            $this->add();
        } elseif (METHOD === 'DELETE') { // 删除
            $this->delete();
        } elseif (METHOD === 'PUT') { // 修改
            $this->edit();
        }
    }

    public function add()
    {

        $dataArr = (json_decode($_POST["data"], true));
        $tLen = mb_strlen($dataArr['qtitle']);
        if (!($tLen >= 1 && $$tLen <= 255)) {
            $this->json(500, '1字<=问题标题<=255字');
            die();
        }
        $dataArr['user_id'] = $_SESSION['uid'];
        // 插入问卷
        if (!self::$questionModelObj->insert($dataArr)) {
            
            $this->json(500, '插入失败');
        } else {
            $this->json(200, '插入成功');
        }

    }
    // 问卷基础信息
    public function edit()
    {
     
        $dataArr = json_decode($this->getBody()['data'], true);
     
        if (self::$questionModelObj->update($dataArr, "'" . $this->id . "'")) {
            $this->json(200, '修改问题成功');
        } else {
            $this->json(200, '修改问题成功');
        }
    }
    public function delete()
    {
     
        if (self::$questionModelObj->delete($this->wTable, ID)) {
            $this->json(200, '删除成功');
        } else {
            $this->json(500, '删除失败');
        }
    }
}
