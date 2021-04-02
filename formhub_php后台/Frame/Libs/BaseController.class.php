<?php
namespace Frame\Libs;

abstract class BaseController
{

    protected static $controllerObj = null;
    protected static $modelObj = null;
    protected static $questionModelObj = null;
    protected static $optionModelObj = null;
    protected static $userModelObj  =null;
    protected $v; // 验证码
    protected $u; // 用户账户
    protected $p; // 用户密码
    protected $id; // 用户id

    public function __construct()
    {}
    public static function getInstance()
    {
        $className = get_called_class();
        if (!self::$controllerObj instanceof $className) {

            return self::$controllerObj = new $className();
        }
        return self::$controllerObj;
    }
    // 检查 /xxx/id/xxx 传入参数是否为数字
    protected function checkId()
    {
        if (!isset($this->id)) {
            $this->json(500, 'id输入错误');
            die();
        }
    }
    // 提取body 返回关联数组
    protected function getBody()
    {
        $str = urldecode(@file_get_contents('php://input'));
        $arr = explode('&', $str); //转成数组
        $res = array();
        foreach ($arr as $k => $v) {
            $arr = explode('=', $v);
            $res[$arr[0]] = $arr[1];
        }
        return $res;
    }
    // 修改控制 只有role = 1 管理员 或 当前用户才能 修改当前用户信息
    protected function checkUser($userModel, $model, $qo = false)
    {
        $uid = $_SESSION['uid'];
        $user = $userModel->fetchOne(" id = $uid");
        $role = $user['role'];// 是否为管理员
        if($model==false) {  // 广场校验
            $user_id = $this->id;
        }else if($qo) {
            $wenjuan = $model->fetchOne(" id = '$this->id'");
            
            $user_id = $wenjuan['user_id'];
        }else {
            $wenjuan = $model->fetchOne(" wenjuan_id = '$this->id'");
            $user_id = $wenjuan['user_id'];
        }
        // $this->json(500, $wenjuan);
        if (!($role === '1' || $user_id == $uid)) {
            $this->json(500, '权限不足');
            die();
        }
        
    }
    // 301跳转
    protected function jump($message, $url = "?", $timeout = 2)
    {

        echo "
		<h1 style='text-align:center'>{$message}</h1>
		";

        header("Refresh:$timeout;Url=$url");
        exit();
    }
    // json返回
    protected function json($code, $message)
    {
        /*
        $code 200 成功 500 失败
         */
        header('Content-Type:application/json; charset=utf-8');

        $arr = array('code' => $code, 'msg' => $message);

        exit(json_encode($arr));

    }
    // session访问控制
    protected function denyAccess()
    {
        if (empty($_SESSION['uid'])) {
            $this->json(500, '未登录,无法访问');
        }
    }
}
