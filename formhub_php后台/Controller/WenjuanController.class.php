<?php
namespace Controller;

use \Frame\Libs\BaseController;
use \Model\OptionModel;
use \Model\QuestionModel;
use \Model\WenjuanModel;
use \Model\UserModel;
final class WenjuanController extends BaseController
{

    public $wTable = 'fb_wenjuans';

    public function index()
    {
    

        $this->id = ID;
        self::$userModelObj = UserModel::getInstance();
        self::$optionModelObj = OptionModel::getInstance();
        self::$questionModelObj = QuestionModel::getInstance();
        self::$modelObj = WenjuanModel::getInstance();
        
        if(METHOD !== 'POST' &&  METHOD !== 'GET') {
            $this->denyAccess();
        }
      
        $this->checkId();
        if(METHOD !== 'POST' &&  METHOD !== 'GET') { //除添加问卷才校验身份
            $this->checkUser(self::$userModelObj, self::$modelObj);
        }

        $this->u = HEADER['Username'];
        $this->p = HEADER['Password'];
        if (METHOD === 'POST') { // 添加
            if (ID) { // 填报问卷
                $this->upload();
            } else { // 下载问卷
                $this->denyAccess();
                $this->add();
            }
        } elseif (METHOD === 'GET') { // 查看
            $this->show();
        } elseif (METHOD === 'DELETE') { // 删除
            // header 注意首字母大写
            if (HEADER['Iszero']) {
                $this->zero();
            } else {
                $this->delete();
            }
        } elseif (METHOD === 'PUT') { // 修改
            
        

            if (HEADER['Ques']) { // 添加问题
                
                $this->addQuestion();
            } else { // 下载问卷
                $this->edit();
            }
           
        }
    }
    public function isProvided()
    { // 是否已经填报
        $arr = self::$modelObj->fetchOne(" wenjuan_id = '$this->id' ");
        $provided = explode(',', $arr['provided']);
        if (in_array($_SESSION['uid'], $provided)) {
            $this->json(500, '你已经填过此问卷!');
        }
    }
    // 填报问卷
    public function upload()
    {
        $isMustLogin = self::$modelObj->fetchOne(" wenjuan_id = '$this->id' ")['mustLogin'];
        
        if($isMustLogin=='1') { // 是否登录才能填报
            $this->denyAccess();
            $this->isProvided();
        }else {
            // 非登录 重复提交检测
            if(HEADER['Formhub']==$this->id) {
                $this->json(500, '你已经填过此问卷!');
            }
        }
       
        $dataArr = json_decode($this->getBody()['data'], true);
        
        
       
        // 防止修改上传数据, 重复提交某题
        try {
            $questions = ($dataArr['questions']);
            foreach ($questions as $q) {
                $q['content'] .= $q['content']?'&$%':''; //特殊字符组合分割简答项
                
                self::$questionModelObj->insertAnswer(" personCount=personCount+ '1', content = CONCAT(content,'{$q['content']}') ", $q['id']);
                                                                                             
            }
            $options = array_unique($dataArr['options']);
            foreach ($options as $o) {
                
                self::$optionModelObj->insertAnswer(" personCount=personCount+ '1' ", $o);
            }
            // 更新问卷人数
            self::$modelObj->updatePerson(" personCount=personCount+ '1' ", ID);
            // 更新已经填用户信息
            $userid = $_SESSION['uid'] . ',';
            self::$modelObj->updatePerson(" provided=CONCAT(provided," . "'$userid'" . ") ", ID);
            $this->json(200, '感谢填报');
        } catch (Expression $e) {
            $this->json(500, '网络错误');
        }
    }
    public function addQuestion() {
        $dataArr = json_decode($this->getBody()['data'], true);
      
        $wenjuan_id = $this->id;
        $questions = $dataArr['questions'];
       
        foreach ($questions as $q) {
            // 遍历插入问题
            $data = array(
                'qtitle' => $q['qtitle'],
                'wenjuan_id' => $wenjuan_id,
                'qtype' => $q['qtype'],
                'w_order' => $q['w_order'],
                'user_id' => $_SESSION['uid'],
            );
 
            if (!self::$questionModelObj->insert($data)) {
                $this->json(500, '插入失败');
                die();
            }
           
            // 获取刚插入的题id
            $qid = self::$questionModelObj->getRecentId('`fb_questions`');
            $options = $q['options'];
            
            foreach ($options as $o) {

                $data = array(
                    'title' => $o['title'],
                    'qid' => $qid[0],
                    'w_order' => 0,
                    'user_id' => $_SESSION['uid'],
                );
                
                if (!self::$optionModelObj->insert($data)) {
                    $this->json(500, '插入失败');
                    die();
                }
            }

        }
        // 获取刚才插入id, 更新content
        $recentId = self::$questionModelObj->fetchAllByCond("true", " id ", " id desc limit 1 ")[0]['id'];
        $dataArr = array('personCount' => 0, 'content' => '');
        self::$questionModelObj->updateByCond($dataArr, " id = '" . $recentId . "'");
        $this->json(200, '问题及选项添加成功');
       
    }
    public function show()
    {
        $arrs = self::$modelObj->fetchOne(" wenjuan_id='$this->id'" );
        $isMustLogin = $arrs['mustLogin'];
        // $this->json(500, $isMustLogin);
        if($isMustLogin=='1') { // 是否登录才能填报
            $this->denyAccess();
        }else {
            // 非登录 重复提交检测
            if(HEADER['Formhub']==$this->id) {
                $this->json(500, '你已经填过此问卷!');
            }
        }
        
        $code = $arrs['code'];
        $status = $arrs['status'];
        $uid = $arrs['user_id'];
        $timeEnd = $arrs['timeEnd'];
        
        // 填报问卷
        $inputCode = !isset(HEADER['Code'])?'0':HEADER['Code'];
        // $this->json(500, '问卷过期');
        if ($_SESSION['uid'] != $uid && ($code!='0' && $code != $inputCode)) {
           
            if(($code!='0' && $code != $inputCode)){
                $this->json(500, '请输入正确提取码');
                die();
            }
            $this->json(500, '无权访问问卷');
            die();
        }
        
        // 过期问卷只能 问卷发布者填写
        if ((intVal($this->get_total_millisecond()) > intVal(($timeEnd)) || $status == 0) && $_SESSION['uid'] != $uid) {
            $this->json(500, '问卷过期');
            die();
        }
        
        if ($_SESSION['uid'] == $uid) {
            // 问卷所有者获取完全问卷信息
            $questions = self::$questionModelObj->fetchAllByCond(" status=1 and `wenjuan_id`='" . ID . "'");

            foreach ($questions as &$qitem) {
              
                $qitem['options'] = self::$optionModelObj->fetchAllByCond(" status=1 and `qid`='" . $qitem['id'] . "'");

            }
            
            $arrs['questions'] = $questions;

        } else {
            // id
            $wenjuan_id = $arrs['wenjuan_id'];
            // title
            $title = $arrs['title'];
            // intro
            $intro = $arrs['intro'];
            // timeEnd
            $timeEnd = $arrs['timeEnd'];

            $arrs = array('wenjuan_id' => $wenjuan_id, 'title' => $title,
                'intro' => $intro, 'timeEnd' => $timeEnd);

            $questions = self::$questionModelObj->fetchAllByCond(" status=1 and `wenjuan_id`='" . ID . "'", "`id`,`qtitle`,`mustbe`,`qtype`");

            foreach ($questions as &$qitem) {

                $qitem['options'] = self::$optionModelObj->fetchAllByCond(" status=1 and `qid`='" . $qitem['id'] . "'", "`id`,`title`");

            }

            $arrs['questions'] = $questions;
        }

        $this->json(200, $arrs);

    }
    // 检查标题、简介格式
    public function checkInput($title, $intro)
    {
        $tLen = mb_strlen($title);
        $iLen = mb_strlen($intro);
        if (!($tLen >= 1 && $tLen <= 140)) {
            $this->json(500, '1字<=标题<=140字');
            die();
        }
        if (!($iLen >= 1 && $iLen <= 255)) {
            $this->json(500, '1字<=标题<=255字');
            die();
        }
    }
    public function get_total_millisecond()

