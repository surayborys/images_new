<?php

use yii\db\Migration;

/**
 * Class m180624_114922_alter_comment_table_change_datatype_for_created_at_and_updated_at
 */
class m180624_114922_alter_comment_table_change_datatype_for_created_at_and_updated_at extends Migration
{
    
    public function up()
    {
        $this->alterColumn('{{%comment}}', 'created_at', $this->integer());
        $this->alterColumn('{{%comment}}', 'updated_at', $this->integer());
    }

    public function down()
    {
        $this->alterColumn('{{%comment}}', 'created_at', $this->dateTime());
        $this->alterColumn('{{%comment}}', 'updated_at', $this->dateTime());
    }
}
