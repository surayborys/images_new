<?php

use yii\db\Migration;

/**
 * Class m180617_131502_alter_comment_table_add_foreign_key
 */
class m180617_131502_alter_comment_table_add_foreign_key extends Migration
{
    public function up()
    {
        $this->addForeignKey('fk-comment-post_id', '{{%comment}}', 'post_id', '{{%post}}', 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('fk-comment-post_id', '{{%comment}}');
    }
}
