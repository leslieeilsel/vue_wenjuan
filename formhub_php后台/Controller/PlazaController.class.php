<?php
namespace Controller;

use \Frame\Libs\BaseController;
use \Model\WenjuanModel;
use \Model\UserModel;

final class PlazaController extends BaseController
{
    public $id;
    public function index()
    {

        $startpage = HEADER['Startpage'] ? HEADER['Startpage'] : 0;
        $pagesize = HEADER['Pagesize'] ? HEADER['Pagesize'] : 20;
        self::$modelObj = WenjuanModel::getInstance();
        $userModelObj = UserModel::getInstance();
        $leftTable = 'fb_wenjuans';
        $rightTable = 'fb_users';
        $data = ['mustLogin','wenjuan_id', $leftTable.'.status','title', $rightTable . '.username', 'user_id', 'timeCreated', 'timeEnd','intro', 'personCount', 'totalPerson'];
        if (ID) {
            $this->id = ID;
            $this->checkUser($userModelObj, false);
        }
        
        $where = " $leftTable.enabled=1 and $leftTable.status=1 and $leftTable.code=0 and $rightTable.status=1 ";
        if (ID) {
            array_push($data, 'code');
            $where = " $leftTable.enabled=1 and {$leftTable}.user_id=" . (string) ID;
        }
       
        $join = array(
            array(
                'table' => $rightTable, 'leftid' => "$leftTable.user_id",
                'rightid' => "$rightTable.id",
            ),
        );
        $orderby = "";
        $timeCreated = HEADER['Createtime']; //  desc asc

        if ($timeCreated) {
            $orderby .= "{$leftTable}.timeCreated {$timeCreated},";
        }
        $endTime = HEADER['Endtime'];

        if ($endTime) {
            $orderby .= "{$leftTable}.endTime {$endTime},";
        }

        $personCount = HEADER['Personcount'];
        if ($personCount) {
            $orderby .= "{$leftTable}.personCount {$personCount},";
        }

        $totalPerson = HEADER['Totalperson'];
        if ($totalPerson) {
            $orderby .= "{$leftTable}.totalPerson {$totalPerson},";
        }

        $expectPerson = HEADER['Expectperson'];

        if ($expectPerson) {
            $orderby .= "(totalPerson-personCount) {$expectPerson},";
        }

        $arrs = self::$modelObj->fetchAllWithJoin(implode(",", $data), $where, $join, substr($orderby, 0, -1), $startpage, $pagesize);

        $this->json(200, $arrs);
    }
}
