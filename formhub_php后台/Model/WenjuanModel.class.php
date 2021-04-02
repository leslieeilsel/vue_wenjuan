<?php
namespace Model;

use \Frame\Libs\BaseModel;

final class WenjuanModel extends BaseModel
{
    protected $table = 'fb_wenjuans';

    // 更新问卷
    public function updateW($data, $id)
    {
        //构建"字段名=字段值"的字符串
        $str = "";
        foreach ($data as $key => $value) {

            $str .= "{$key}='{$value}',";
        }
        //去除结尾的逗号
        $str = rtrim($str, ',');
        //构建更新的SQL语句：UPDATE news SET title='标题',content='内容' WHERE id=5
        $sql = "UPDATE {$this->table} SET {$str} WHERE wenjuan_id={$id}";
      //echo $sql;
        //执行SQL语句，并返回布尔值
        return self::$pdo->exec($sql);
    }
    // 更新问卷人数
    public function updatePerson($data, $id)
    {
        //构建更新的SQL语句：UPDATE news SET title='标题',content='内容' WHERE id=5
        $sql = "UPDATE {$this->table} SET {$data} WHERE wenjuan_id='{$id}'";
      //echo $sql;
        //执行SQL语句，并返回布尔值
        return self::$pdo->exec($sql);
    }
    //删除问卷记录
    public function deleteW($table, $id)
    {
        //构建删除的SQL语句
        $sql = "UPDATE {$table} SET `enabled` = '0' WHERE wenjuan_id= '{$id}'";
        //执行SQL语句，并返回布尔值
      // echo $sql;
        return self::$pdo->exec($sql);
    }

}
