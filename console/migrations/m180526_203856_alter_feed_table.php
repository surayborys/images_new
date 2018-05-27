<?php

use yii\db\Migration;

/**
 * Class m180526_203856_alter_feed_table
 */
class m180526_203856_alter_feed_table extends Migration
{
    
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->alterColumn('{{%feed}}', 'post_created_at', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->alterColumn('{{%feed}}', 'post_created_at', $this->time());
    }
}
