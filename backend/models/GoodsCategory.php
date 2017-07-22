<?php
namespace backend\models;
use creocoder\nestedsets\NestedSetsBehavior;
use yii\db\ActiveRecord;
use Yii;

class GoodsCategory extends ActiveRecord{

    public static function tableName(){
        return 'goods_category';
    }
    //定义规则
    public function rules()
    {
        return [
            [['name','parent_id'],'required'],
            [['tree', 'lft', 'rgt', 'depth', 'parent_id'], 'integer'],
            [['intro'], 'string'],
            [['name'], 'string', 'max' => 50],
        ];
    }
    public function attributeLabels()
    {
        return [
            'name'=>'分类名称',
            'parent_id'=>'上级分类',
            'intro'=>'简介',
        ];
    }

    //集合嵌套行为
    public function behaviors()
    {
        return [
            'tree' => [
                'class' =>NestedSetsBehavior::className(),
                'treeAttribute' => 'tree',
                'leftAttribute' => 'lft',
                'rightAttribute' => 'rgt',
                'depthAttribute' => 'depth',
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new GoodsCategoryQuery(get_called_class());
    }

    public static function getParent($parent_id){
        $model=self::findOne(['id'=>$parent_id]);
        if(!$model){
            $model=(object)null;
            $model->name='顶级分类';
        }
        return $model;
    }

    public static function exceptionInfo($msg){
        $infos = [
            'Can not move a node when the target node is same.'=>'不能修改到自己分类下面',
            'Can not move a node when the target node is child.'=>'不能修改到自己的子孙分类下面',
        ];
        return isset($infos[$msg])?$infos[$msg]:$msg;
    }
}