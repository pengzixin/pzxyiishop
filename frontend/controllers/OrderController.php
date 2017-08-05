<?php
namespace frontend\controllers;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Goods;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Request;

class OrderController extends Controller{
    public $layout=false;
    public $enableCsrfValidation=false;
    //订单提交之前页面
    public function actionOrder(){
        //收货人信息
        $user_id=\Yii::$app->user->identity->id;
        $address=Address::find()->where(['user_id'=>$user_id])->all();
        //送货方式
        $deliveries=Order::$deliveries;
        //付款方式
        $payments=Order::$payments;
        //商品信息，从购物车表中读取
        $model=Cart::find()->where(['member_id'=>$user_id])->asArray()->all();
        $goods_id=[];
        $carts=[];
        foreach($model as $cart){
            $goods_id[]=$cart['goods_id'];
            $carts[$cart['goods_id']]=$cart['amount'];
        }
        //var_dump($carts);exit;
        $goods=Goods::find()->where(['in','id',$goods_id])->asArray()->all();
        //调用视图，分配数据
        return $this->render('order',['address'=>$address,'deliveries'=>$deliveries,'payments'=>$payments,'goods'=>$goods,'carts'=>$carts]);
    }

    //提交订单
    public function actionAddOrder($address_id,$delivery_id,$payment_id){
        //实例化模型
        $model=new Order();
        //开始事物
        $transaction=\Yii::$app->db->beginTransaction();
        //$request=new Request();
        //if($request->isPost){
            //加载数据
           // $model->load($request->post());
            //var_dump($model);exit;
            //验证数据
            //if($model->validate()){
                //var_dump($model);exit;
                try{
                    //处理数据
                    $user_id=\Yii::$app->user->identity->id;
                    //获取地址信息
                    $address=Address::findOne(['user_id'=>$user_id,'id'=>$address_id]);
                    $model->member_id=$user_id;
                    $model->name=$address->name;
                    $model->province=$address->province;
                    $model->center=$address->center;
                    $model->area=$address->area;
                    $model->address=$address->address;
                    $model->tel=$address->tel;
                    //获取配送方式
                    $model->delivery_id=$delivery_id;
                    $model->delivery_name = Order::$deliveries[$delivery_id]['name'];
                    $model->delivery_price = Order::$deliveries[$delivery_id]['price'];
                    //付款方式
                    $model->payment_id=$payment_id;
                    $model->payment_name=Order::$payments[$payment_id]['name'];
                    $model->total=0;
                    $model->status=1;
                    $model->create_time=time();
                    $model->save(false);
                    //处理订单商品表数据
                    //获取购物车数据
                    $carts=Cart::find()->where(['member_id'=>$user_id])->all();
                    $total=0;
                    foreach($carts as $cart){
                        $goods=Goods::findOne(['id'=>$cart->goods_id]);
                        $order_goods=new OrderGoods();
                        if($cart->amount<=$goods->stock){
                            $order_goods->order_id=$model->id;
                            $order_goods->goods_id=$goods->id;
                            $order_goods->goods_name=$goods->name;
                            $order_goods->logo=$goods->logo;
                            $order_goods->price=$goods->shop_price;
                            $order_goods->amount=$cart->amount;
                            $order_goods->total=($cart->amount*$goods->shop_price)+$model->delivery_price;
                            $order_goods->save();
                            //改变订单表的统计金额,将购买的每个商品的价钱相加
                            $total+=$order_goods->total;
                            //下单成功后改变商品库存
                            $goods->stock-=$cart->amount;
                            $goods->save();
                            //下单成功后清除购物车
                            $cart->delete();
                        }else{
                            //（检查库存，如果库存不够抛出异常）
                            throw new Exception('商品库存不足，无法继续下单，请修改购物车商品数量');
                        }
                    }
                    //订单生成成功后，计算订单表总金额
                    $model->total=$total;
                    $model->update(false,['total']);
                    //提交事务
                    $transaction->commit();
                    return json_encode('success');
                }catch(Exception $e){//捕获异常
                    //如果异常回滚数据
                    $transaction->rollBack();
                }
           // }else{
                var_dump($model->getErrors());exit;
          //  }
        //}
        //return 'success';
    }

    //订单完成页面
    public function actionEnd(){
        return $this->render('end');
    }

    public function behaviors()
    {
        return [
            'ACF'=>[
                'class'=>AccessControl::className(),
                'only'=>['order','add-order'],//哪些操作需要使用该过滤器
                'rules'=>[
                    [
                        'allow'=>true,//是否允许
                        'actions'=>['order','add-order'],//指定操作
                        'roles'=>['@'],//指定角色 ?表示未认证用户(未登录) @表示已认证用户(已登录)
                    ],
                ]
            ]
        ];
    }
}