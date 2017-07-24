<?php
namespace backend\models;
use yii\db\ActiveRecord;

class GoodsDayCount extends ActiveRecord{
    public static function getSn(){
        $date=date('Ymd');
        $count=self::find()->where(['day'=>$date])->one();
        //var_dump($count);exit;
        if($count){//已经有数据了，就自增一
            $count->count+=1;
            $count->save();
        }else{
            $count=new self;
            $count->day=$date;
            $count->count=1;
            $count->save();
        }
        return $date.str_pad($count->count,3,"0",STR_PAD_LEFT);
    }
}