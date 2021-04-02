<?php
//动态分隔符
define('DS', DIRECTORY_SEPARATOR);
//根路径常量
define('ROOT_PATH', getcwd() . DS);
//框架路径
define('FRAME_PATH', ROOT_PATH . 'Frame' . DS);
//引入框架类
require_once FRAME_PATH . 'Frame.class.php';
//调用框架静态方法初始化框架
\Frame\Frame::run();
