<?php
namespace backend\models;
use yii\base\Model;

class PermissionForm extends Model{
    public $name;//权限
    public $description;//描述
    const SCENARIO_ADD='add';//定义场景
    public function rules()
    {
        return [
            [['name','description'],'required'],
            ['name','ValidateName','on'=>self::SCENARIO_ADD],//权限名称唯一性，不能重复
        ];
    }
    public function attributeLabels()
    {
        return [
          'name'=>'权限（路由）',
            'description'=>'描述',
        ];
    }

    public function ValidateName(){
        $authManager=\Yii::$app->authManager;
        if($authManager->getPermission($this->name)){
            $this->addError('name','权限已存在');
        }
    }
}