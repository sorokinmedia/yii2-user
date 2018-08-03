<?php
use yii\db\Migration;

/**
 * Class m180727_132939_add_user_table
 */
class m180727_132939_add_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'email' => $this->string(255),
            'password_hash' => $this->string(60),
            'password_reset_token' => $this->string(60),
            'auth_key' => $this->string(45),
            'username' => $this->string(255),
            'status_id' => $this->integer(1),
            'created_at' => $this->integer(11),
            'last_entering_date' => $this->integer(11),
            'email_confirm_token' => $this->string(255),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user');
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
