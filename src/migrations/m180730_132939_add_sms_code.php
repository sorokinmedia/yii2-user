<?php
use yii\db\Migration;

/**
 * Class m180730_132939_add_sms_code
 * use rbac migration as php yii migrate --migrationPath=@sorokinmedia/user/migrations/
 */
class m180730_132939_add_sms_code extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('sms_code', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'phone' => $this->string(12),
            'created_at' => $this->integer(),
            'code' => $this->integer(4),
            'type_id' => $this->integer(1),
            'ip' => $this->string(20),
            'is_user' => $this->integer(2),
            'is_validated' => $this->boolean(),
            'is_deleted' => $this->boolean()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('sms_code');
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
