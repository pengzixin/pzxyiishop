<?php

use yii\db\Migration;

class m170728_022529_crate_menu_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('menu', [
            'id' => $this->primaryKey()->comment('ID'),
            'label'=>$this->string()->comment('菜单名称'),
            'url'=>$this->string()->comment('路由'),
            'parent_id'=>$this->integer()->comment('上级分类id'),
            'sort'=>$this->integer()->comment('排序'),
        ],$tableOptions);
    }

    public function down()
    {
        $this->dropTable('menu');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
