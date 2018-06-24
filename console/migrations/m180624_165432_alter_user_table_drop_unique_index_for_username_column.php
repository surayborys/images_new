<?php

use yii\db\Migration;

/**
 * Class m180624_165432_alter_user_table_drop_unique_index_for_username_column
 */
class m180624_165432_alter_user_table_drop_unique_index_for_username_column extends Migration
{
   
    public function up()
    {
        $this->dropIndex('username', '{{%user}}');
    }

    public function down()
    {
        $this->createIndex('username', '{{%user}}', 'username', $unique = true);
    }
}
