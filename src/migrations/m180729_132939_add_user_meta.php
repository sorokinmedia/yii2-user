<?php
use yii\db\Migration;

/**
 * Class m180729_132939_add_user_meta
 * use rbac migration as php yii migrate --migrationPath=@sorokinmedia/user/migrations/
 */
class m180729_132939_add_user_meta extends Migration
{
    public function safeUp()
    {
        $this->createTable('user_meta', [
            'user_id' => $this->integer(),
            'notification_email' => $this->string(255),
            'notification_phone' => $this->json(),
            'notification_telegram' => $this->integer(),
            'full_name' => $this->json(),
            'tz' => $this->string(100),
            'location' => $this->string(250),
            'about' => $this->text(),
            'custom_fields' => $this->json(),
        ]);
        $this->addPrimaryKey('pk-user_meta', 'user_meta', ['user_id']);
    }

    public function safeDown()
    {
        $this->dropTable('user_meta');
    }
}