<?php
namespace backend\models;
use yii\db\ActiveRecord;

class ArticleCategory extends ActiveRecord{
    public $imgFile;//保存上传文件
    public static $logo_option=[-1=>'删除',0=>'隐藏',3=>'正常'];
    public static function getStatusOptions($hidden=true){
        if($hidden=true){
            unset(self::$logo_option[-1]);
        }
        return self::$logo_option;
    }
    //定义规则
    public function rules()
    {
        return [
            [['name','intro','sort','status'],'required'],//必填
            ['sort','integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name'=>'分类名称',
            'intro'=>'简介',
            'sort'=>'排序',
            'status'=>'状态',
        ];
    }
}