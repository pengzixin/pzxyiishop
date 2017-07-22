<?php
namespace backend\models;
use creocoder\nestedsets\NestedSetsQueryBehavior;
use yii\db\ActiveQuery;

class GoodsCategoryQuery extends ActiveQuery{
    public function behaviors()
    {
        return [
            NestedSetsQueryBehavior::className()
        ];
    }
}