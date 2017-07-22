<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods_category`.
 */
class m170721_040728_create_goods_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('goods_category', [
            'id' => $this->primaryKey(),
            'tree'=>$this->integer(),
            'lft'=>$this->integer(),
            'rgt'=>$this->integer(),
            'depth'=>$this->integer()->comment('层级'),
            'name'=>$this->string(50)->comment('名称'),
            'parent_id'=>$this->integer()->comment('上级分类id'),
            'intro'=>$this->text()->comment('简介')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('goods_category');
    }
}
