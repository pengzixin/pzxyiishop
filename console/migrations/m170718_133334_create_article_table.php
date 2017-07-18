<?php

use yii\db\Migration;

/**
 * Handles the creation of table `article`.
 */
class m170718_133334_create_article_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('article', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(100)->comment('标题'),
            'intro'=>$this->text()->comment('简介'),
            'category_id'=>$this->integer()->comment('分类'),
            'sort'=>$this->integer(11)->comment('排序'),
            'status'=>$this->smallInteger(2)->comment('状态(-1删除 0隐藏 1正常)'),
            'create_time'=>$this->integer()->comment('创建时间'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('article');
    }
}
