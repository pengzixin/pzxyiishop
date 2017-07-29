<?php
namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\PermissionForm;
use backend\models\RoleForm;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class RbacController extends Controller{
    //添加权限
    public function actionAddPermission(){
        //实例化表单模型
        $model = new PermissionForm();
        $model->scenario=PermissionForm::SCENARIO_ADD;//规定场景
        //加载数据，验证数据
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //实例化authManager
            $authManager= \Yii::$app->authManager;
            //创建权限
            $permission=$authManager->createPermission($model->name);
            $permission->description=$model->description;
            //保存到数据表
            $authManager->add($permission);
            //添加成功跳转到列表页面
            \Yii::$app->session->setFlash('success','添加成功');
            return $this->redirect(['rbac/permission-index']);
        }
        //调用视图，分配数据
        return $this->render('add-permission',['model'=>$model]);
    }

    //列表页面
    public function actionPermissionIndex(){
        //实例化authManager组件
        $authManager= \Yii::$app->authManager;
        //获取所有的权限
        $permissions=$authManager->getPermissions();
        //调用视图，分配数据
        return $this->render('permission-index',['permissions'=>$permissions]);
    }
    //修改权限
    public function actionEditPermission($name){
        //实例化authManager组件
        $authManager = \Yii::$app->authManager;
        //检权限是否存在
        $permission=$authManager->getPermission($name);
        if($permission==null){
            throw new NotFoundHttpException('权限不存在');
        }
        $model = new PermissionForm();
        //判断提交方式
        if(\Yii::$app->request->isPost){
            //加载数据，验证数据
            if($model->load(\Yii::$app->request->post()) && $model->validate()){
                $permission->name=$model->name;
                $permission->description=$model->description;
                //更新保存到数据表
                $authManager->update($name,$permission);
                //修改成功跳转到列表页
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['rbac/permission-index']);
            }
        }else{
            //回显数据到表单
            $model->name=$permission->name;
            $model->description=$permission->description;
        }
        //调用视图
        return $this->render('add-permission',['model'=>$model]);
    }

    //删除权限
    public function actionDelPermission($name){
        //实例化authManager
        $authManager = \Yii::$app->authManager;
        //判断是否存在权限
        $permission=$authManager->getPermission($name);
        if($permission){
           $authManager->remove($permission);
        }
        return $this->redirect(['rbac/permission-index']);
    }

    //=====================角色管理======================================》

    //角色添加
    public function actionAddRole(){
        //实例化表单模型
        $model = new RoleForm();
        $model->scenario=RoleForm::SCENARIO_ADD;//指定场景
        //加载验证数据
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //实例化authManager组件
            $authManager = \Yii::$app->authManager;
            //创建角色
            $role=$authManager->createRole($model->name);
            $role->description=$model->description;
            $authManager->add($role);
            //给角色添加权限
            if(is_array($model->permissions)){
                foreach($model->permissions as $permissionName){
                    //得到权限对象
                    $permission=$authManager->getPermission($permissionName);
                    //给角色添加权限
                    if($permission)$authManager->addChild($role,$permission);
                }
            }
            //添加成功，返回列表页
            \Yii::$app->session->setFlash('success','添加角色成功');
            return $this->redirect(['rbac/role-index']);
        }

        //调用视图，分配数据
        return $this->render('add-role',['model'=>$model]);
    }

    //角色列表页面
    public function actionRoleIndex(){
        //实例化authManager组件
        $authManager = \Yii::$app->authManager;
        //得到所有的角色
        $roles= $authManager->getRoles();
        //调用视图，分配数据
        return $this->render('role-index',['roles'=>$roles]);
    }

    //角色修改
    public function actionEditRole($name){
        //实例化表单模型
        $model = new RoleForm();
        //得到当前角色
        $authManager = \Yii::$app->authManager;
        $role=$authManager->getRole($name);
        //判断提交方式
        if(\Yii::$app->request->isPost){
            //清除所有角色关联权限
            $authManager->removeChildren($role);
            //加载验证数据
            if($model->load(\Yii::$app->request->post()) && $model->validate()){
                //创建角色
                $role=$authManager->createRole($model->name);
                $role->description=$model->description;
                $authManager->update($name,$role);
                //给角色添加权限
                if(is_array($model->permissions)){
                    foreach($model->permissions as $permissionName){
                        //得到权限对象
                        $permission=$authManager->getPermission($permissionName);
                        //给角色添加权限
                        if($permission)$authManager->addChild($role,$permission);
                    }
                }
                //添加成功，返回列表页
                \Yii::$app->session->setFlash('success','修改角色成功');
                return $this->redirect(['rbac/role-index']);
            }
        }
        //数据回显赋值
        $permissions= $authManager->getPermissionsByRole($name);
        $model->name=$role->name;
        $model->description=$role->description;
        $model->permissions=ArrayHelper::map($permissions,'name','name');
        //调用视图,回显数据
        return $this->render('add-role',['model'=>$model]);
    }

    //角色删除
    public function actionDelRole($name){
        //实例化authManager
        $authManager = \Yii::$app->authManager;
        //判断是否存在权限
        $role=$authManager->getRole($name);
        if($role){
            $authManager->remove($role);
        }
        return $this->redirect(['rbac/role-index']);
    }

    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
                'only'=>['add','edit','delete','index']
            ]
        ];
    }
}