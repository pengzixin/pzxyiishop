<?php
namespace backend\models;
use yii\db\ActiveRecord;

class GoodsGallery extends ActiveRecord{
    public function rules()
    {
        return [
            [['goods_id'], 'integer'],
            [['path'], 'required'],
            [['path'], 'string', 'max' => 255],
        ];
    }
}