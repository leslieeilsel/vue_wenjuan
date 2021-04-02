<?php
namespace Controller;

use \Frame\Libs\BaseController;
use \Model\UserModel;

final class LoginController extends BaseController
{
    public $v; // 验证码
    public $u; // 用户账户
    public $p; // 用户密码
    public function index()
    {
	
        $this->v = HEADER['Verify'];
        $this->u = HEADER['Username'];
        $this->p = HEADER['Password'];
        if (METHOD === 'POST') { // 登录
            $this->login();
        } elseif (METHOD === 'GET') { // 检查
            $this->checkUsername();
        } elseif (METHOD === 'DELETE') { // 登出
            $this->logout();
        } elseif (METHOD === 'PUT') { // 获取用户登录状态
            $this->loginStatus();
        } 
    }
    public function loginStatus () {
        if($_SESSION['uid']) {
            $this->json(200, $_SESSION['uid']);
        }else {
            $this->json(500, '未登录');
        }
    }
    public function login()
    {
        self::$modelObj = UserModel::getInstance();
        $data = array(
            'username' => $this->u,
            'password' => md5($this->p),
            'verify' => $this->v,
        );

        if (strtolower($data['verify']) != $_SESSION['captcha']) {
            $this->json(500, "验证码输入错误");
        }

        $result = self::$modelObj->fetchOne("username='{$data['username']}' and password ='{$data['password']}'");
        if (!$result) {
            $this->json(500, "请检查账号密码");
            die();
        }
        if ($result['status'] === '0') {
            $this->json(500, "账号被停用，请与管理员联系");
            die();
        }
        $data = array(
            'last_login_ip' => $_SERVER['REMOTE_ADDR'],
            'last_login_time' => time(),
            'login_times' => $result['login_times'] + 1,
        );

        if (!self::$modelObj->update($data, $result['id'])) {
            $this->json(500, "网络异常");
            die();
        };
        $_SESSION['uid'] = $result['id'];
        $_SESSION['uname'] = $result['username'];
        $result['password'] = "";
        $this->json(200, $result);
    }
    public function checkUsername()
    {
        self::$modelObj = UserModel::getInstance();
        $status = self::$modelObj->rowCount("username='{$this->u}'");
        $this->json($status ? 500 : 200, $status ? '占用' : '空闲');
    }
    public function logout()
    {
        $this->denyAccess();
        unset($_SESSION['username']);
        unset($_SESSION['uid']);

        session_destroy();

        setcookie(session_name(), false);
        $this->json(200, '再见');
    }

}
