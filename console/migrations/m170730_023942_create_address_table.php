<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m170730_023942_create_address_table extends Migration
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
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(20)->comment('收货人'),
            'province'=>$this->string(255)->comment('省'),
            'center'=>$this->string(255)->comment('市'),
            'area'=>$this->string(255)->comment('区县'),
            'address'=>$this->string(255)->comment('详细地址'),
            'tel'=>$this->char(11)->comment('电话'),
            'status'=>$this->integer()->comment('默认地址（1默认，0非默认）'),
            'user_id'=>$this->integer()->comment('用户id'),
        ],$tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
