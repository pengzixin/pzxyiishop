<?php
namespace frontend\models;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class GoodsCategory extends ActiveRecord{
    public function getChildren(){
        return $this->hasMany(self::className(),['parent_id'=>'id']);
    }

    //根据父ID查询所有分类Id
    public static function getId($pid){
       $category=self::find()->select(['lft','rgt','tree'])->where(['id'=>$pid])->one();
        //var_dump($category);exit;
        $ids=self::find()->select(['id'])
            ->andwhere([">=",'lft',"$category->lft"])
            ->andWhere(["<=",'rgt',"$category->rgt"])
            ->andWhere(['tree'=>$category->tree])
            ->asArray()->all();
        $ida=ArrayHelper::map($ids,'id','id');
        //print_r($ida);exit;
        return $ida;
    }
}