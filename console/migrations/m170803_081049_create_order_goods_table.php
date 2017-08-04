<?php

use yii\db\Migration;

/**
 * Handles the creation of table `order_goods`.
 */
class m170803_081049_create_order_goods_table extends Migration
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
        $this->createTable('order_goods', [
            'id' => $this->primaryKey(),
            'order_id'=>$this->integer()->comment('订单id'),
            'goods_id'=>$this->integer()->comment('商品id'),
            'goods_name'=>$this->string()->comment('商品名称'),
            'logo'=>$this->string()->comment('商品logo'),
            'price'=>$this->decimal(10,2)->comment('价格'),
            'amount'=>$this->integer()->comment('购买数量'),
            'total'=>$this->decimal(10,2)->comment('小计'),
        ],$tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('order_goods');
    }
}
