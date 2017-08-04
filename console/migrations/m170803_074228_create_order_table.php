<?php

use yii\db\Migration;

/**
 * Handles the creation of table `order`.
 */
class m170803_074228_create_order_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('order', [
            'id' => $this->primaryKey(),
            'member_id'=>$this->integer()->comment('用户id'),
            'name'=>$this->string(20)->comment('收货人'),
            'province'=>$this->string(255)->comment('省'),
            'center'=>$this->string(255)->comment('市'),
            'area'=>$this->string(255)->comment('区县'),
            'address'=>$this->string(255)->comment('详细地址'),
            'tel'=>$this->char(11)->comment('电话'),
            'delivery_id'=>$this->integer()->comment('配送方式id'),
            'delivery_name'=>$this->string(255)->comment('配送方式名称'),
            'delivery_price'=>$this->decimal(10,2)->comment('配送方式价格'),
            'payment_id'=>$this->integer()->comment('支付方式id'),
            'payment_name'=>$this->string(255)->comment('支付方式名称'),
            'total'=>$this->decimal(10,2)->comment('订单金额'),
            'status'=>$this->integer()->comment('订单状态（0已取消、1待付款、2待发货、3待收货、4完成）'),
            'trade_no'=>$this->string()->comment('第三方交易号'),
            'create_time'=>$this->integer()->comment('创建时间'),
        ],$tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('order');
    }
}