    {
    $time = explode (" ", microtime () );

    $time = $time [1] . ($time [0] * 1000);

    $time2 = explode ( ".", $time );

    $time = $time2 [0];

    return $time;

    }
    public function add()
    {
        $dataArr = (json_decode($_POST["data"], true)); 
        $wenjuan_id = '' . md5(uniqid(md5(microtime(true)), true));
       
        $this->checkInput($dataArr['title'], $dataArr['intro']);
        
        $data = array(
            'user_id' => $_SESSION['uid'],
            'wenjuan_id' => $wenjuan_id,
            'timeCreated' => $this->get_total_millisecond(),
            'timeEnd' => $dataArr['timeEnd'],
            'personCount' => 0,
            'totalPerson' => intVal($dataArr['totalPerson']),
            'title' => $dataArr['title'],
            'intro' => $dataArr['intro'],
            'code ' => $dataArr['code'] ? $dataArr['code'] : 0,
            'provided' => $_SESSION['uid'] . ',',
            'mustLogin' => $dataArr['mustLogin']?$dataArr['mustLogin']:'0'
        );

        // 插入问卷
        if (!self::$modelObj->insert($data)) {
            $this->json(500, '插入失败');
        }

        $questions = $dataArr['questions'];

        foreach ($questions as $q) {
            // 遍历插入问题
            $data = array(
                'qtitle' => $q['qtitle'],
                'wenjuan_id' => $wenjuan_id,
                'qtype' => $q['qtype'],
                'w_order' => $q['w_order'],
                'user_id' => $_SESSION['uid'],
            );

            if (!self::$questionModelObj->insert($data)) {
                $this->json(500, '插入失败');
                die();
            }
            // 获取刚插入的题id
            $qid = self::$questionModelObj->getRecentId('`fb_questions`');
            $options = $q['options'];
            foreach ($options as $o) {

                $data = array(
                    'title' => $o['title'],
                    'qid' => $qid[0],
                    'w_order' => $o['w_order'],
                    'user_id' => $_SESSION['uid'],
                );
                if (!self::$optionModelObj->insert($data)) {
                    $this->json(500, '插入失败');
                    die();
                }
            }

        }
        $this->json(200, '问卷添加成功');

    }
    // 问卷基础信息
    public function edit()
    {
        
        $dataArr = json_decode($this->getBody()['data'], true);
        $title = isset($dataArr['title']) ? $dataArr['title'] : '不存在';
        $intro = isset($dataArr['intro']) ? $dataArr['intro'] : '不存在';
        $this->checkInput($title, $intro);

        if (self::$modelObj->updateW($dataArr, "'" . $this->id . "'")) {
            $this->json(200, '修改问卷成功');
        } else {
            $this->json(500, '修改问卷失败');
        }
    }
    public function delete()
    {

        if (self::$modelObj->deleteW($this->wTable, ID)) {
            $this->json(200, '删除成功');
        } else {
            $this->json(500, '删除失败');
        }
    }
    // 问卷清0
    public function zero()
    {
        try {
            $wid = ID; // 问卷加密id
            $uid = $_SESSION['uid'];
            $dataArr = array('personCount' => 0, 'provided' => $uid . ',');
            self::$modelObj->updateW($dataArr, "'" . $this->id . "'");

            // 当前问卷所有问题personCount = 0
            $dataArr = array('personCount' => 0, 'content' => '');
            self::$questionModelObj->updateByCond($dataArr, " wenjuan_id = '" . $this->id . "'");
            $qArr = self::$questionModelObj->fetchAll(" wenjuan_id = '" . $this->id . "'");

            foreach ($qArr as $q) {
                $dataArr = array('personCount' => 0, 'content' => '');
                self::$optionModelObj->updateByCond($dataArr, " qid = '" . $q['id'] . "'");
            }
            $this->json(200, '重置完成');
        } catch (Expression $e) {
            $this->json(500, '重置失败');
        }
    }

    

}
