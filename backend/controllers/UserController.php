<?php
namespace backend\controllers;
use backend\filters\RbacFilter;
use backend\models\LoginForm;
use backend\models\User;
use yii\captcha\CaptchaAction;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;

class UserController extends Controller{
    //展示页面
    public function actionIndex(){
        $users=User::find()->all();
        return $this->render('index',['users'=>$users]);
    }
//添加，注册
    public function actionAdd(){
        //实例化
        $model=new User(['scenario'=>User::SCENARIO_ADD]);
        $request = new Request();
        //判断提交方式
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            //验证数据
            if($model->validate()){
                //验证通过
                $model->save(false);
                //实例化组件authManager
              $authManager = \Yii::$app->authManager;
                if(is_array($model->roles)){
                    foreach($model->roles as $roleName){
                        //得到角色对象
                        $role=$authManager->getRole($roleName);
                        //给用户添加角色
                        if($role)$authManager->assign($role,$model->id);
                    }
                }
                //跳转页面
                \yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['user/index']);
            }else{
                //验证失败
                var_dump($model->getErrors());exit;
            }
        }
        //调用视图，分配数据
        return $this->render('add',['model'=>$model]);
    }
    //修改
    public function actionEdit($id){
        $model=User::findOne(['id'=>$id]);
        $model->password='';
        $request = new Request();
        //实例化组件authManager
        $authManager = \Yii::$app->authManager;
        //得到角色，回显数据
        $roles=$authManager->getRolesByUser($id);
        //var_dump($role);exit;
        $model->roles=ArrayHelper::map($roles,'name','name');
        //判断提交方式
        if($request->isPost){
            //取消所有角色关联
            if(is_array($roles)){
                $authManager->revokeAll($id);
            }
            //加载数据
            $model->load($request->post());
            //var_dump($model);exit;
            //验证数据
            if($model->validate()){//验证通过
                $model->save(false);
                if(is_array($model->roles)){
                    foreach($model->roles as $roleName){
                        //得到角色对象
                        $role=$authManager->getRole($roleName);
                        //给用户添加角色
                        if($role)$authManager->assign($role,$model->id);
                    }
                }
                //跳转页面
                \yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['user/index']);
            }else{
                //验证失败
                var_dump($model->getErrors());exit;
            }
        }
        //调用视图，分配数据
        return $this->render('add',['model'=>$model]);
    }
    //删除
    public function actionDelete($id){
        $model=User::findOne(['id'=>$id]);
        //实例化组件authManager
        $authManager = \Yii::$app->authManager;
        $authManager->revokeAll($id);
        $model->delete();
        //跳转
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['user/index']);
    }

    //登录功能
    public function actionLogin(){
        //实例化对象
        $model=new LoginForm();
        $request = new Request();
        //判断提交方式
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            //验证数据
            if($model->validate() && $model->login()){
                //var_dump($_SERVER);exit;
                //验证成功
                //var_dump($ip);exit;
                \yii::$app->session->setFlash('success','登陆成功');
                return $this->redirect(['user/index']);
            }
        }
        //调用视图，分配数据
        return $this->render('login',['model'=>$model]);
    }

    //注销,退出登录
    public function actionLogout()
    {
        \Yii::$app->user->logout();
        return $this->redirect(['user/index']);
    }

    //修改自己密码功能
    public function actionChPwd(){
        //判断是否登录
        if(\Yii::$app->user->isGuest){//未登录，跳转到登录页面
            return $this->redirect(['user/login']);
        }
        $user_id= \Yii::$app->user->identity->getId();
        $model=User::findOne(['id'=>$user_id]);
        $model->scenario=User::SCENARIO_CHPWD;
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //验证旧密码是否正确
            if(\Yii::$app->security->validatePassword($model->oldpassword,$model->password_hash) && $model->newpssword!=$model->oldpassword){
                //加密密码
                $model->password_hash=\Yii::$app->security->generatePasswordHash($model->newpssword);
                $model->save();
                \Yii::$app->session->setFlash('success','密码修改成功');
                \Yii::$app->user->logout();
                return $this->redirect(['user/login']);
            }elseif(\Yii::$app->security->validatePassword($model->oldpassword,$model->password_hash) && $model->newpssword==$model->oldpassword){
                $model->addError('newpssword','新密码不能和旧密码一样');
            }else{
                $model->addError('oldpassword','旧密码输入错误');
            }
        }
        //调用视图
        return $this->render('change',['model'=>$model]);
        //var_dump($user_id);exit;
    }

    //定义验证码操作
    public function actions()
    {
        return [
            'captcha'=>[
                'class'=>CaptchaAction::className(),
                //验证码的长度
                'minLength'=>4,
                'maxLength'=>4,
            ]
        ];
    }
    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
                'except'=>['login','logout','captcha'],
            ]
        ];
    }
}