<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods`.
 */
class m170721_142214_create_goods_table extends Migration
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
        $this->createTable('goods', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(20)->comment('名称'),
            'sn'=>$this->string(20)->comment('货号'),
            'logo'=>$this->string(255)->comment('LOGO'),
            'goods_category_id'=>$this->integer()->comment('分类ID'),
            'brand_id'=>$this->integer()->comment('品牌分类ID'),
            'market_price'=>$this->decimal(10,2)->comment('市场价格'),
            'shop_price'=>$this->decimal(10,2)->comment('商品价格'),
            'stock'=>$this->integer()->comment('库存'),
            'is_on_sale'=>$this->smallInteger(1)->comment('是否在售，1在售，0下架'),
            'status'=>$this->smallInteger(1)->comment('状态，1正常，0回收站'),
            'sort'=>$this->integer()->comment('排序'),
            'create_time'=>$this->integer()->comment('创建时间'),
            'view_times'=>$this->integer()->comment('浏览次数'),
        ],$tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('goods');
    }
}
