<?php

namespace Frame\Libs;

use \PDO;

final class PDOWrapper extends \PDO
{
    private static $pdo = null;
    private static $PDOWrapper = null;
    private $db_type;
    private $db_host;
    private $db_name;
    private $db_port;
    private $db_user;
    private $db_pass;
    private $charset;

    public function __construct()
    {
        $this->db_type = $GLOBALS['config']['db_type'];
        $this->db_host = $GLOBALS['config']['db_host'];
        $this->db_name = $GLOBALS['config']['db_name'];
        $this->db_port = $GLOBALS['config']['db_port'];
        $this->db_user = $GLOBALS['config']['db_user'];
        $this->db_pass = $GLOBALS['config']['db_pass'];
        $this->charset = $GLOBALS['config']['charset'];

        $this->createDb();
        $this->setErrMode();
    }
    public static function getInstance()
    {
        if (!self::$pdo instanceof \PDO) {
            return self::$PDOWrapper = new self();
        }
        return self::$PDOWrapper;
    }
    // 创建数据库
    private function createDb()
    {
        try {
            $dbh = "{$this->db_type}:host={$this->db_host};dbname={$this->db_name};port:{$this->db_port};charset={$this->charset}";
            self::$pdo = new PDO($dbh, $this->db_user, $this->db_pass);
        } catch (\Exception $e) {
            echo "创建数据库对象失败";
        }
    }
    // 设置数据库错误
    private function setErrMode()
    {
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    // 返回数据库错误信息
    private function showErrMsg($e)
    {
        $this->json(500, "参数错误,获取失败");

    }
    //
    private function json($code, $message)
    {
        /*
        $code 200 成功 500 失败
         */
        header('Content-Type:application/json; charset=utf-8');

        $arr = array('code' => $code, 'msg' => $message);

        exit(json_encode($arr));

    }
    // 获取1行
    public function fetchOne($sql)
    {
        try {
            $sth = self::$pdo->prepare($sql);
            $sth->execute();
            return $sth->fetch();
        } catch (\PDOException $e) {
            $this->showErrMsg($e);
        }
    }
    // 获取全部
    public function fetchAll($sql)
    {
        try {
            $sth = self::$pdo->prepare($sql);
            $sth->execute();
            return $sth->fetchAll();
        } catch (\PDOException $e) {
            $this->showErrMsg($e);
        }
    }
    // 执行sql语句
    public function exec($sql)
    {
        try {
            return self::$pdo->exec($sql);
        } catch (\PDOException $e) {
            $this->showErrMsg($e);
        }
    }
    // 获取查询结果行数
    public function rowCount($sql)
    {
        try {
            $sth = self::$pdo->prepare($sql);
            $sth->execute();
            return $sth->rowCount();
        } catch (\PDOException $e) {
            $this->showErrMsg($e);
        }
    }
}
