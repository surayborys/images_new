<?php

use yii\db\Migration;

/**
 * Class m180630_080656_alter_user_table_add_index_username
 */
class m180630_080656_alter_user_table_add_index_username extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE user ADD FULLTEXT INDEX idx_username(username)');
    }
    
    public function down()
    {
        $this->dropIndex('idx_username', 'user');
    }
}
