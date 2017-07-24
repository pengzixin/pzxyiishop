<?php
namespace backend\models;
use yii\db\ActiveRecord;

class GoodsIntro extends ActiveRecord{
    public function rules()
    {
        return [
            ['content','required']  //必填
        ];
    }

    public function attributeLabels()
    {
        return [
            'content'=>'商品详情',
        ];
    }
}