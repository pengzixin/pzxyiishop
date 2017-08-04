<?php
namespace frontend\controllers;
use frontend\models\Cart;
use frontend\models\Goods;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Cookie;

class CartController extends Controller{
    public $enableCsrfValidation=false;
    //添加到购物车页面
    public function actionAddCart($goods_id,$amount){
        //判断是否登录
        if(\Yii::$app->user->isGuest){//未登录存放在cookie中
            //先取出cookie中的购物车商品
            $cookies=\Yii::$app->request->cookies;//(读取信息request里面的cookie）
            $cart=$cookies->get('cart');
            //判断是否有数据
            if($cart==null){//无数据，将新数据加入
                $carts=[$goods_id=>$amount];
            }else{//有数据，分两种情况：1已有该商品，没有该商品，有其他商品
                //序列化数据,取出商品数量
                $carts=unserialize($cart->value);
                if(isset($carts[$goods_id])){//存在该商品
                    $carts[$goods_id]+=$amount;
                }else{//不存在该商品
                    $carts[$goods_id]=$amount;
                }
            }
            //将商品信息存入到cookie
            ///实例化cookie组件（写入cookie用response里面的cookie）
            $cookies=\Yii::$app->response->cookies;
            //实例化cookie
            $cookie=new Cookie([
                'name'=>'cart',//cookie名
                'value'=>serialize($carts) ,//cookie值
                'expire'=>1*24*3600+time(),//设置过期时间
            ]);
            $cookies->add($cookie);//将数据保存到cookie
        }else{//已经登录
            $member_id=\Yii::$app->user->identity->id;
            //实例化模型
            $model=Cart::find()->andWhere(['member_id'=>$member_id])
                ->andWhere(['goods_id'=>$goods_id])
                ->one();
            //var_dump($model);exit;
            if($model){
                $model->amount+=$amount;
            }else{
                $model = new Cart();
                $model->goods_id=$goods_id;
                $model->amount=$amount;
                $model->member_id=$member_id;
            }
            if($model->validate()){
                $model->save();
            }else{
                var_dump($model->getErrors());exit;
            }
        }
        return $this->redirect(['cart']);
    }

    //购物车页面
    public function actionCart(){
        $cookies = \Yii::$app->request->cookies;
        $this->layout=false;
        //判断是否登录
        if(\Yii::$app->user->isGuest){//未登录，购物车数据从cookie中取出
            //实例化组件
            $cookies=\Yii::$app->request->cookies;
            $cart=$cookies->get('cart');
            if($cart==null){
                $carts=[];
            }else{
                $carts=unserialize($cart->value);
                $goods=Goods::find()->where(['in','id',array_keys($carts)])->asArray()->all();
            }

        }else{//已经登录，数据从数据库中取
            //取出登录用户的id
            $member_id=\Yii::$app->user->identity->id;
            $model=Cart::find()->where(['member_id'=>$member_id])->asArray()->all();
            $goods_id=[];
            $carts=[];
          foreach($model as $cart){
              $goods_id[]=$cart['goods_id'];
              $carts[$cart['goods_id']]=$cart['amount'];
          }
            //var_dump($carts);exit;
            $goods=Goods::find()->where(['in','id',$goods_id])->asArray()->all();
        }
        return $this->render('cart',['goods'=>$goods,'carts'=>$carts]);
    }

    //购物车页面改变商品数量
    public function actionChgCart($goods_id,$amount){
//        //接受数据
//        $goods_id=\Yii::$app->request->get('goods_id');
//        $amount=\Yii::$app->request->get('amount');
        //var_dump($goods_id);exit;
        //判断是否登录
        if(\Yii::$app->user->isGuest){//未登录存放在cookie中
            //先取出cookie中的购物车商品
            $cookies=\Yii::$app->request->cookies;//(读取信息request里面的cookie）
            $cart=$cookies->get('cart');
            //判断是否有数据
            if($cart==null){//无数据，将新数据加入
                $carts=[$goods_id=>$amount];
            }else{//有数据，分两种情况：1已有该商品，没有该商品，有其他商品
                //序列化数据,取出商品数量
                $carts=unserialize($cart->value);
                if(isset($carts[$goods_id])){//存在该商品
                    $carts[$goods_id]=$amount;
                }else{//不存在该商品
                    $carts[$goods_id]=$amount;
                }
            }
            //将商品信息存入到cookie
            ///实例化cookie组件（写入cookie用response里面的cookie）
            $cookies=\Yii::$app->response->cookies;
            //实例化cookie
            $cookie=new Cookie([
                'name'=>'cart',//cookie名
                'value'=>serialize($carts) ,//cookie值
                'expire'=>1*24*3600+time(),//设置过期时间
            ]);
            $cookies->add($cookie);//将数据保存到cookie
            return 'success';
        }else{//已经登录
                //得到用户id
            $member_id=\Yii::$app->user->identity->id;
            //var_dump($goods_id);exit;
            $model =Cart::find()
                ->andWhere(['member_id'=>$member_id])
                ->andWhere(['goods_id'=>$goods_id])
                ->one();
            //var_dump($model);exit;
            $model->amount=$amount;
            $model->save();
            return 'success';
        }
    }

    //删除功能
    public function actionDelCart($id){
        if(\Yii::$app->user->isGuest){//没登录
            //先取出cookie中的购物车商品
            $cookies=\Yii::$app->request->cookies;//(读取信息request里面的cookie）
            $carts=unserialize($cookies->get('cart'));
            //var_dump($carts);exit;
            unset($carts[$id]);
            $cookies=\Yii::$app->response->cookies;
            //实例化cookie
            $cookie=new Cookie([
                'name'=>'cart',//cookie名
                'value'=>serialize($carts) ,//cookie值
                'expire'=>1*24*3600+time(),//设置过期时间
            ]);
            $cookies->add($cookie);//将数据保存到cookie

        }else{//已经登录
            //var_dump($id);exit;
            $member_id=\Yii::$app->user->identity->id;
            $model =Cart::find()
                ->andWhere(['member_id'=>$member_id])
                ->andWhere(['goods_id'=>$id])
                ->one();
            $model->delete();
        }
        //删除成功，跳转到购物车页面
        return $this->redirect(['cart/cart']);
    }
}