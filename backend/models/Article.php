<?php
namespace backend\models;
use yii\db\ActiveRecord;

class Article extends ActiveRecord{
    public static $status_option=[-1=>'删除',0=>'隐藏',3=>'正常'];
    public static function getStatusOptions($hidden=true){
        if($hidden=true){
            unset(self::$status_option[-1]);
        }
        return self::$status_option;
    }
    //建立和分类表之间的关系
    public function getCategory(){
        return $this->hasOne(ArticleCategory::className(),['id'=>'category_id']);
    }
    //定义规则
    public function rules()
    {
        return [
            [['name','intro','category_id','sort','status',],'required'],//必填
            ['sort','integer'],//整数
        ];
    }

    public function attributeLabels()
    {
        return [
            'name'=>'标题',
            'intro'=>'简介',
            'category_id'=>'文章分类',
            'sort'=>'排序',
            'status'=>'状态',
        ];
    }
}