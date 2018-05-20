<?php

use yii\db\Migration;

/**
 * Class m180514_185006_alter_comment_table
 */
class m180514_185006_alter_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%comment}}', 'created_at', $this->time());
        $this->addColumn('{{%comment}}', 'updated_at', $this->time());
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('{{%comment}}', 'created_at');
        $this->dropColumn('{{%comment}}', 'updated_at');
    }
}
