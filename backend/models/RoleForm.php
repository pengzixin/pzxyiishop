<?php
namespace backend\models;
use yii\base\Model;

class RoleForm extends Model{
    public $name;//角色名
    public $description;//描述
    public $permissions=[];//权限

    const SCENARIO_ADD='add';

    public function rules()
    {
        return [
            [['name','description'],'required'],
            ['permissions','safe'],
            ['name','ValidateName','on'=>self::SCENARIO_ADD]
        ];
    }
    public function attributeLabels()
    {
        return [
            'name'=>'角色',
            'description'=>'描述',
            'permissions'=>'权限',
        ];
    }

    public function ValidateName(){
        $authManager=\Yii::$app->authManager;
        if($authManager->getRole($this->name)){
            $this->addError('name','角色已存在');
        }
    }
}