<?php

use yii\db\Migration;

/**
 * Handles the creation of table `comment`.
 */
class m180512_173747_create_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('comment', [
            'id' => $this->primaryKey(),
            'post_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'text' => $this->text(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('comment');
    }
}
