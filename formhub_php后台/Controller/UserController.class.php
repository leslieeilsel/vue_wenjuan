<?php
namespace Controller;

use \Frame\Libs\BaseController;
use \Model\UserModel;

final class UserController extends BaseController
{
    public $userTable = 'fb_user';
    public function index()
    {
        self::$modelObj = UserModel::getInstance();
        $this->v = HEADER['Verify'];
        $this->u = HEADER['Username'];
        $this->p = HEADER['Password'];
        $this->id = ID;

        if (METHOD === 'PUT') { // 更新
            $this->update();
        } elseif (METHOD === 'GET') { // 搜索所有注册者
            $this->search();
        } elseif (METHOD === 'DELETE') { // 删除
            $this->delete();
        } elseif (METHOD === 'POST') { // 添加
            $this->add();
        }
    }
    public function search()
    {
        $this->denyAccess();
        $this->checkUser(self::$modelObj);

        if ($this->id) {
            $this->checkId();
            $arrs = self::$modelObj->fetchOne("id = $this->id");
        } else {
            $arrs = self::$modelObj->fetchAll();
        }
        $code = 200;
        if (!$arrs['msg']) {
            $arrs['msg'] = "用户获取失败";
            $code = 500;
        }
        $this->json($code, $arrs);
    }
    public function add()
    {

        $data = array(
            'username' => $this->u,
            'password' => md5($this->p),
            'displayname' => "问卷用户" . $this->u,
            'tel' => "0",
            'status' => "1",
            'role' => "0", // 普通用户
            'addate' => time(),
            'login_times' => 0,
            'last_login_ip' => $_SERVER['REMOTE_ADDR'],
            'last_login_time' => time(),
            //十位整数
        );
        
        if (strtolower($this->v) != $_SESSION['captcha']) {
            $this->json(500, "验证码输入错误");
            die();
        }
        $ulen = mb_strlen($this->u);

        if (!($ulen <= 15 && $ulen >= 8)) {
            $this->json(500, "8位<=用户名<=15位");
            die();
        }
        $plen = mb_strlen($this->p);
        if (!($plen <= 15 && $plen >= 8)) {
            $this->json(500, "8位<=密码<=15位");
            die();
        }
        if (self::$modelObj->rowCount("username='{$data['username']}'")) {
            $this->json(500, $data['username'] . "用户已注册");
            die();
        }
        if (self::$modelObj->insert($data)) {
            $this->json(200, "添加用户" . $data['username'] . '成功');
            die();
        } else {
            $this->json(500, "添加用户" . $data['username'] . '失败');
            die();
        }
    }

    public function delete()
    {
        $this->denyAccess();
        $this->checkId();
        $this->checkUser(self::$modelObj);

        if (self::$modelObj->delete($this->userTable, $this->id)) {
            $this->json(200, "删除" . $this->id . '成功');
        } else {
            $this->json(500, "删除" . $this->id . '失败');
        }
    }

    public function update()
    {
        $this->denyAccess();
        $this->checkId();
        $this->checkUser(self::$modelObj);

        $name = HEADER['Displayname']; // 用户名
        $tel = HEADER['Tel']; // 电话
        $confirmPass = HEADER['Confirmpassword']; // 确认密码

        $data = array(); // 修改数组

        // 校验手机号
        $pattern = '/^[1](([3|5|8][\d])|([4][4,5,6,7,8,9])|([6][2,5,6,7])|([7][^9])|([9][1,8,9]))[\d]{8}$/'; //需要转义/;
        if (isset($tel)) {
            if (preg_match($pattern, $tel)) {
                $data += array('tel' => $tel);
            } else {
                $this->json(500, "手机号码错误");
                die();
            }
        }
        $nameLen = mb_strlen($name);
        if (isset($name)) {
            if (!($nameLen >= 8 && $nameLen <= 15)) {
                $this->json(500, "8位<=用户名<=15位");
                die();
            } else {
                $data += array('displayname' => $name);
            }
        }

        $pLen = mb_strlen($this->p);
        if (isset($this->p)) {
            if (!($pLen >= 8 && $pLen <= 15)) {
                $this->json(500, "8位<=密码<=15位");
                die();
            } else {
                if ($confirmPass !== $this->p) {
                    $this->json(500, "密码不一致");
                    die();
                }
                $data += array('password' => md5($this->p));
            }
        }

        if (self::$modelObj->update($data, $this->id)) {
            $this->json(200, "更新用户" . $data['name'] . '成功');
        } else {
            $this->json(500, "更新用户" . $data['name'] . '失败');
        }

    }

}
