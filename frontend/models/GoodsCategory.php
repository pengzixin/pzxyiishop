<?php
namespace frontend\models;
use yii\db\ActiveRecord;

class GoodsCategory extends ActiveRecord{
    public function getChildren(){
        return $this->hasMany(self::className(),['parent_id'=>'id']);
    }

}