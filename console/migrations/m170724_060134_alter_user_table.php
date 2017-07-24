<?php

use yii\db\Migration;

class m170724_060134_alter_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('user','lsat_login_time',$this->integer());
        $this->addColumn('user','lsat_login_ip',$this->string(255));
    }

    public function down()
    {
        echo "m170724_060134_alter_user_table cannot be reverted.\n";

        return false;
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
