<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $name
 * @property string $province
 * @property string $center
 * @property string $area
 * @property string $address
 * @property string $tel
 * @property integer $delivery_id
 * @property string $delivery_name
 * @property string $delivery_price
 * @property integer $payment_id
 * @property string $payment_name
 * @property string $total
 * @property integer $status
 * @property string $trade_no
 * @property integer $create_time
 */
class Order extends \yii\db\ActiveRecord
{
    public $address_id;//地址ID
    //定义送货方式
    public static $deliveries=[
        1=>['name'=>'顺丰快递','price'=>'25.00','detail'=>'速度快，服务好，价格贵','default'=>1],
        2=>['name'=>'圆通快递','price'=>'10.00','detail'=>'速度一般，服务一般，价格便宜','default'=>0],
        3=>['name'=>'EMS','price'=>'20.00','detail'=>'速度一般，服务一般，价格贵，国内任何地址都可以送到','default'=>0],
    ];

    //定义付款方式
    public static $payments=[
        1=>['name'=>'在线支付','detail'=>'支持银行卡、信用卡、支付宝、微信等方式付款','default'=>1],
        2=>['name'=>'货到付款','detail'=>'收货时将现金交于送货员','default'=>0],
    ];
        /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'delivery_id','address_id','payment_id'],'required'],
            [['member_id', 'delivery_id', 'payment_id', 'status', 'create_time'], 'integer'],
            [['delivery_price', 'total'], 'number'],
            [['name'], 'string', 'max' => 20],
            [['province', 'center', 'area', 'address', 'delivery_name', 'payment_name', 'trade_no'], 'string', 'max' => 255],
            [['tel'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '用户id',
            'name' => '收货人',
            'province' => '省',
            'center' => '市',
            'area' => '区县',
            'address' => '详细地址',
            'tel' => '电话',
            'delivery_id' => '配送方式id',
            'delivery_name' => '配送方式名称',
            'delivery_price' => '配送方式价格',
            'payment_id' => '支付方式id',
            'payment_name' => '支付方式名称',
            'total' => '订单金额',
            'status' => '订单状态（0已取消、1待付款、2待发货、3待收货、4完成）',
            'trade_no' => '第三方交易号',
            'create_time' => '创建时间',
        ];
    }

}
