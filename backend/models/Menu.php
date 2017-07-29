<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "menu".
 *
 * @property integer $id
 * @property string $label
 * @property string $url
 * @property integer $parent_id
 * @property integer $sort
 */
class Menu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['label','parent_id','sort'],'required'],
            [['parent_id', 'sort'], 'integer'],
            [['label', 'url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'label' => '菜单名称',
            'url' => '路由',
            'parent_id' => '上级菜单',
            'sort' => '排序',
        ];
    }

    //获取菜单，回显到下拉框
    public static function getLableOption(){
        $label1= ArrayHelper::map(self::find()->where(['=','parent_id',0])->asArray()->all(),'id','label');
        $label0=[0=>'顶级菜单'];
        $labels=ArrayHelper::merge($label0,$label1);
        return $labels;
    }
    public static function getUrl(){
        //实例化authManager组件
        $authManager=\Yii::$app->authManager;
        $url=$authManager->getPermissions();
        return $url;
    }

    public static function getLabelName($id){
        return self::find()->select('label')->where(['id'=>$id])->one();
    }
    //查询下级分类
    public static function getChildren($id){
        return self::find()->where(['parent_id'=>$id])->all();
    }
}
