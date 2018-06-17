<?php

use yii\db\Migration;

/**
 * Class m180617_123950_alter_feed_table_add_foreign_key
 */
class m180617_123950_alter_feed_table_add_foreign_key extends Migration
{
    public function up()
    {
        $this->addForeignKey('fk-feed-post_id', '{{%feed}}', 'post_id', '{{%post}}', 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('fk-feed-post_id', '{{%feed}}');
    }
}
