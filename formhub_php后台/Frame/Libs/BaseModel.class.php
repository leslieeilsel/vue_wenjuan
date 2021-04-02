<?php
namespace Frame\Libs;

use \Frame\Libs\PDOWrapper;

abstract class BaseModel
{
    protected static $modelObj = null;
    protected static $pdo = null;

    public function __construct()
    {

        self::$pdo = PDOWrapper::getInstance();

    }
    public static function getInstance()
    {
        $className = get_called_class();
        if (!self::$modelObj instanceof $className) {
            return self::$modelObj = new $className();
        }
        return self::$modelObj;
    }
    // 获取指定表行数
    public function rowCount($where = false)
    {
        $sql = "SELECT *FROM {$this->table} WHERE {$where}";

        return self::$pdo->rowCount($sql);

    }
    //获取特定表一行数据
    public function fetchOne($where = false)
    {
        //构建查询的SQL语句
        $sql = "SELECT * FROM {$this->table} WHERE $where";
        //执行SQL语句，返回一维数组
        // echo $sql;
        return self::$pdo->fetchOne($sql);
    }
    /*
    功能: 获取特定表一行指定字段数据
    参数:
    $where 字符串 查询条件
    $fields 字符串 查询字段(如: id,name,age)
     */
    public function fetchOneByCond($where = false, $fields = '*')
    {
        //构建查询的SQL语句
        $sql = "SELECT $fields FROM {$this->table} WHERE $where";
        //执行SQL语句，返回一维数组
        return self::$pdo->fetchOne($sql);
    }
    /*
    功能: 获取多行数据
    参数:
    $where 字符串 查询条件
    $orderby 字符串 排序方式(默认按 id 倒叙排列)
     */
    public function fetchAll($where = false, $orderby = ' id desc ')
    {
        //构建查询的SQL语句
        $sql = "SELECT * FROM {$this->table} WHERE $where ORDER BY  $orderby";
        //执行SQL语句，返回二维数组
        return self::$pdo->fetchAll($sql);
    }
    /*
    功能: 获取特定表多行指定字段数据
    参数:
    $where 字符串 查询条件
    $fields 字符串 查询字段(如: id,name,age)
    $orderby 字符串 排序方式(默认按 id 倒叙排列)
     */
    public function fetchAllByCond($where = false, $fields = '*', $orderby = ' id asc ')
    {
        //构建查询的SQL语句
        $sql = "SELECT $fields FROM {$this->table} WHERE $where ORDER BY  $orderby";
        
        //执行SQL语句，返回二维数组
        return self::$pdo->fetchAll($sql);
    }

    //获取多行连表数据
    public function fetchAllWithJoin($data, $where = true, $join, $orderby, $startpage = 0, $pagesize = 20)
    {

        //构建查询的SQL语句
        $sql = "SELECT {$data} FROM {$this->table}";
        foreach ($join as $item) {
            $sql .= " LEFT JOIN {$item['table']} on {$item['leftid']}={$item['rightid']}";
        }
        $sql .= " WHERE $where ";
        $sql .= $orderby ? "ORDER BY $orderby" : "";
        // $sql .= " ORDER BY article.orderby desc,article.id desc ";
        $sql .= " LIMIT $startpage,$pagesize ";
        // echo $sql;
        //执行SQL语句，返回二维数组
        return self::$pdo->fetchAll($sql);
    }

    //删除指定表指定id记录
    public function delete($table, $id)
    {
        //构建删除的SQL语句
        $sql = "UPDATE {$table} SET `status` = '0' WHERE id= {$id}";
        //执行SQL语句，并返回布尔值
        

        return self::$pdo->exec($sql);
    }

    //添加记录
    public function insert($data)
    {
        //构建"字段列表"和"值列表"字符串
        $fields = '';
        $values = '';
        foreach ($data as $key => $value) {
            $fields .= "$key,";
            $values .= "'$value',";
        }
        //去除结尾的逗号
        $fields = rtrim($fields, ',');
        $values = rtrim($values, ',');
        //构建插入的SQL语句：INSERT INTO news(title,content,hits) VALUES('标题','内容','30')
        $sql = "INSERT INTO {$this->table}($fields) VALUES($values)";
        return self::$pdo->exec($sql);
    }
    public function getRecentId($table)
    {
        return self::$pdo->fetchOne("select max(id) from $table;");
    }
    //更新记录
    public function update($data, $id)
    {
        //构建"字段名=字段值"的字符串
        $str = "";
        foreach ($data as $key => $value) {
            $str .= "{$key}='{$value}',";
        }
        //去除结尾的逗号
        $str = rtrim($str, ',');
        //构建更新的SQL语句：UPDATE news SET title='标题',content='内容' WHERE id=5
        $sql = "UPDATE {$this->table} SET {$str} WHERE id={$id}";
        
        //执行SQL语句，并返回布尔值
        return self::$pdo->exec($sql);
    }
    //条件更新
    public function updateByCond($data, $where)
    {
        //构建"字段名=字段值"的字符串
        $str = "";
        foreach ($data as $key => $value) {

            $str .= "{$key}='{$value}',";
        }
        //去除结尾的逗号
        $str = rtrim($str, ',');
        //构建更新的SQL语句：UPDATE news SET title='标题',content='内容' WHERE id=5
        $sql = "UPDATE {$this->table} SET {$str} WHERE $where";
        // echo $sql;
        //执行SQL语句，并返回布尔值
        return self::$pdo->exec($sql);
    }
    // 填报更新人数
    public function insertAnswer($data, $id)
    {

        $sql = "UPDATE {$this->table} SET {$data} WHERE id={$id}";
        // echo $sql;
        //执行SQL语句，并返回布尔值
        return self::$pdo->exec($sql);
    }
}
