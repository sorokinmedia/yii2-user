<?php
use yii\db\Migration;

/**
 * Class m180801_132939_add_company
 * use rbac migration as php yii migrate --migrationPath=@sorokinmedia/user/migrations/
 */
class m180801_132939_add_company extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('company', [
            'id' => $this->primaryKey(),
            'owner_id' => $this->integer(),
            'name' => $this->string(500),
            'description' => $this->text(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('company');
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
