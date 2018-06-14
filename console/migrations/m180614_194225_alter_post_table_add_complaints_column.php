<?php

use yii\db\Migration;

/**
 * Class m180614_194225_alter_post_table_add_complaints_column
 */
class m180614_194225_alter_post_table_add_complaints_column extends Migration
{
    public function up()
    {
        $this->addColumn('{{%post}}', 'complaints', $this->integer()->notNull()->defaultValue(0));
    }
    
    public function down()
    {
        $this->dropColumn('{{%post}}', 'complaints');
    }
}

