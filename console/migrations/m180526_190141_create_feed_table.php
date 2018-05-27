<?php

use yii\db\Migration;

/**
 * Handles the creation of table `feed`.
 */
class m180526_190141_create_feed_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('feed', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'author_id' => $this->integer(),
            'author_nickname' => $this->string(),
            'author_name' => $this->string(),
            'author_picture' => $this->string(),
            'post_id' => $this->integer(),
            'post_filename' => $this->string()->notNull(),
            'post_description' => $this->string(),
            'post_created_at' => $this->time()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('feed');
    }
}
