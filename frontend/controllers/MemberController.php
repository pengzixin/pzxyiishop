<?php

namespace frontend\controllers;

use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Locations;
use frontend\models\LoginForm;
use frontend\models\Member;
use frontend\models\Order;
use yii\captcha\CaptchaAction;
use yii\filters\AccessControl;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;
use yii\web\Request;

class MemberController extends \yii\web\Controller
{
    public $layout=false;
    //==================注册开始=============================
    public function actionRegister(){
        //实例化模型
        $model = new Member();
        //加载数据
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
                $model->save(false);
            \yii::$app->session->setFlash('success','注册成功');
            return $this->redirect(['member/login']);
        }
        //调用视图
        return $this->render('register',['model'=>$model]);
    }
//=====================注册结束======================================


    //========================登录开始================================
    public function actionLogin(){
        $model= new LoginForm();
        //$user = new Member();
        //加载数据
        if($model->load(\Yii::$app->request->post())){
            //验证数据
            if( $model->validate() && $model->login()){
                //登陆成功，取出用户Id
                $member_id=\Yii::$app->user->identity->id;
                //q取出cookie中的购物信息
                $cookies=\Yii::$app->request->cookies;
                $carts=$cookies->get('cart');
                if($carts){//如果存在购物信息
                    $carts=unserialize($carts);
                    foreach(array_keys($carts) as $goods_id){//遍历键值，得到商品id
                        $model=Cart::find()->andWhere(['goods_id'=>$goods_id])
                            ->andWhere(['member_id'=>$member_id])
                            ->one();
                        if($model){
                            $model->amount+=$carts[$goods_id];
                            $model->save();
                        }else{
                            $model=new Cart();
                            $model->goods_id=$goods_id;
                            $model->amount=$carts[$goods_id];
                            $model->member_id=$member_id;
                            $model->save();
                        }
                    }
                   \Yii::$app->response->cookies->remove('cart');
                }

                //var_dump($model);exit;
                \yii::$app->session->setFlash('success','登陆成功');
                return $this->redirect(['goods/index']);
            }else{
                //print_r($model->getErrors());exit;
            }
        }
        return $this->render('login',['model'=>$model]);
    }

    //注销，退出登录
    public function actionLogout(){
        \Yii::$app->user->logout();
        return $this->redirect(['goods/index']);
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

    //测试短信发送
    public function actionSms($tel)
    {

        $redis= new \Redis();
        $redis->connect('127.0.0.1');
        //=============防止短信被盗刷====================》
        //1、限制每个手机每天只能发送5条
        //获取redis中保存的手机发送次数
        $times=$redis->get('times_'.$tel.'_'.date('Ymd'));
        $res=['status'=>0,'msg'=>''];
        if($times && $times>=1){//判断存在该手机次数，并且次数大于3
            $res['msg']='次数上限，明天再试';
            return json_encode($res);
        }
        if($redis->get('code_'.$tel)){
            $res['msg']="'请等待'.$redis->ttl('code_'.$tel).'秒后，再试'";
            return json_encode($res);
        }

        $dcode=rand(1000,9999);
        //$tel=18228185755;
        \Yii::$app->sms->setPhoneNumbers($tel)->setTemplateParam(['code'=>$dcode])->send();
        //将短信验证码保存到session。
        $redis->set('code_'.$tel,$dcode,60);
        $times=$redis->incr('times_'.$tel.'_'.date('Ymd'));//记录每个手机发送的次数
        $res['status']=1;
        return json_encode($res);
    }

    //============================订单展示页面开始==============================》
    public function actionOrderList(){
        //根据用户ID查询出所有订单
        $member_id=\Yii::$app->user->identity->id;
        //var_dump($member_id);exit;
        $orders=Order::find()->where(['member_id'=>$member_id])->all();
        //调用视图，分配数据
        return $this->render('order-list',['orders'=>$orders]);
    }
    //============================订单展示页面结束==============================》

    public function behaviors()
    {
        return [
            'ACF'=>[
                'class'=>AccessControl::className(),
                'only'=>['order-list','chg-status','edit-address','del-address','address'],//哪些操作需要使用该过滤器
                'rules'=>[
                    [
                        'allow'=>true,//是否允许
                        'actions'=>['order-list','chg-status','edit-address','del-address','address'],//指定操作
                        'roles'=>['@'],//指定角色 ?表示未认证用户(未登录) @表示已认证用户(已登录)
                    ],
                ]
            ]
        ];
    }
}
