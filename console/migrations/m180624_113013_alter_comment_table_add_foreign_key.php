<?php

use yii\db\Migration;

/**
 * Class m180624_113013_alter_comment_table_add_foreign_key
 */
class m180624_113013_alter_comment_table_add_foreign_key extends Migration
{
    public function up()
    {
        $this->addForeignKey('fk-comment-user_id', '{{%comment}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('fk-comment-user_id', '{{%comment}}');
    }
}
