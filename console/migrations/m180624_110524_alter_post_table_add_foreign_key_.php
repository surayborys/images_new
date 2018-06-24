<?php

use yii\db\Migration;

/**
 * Class m180624_110524_alter_post_table_add_foreign_key_
 */
class m180624_110524_alter_post_table_add_foreign_key_ extends Migration
{
    public function up()
    {
        $this->addForeignKey('fk-post-user_id', '{{%post}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('fk-post-user_id', '{{%post}}');
    }
}
