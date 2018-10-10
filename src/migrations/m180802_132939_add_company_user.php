<?php
use yii\db\Migration;

/**
 * Class m180802_132939_add_company_user
 * use rbac migration as php yii migrate --migrationPath=@sorokinmedia/user/migrations/
 */
class m180802_132939_add_company_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('company_user', [
            'company_id' => $this->integer(),
            'user_id' => $this->integer(),
            'role' => $this->string(255),
            'permissions' => $this->json()
        ]);
        $this->addPrimaryKey('pk-company_user', 'company_user', ['company_id', 'user_id', 'role']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('company_user');
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
