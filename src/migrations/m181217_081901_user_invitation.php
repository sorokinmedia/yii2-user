<?php

use yii\db\Migration;

/**
 * Class m181217_081901_user_invitation
 */
class m181217_081901_user_invitation extends Migration
{
    private const INVITATION_TABLE_NAME = 'user_invite';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(self::INVITATION_TABLE_NAME, [
            'id' => $this->primaryKey(12),
            'user_id' => $this->integer(12),
            'user_email' => $this->string(255),
            'send_at' => $this->dateTime()->defaultExpression('NOW()'),
            'initiator_id' => $this->integer(12)->notNull(),
            'status' => $this->smallInteger(2),
            'company_id' => $this->integer(12),
            'role' => $this->string(255),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(self::INVITATION_TABLE_NAME);
    }
}
