<?php
use yii\db\Migration;

/**
 * Class m180728_132939_add_user_access_token
 */
class m180728_132939_add_user_access_token extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user_access_token', [
            'id' => $this->primaryKey(),
            'access_token' => $this->string(32),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'expired_at' => $this->integer(11),
            'is_active' => $this->integer(1),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user_access_token');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180727_132939_refactor_user cannot be reverted.\n";

        return false;
    }
    */
}
