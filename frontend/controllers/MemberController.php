<?php

namespace frontend\controllers;

use frontend\models\Address;
use frontend\models\Locations;
use frontend\models\LoginForm;
use frontend\models\Member;
use yii\captcha\CaptchaAction;
use yii\web\NotFoundHttpException;
use yii\web\Request;

class MemberController extends \yii\web\Controller
{
    public $layout=false;
    //==================注册结束=============================
    public function actionRegister(){
        //实例化模型
        $model = new Member();
        //加载数据
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
                $model->save(false);
            \yii::$app->session->setFlash('success','注册成功');
            return $this->redirect(['member/index']);
        }
        //调用视图
        return $this->render('register',['model'=>$model]);
    }
//=====================注册开始======================================


    public function actionIndex()
    {
        echo '这是首页';
    }

    //========================登录开始================================
    public function actionLogin(){
        $model= new LoginForm();
        //$user = new Member();
        //加载数据
        if($model->load(\Yii::$app->request->post())){
            //验证数据
            if( $model->validate() && $model->login()){
                //var_dump($model);exit;
                \yii::$app->session->setFlash('success','登陆成功');
                return $this->redirect(['member/index']);
            }else{
                //print_r($model->getErrors());exit;
            }
        }
        return $this->render('login',['model'=>$model]);
    }
    //=================================登录结束==========================

    //==================================添加增删改查开始==========================
        //添加地址
    public function actionAddress(){
        //实例化模型
        $model = new Address();
        $user_id=\Yii::$app->user->identity->id;
        $address =$model->find()->where(['user_id'=>$user_id])->all();
        $request = new Request();
        //判断提条方式
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            if( $model->validate()){
                $model->save();
                //var_dump($model);exit;
                return $this->redirect(['member/address']);
            }else{
                print_r($model->getErrors());exit;
            }
        }
        //调用视图，分配数据
        return $this->render('address',['model'=>$model,'address'=>$address]);
    }
        //删除地址
    public function actionDelAddress($id){
        $model=Address::findOne(['id'=>$id]);
        if($model==null){
            throw new NotFoundHttpException('地址不存在');
        }
        $model->delete();
        return $this->redirect(['member/address']);
    }

    //修改地址
    public function actionEditAddress($id){
        //实例化模型
        $model = new Address();
        $user_id=\Yii::$app->user->identity->id;
        $address =$model->find()->where(['user_id'=>$user_id])->all();
        $model=Address::findOne(['id'=>$id]);
        $request = new Request();
        //判断提条方式
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            if( $model->validate()){
                $model->save();
                return $this->redirect(['member/address']);
            }else{//验证失败，打印错误信息
                print_r($model->getErrors());exit;
            }
        }
        //调用视图，分配数据
        return $this->render('address',['model'=>$model,'address'=>$address]);
    }

    //设置默认地址
    public function actionChgStatus($id){
        $model=Address::findOne(['id'=>$id]);
        if($model->status==0){
            $model->status=1;
        }
        $model->update(false,['status']);
        return $this->redirect(['member/address']);
    }

    //得到三级联动城市
    public function actionLocations($id){
            $model=new Locations();
            return $model->getProvince($id);
    }
    //=============================地址管理结束===============================

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
}
