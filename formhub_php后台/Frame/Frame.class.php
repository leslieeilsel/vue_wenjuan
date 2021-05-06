<?php
namespace Frame;

use Exception;

final class Frame
{
    public static function run()
    {
        #echo "frame";
        //初始化默认参数
        self::initConfig();
        
        //初始化默认路由
        self::initRoute();
        //初始化默认常量
        self::initConst();
        //初始化自动加载

        self::initAutoLoad();
        //初始化自动分发
        #echo 'patch';
        self::initDispatch();

    }
    private static function initConfig()
    {
        session_start();
        $GLOBALS['config'] = require_once ROOT_PATH . DS . 'Conf' . DS . 'Config.php';
    }
    private static function initRoute()
    {

        $reqArr = (explode('/', $_SERVER['REQUEST_URI']));
        $len = count($reqArr);

        if ($len >= 3 || $reqArr[$len - 2] === "id") {
            $id = $reqArr[$len - 1];
        } else {
            $id = "";
        }

        $c = isset($reqArr[1]) ? $reqArr[1] : $GLOBALS['config']['default_Controller'];
        $a = (isset($reqArr[2]) && $reqArr[2] != 'id') ? $_GET['a'] : $GLOBALS['config']['default_action'];

        define('ID', $id);
        define('CONTROLLER', $c);
        define('ACTION', $a);
        define('METHOD', $_SERVER['REQUEST_METHOD']);
        define('HEADER', getallheaders());
    }
    private static function initConst()
    {
        define('MODEL_PATH', ROOT_PATH . 'Model' . DS);
        define('CONTROLLER_PATH', ROOT_PATH . 'Controller' . DS);
    }
    private static function initAutoLoad()
    {
        spl_autoload_register(function ($className) {
            //必须加载绝对路径

            $fileName = ROOT_PATH . str_replace('\\', DS, ($className)) . '.class.php';
            // echo "==";
            // echo $fileName;

            if (file_exists($fileName)) {
                require_once ($fileName);
                #echo '有';
            }
        });
    }
    private static function initDispatch()
    {

        try {
            $ControllerName = '\\' . 'Controller' . '\\' . ucfirst(CONTROLLER) . 'Controller';
            #echo "控制器";
            
            $a = ACTION;
            ##echo $a;
            $ControllerObj = $ControllerName::getInstance();
           
            $ControllerObj->$a();
        } catch (Exception $e) { // 错误页面处理
            $ControllerName = '\\' . 'Controller' . '\\' . 'notFound' . 'Controller';
            $a = ACTION;
            $ControllerObj = $ControllerName::getInstance();
            $ControllerObj->$a();
        }

    }
}
