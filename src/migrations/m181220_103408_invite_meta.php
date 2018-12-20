<?php

use yii\db\Migration;

/**
 * Class m181220_103408_invite_meta
 */
class m181220_103408_invite_meta extends Migration
{
    private const TABLE_NAME = 'user_invite';
    private const META_FIELD = 'meta';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE_NAME, self::META_FIELD, $this->json());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(self::TABLE_NAME, self::META_FIELD);
    }
}
