<?php
namespace frontend\models;
use yii\db\ActiveRecord;
use yii\helpers\Json;

class Locations extends ActiveRecord{
    //查询城市
    public static function getProvince($id){
        $rows=self::find()->where(['parent_id'=>$id])->asArray()->all();
        return Json::encode($rows);
    }

}