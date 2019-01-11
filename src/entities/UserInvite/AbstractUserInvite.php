<?php

namespace sorokinmedia\user\entities\UserInvite;

use sorokinmedia\ar_relations\RelationInterface;
use yii\db\ActiveRecord;

/**
 * Class AbstractUserInvite
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $user_email
 * @property string $send_at
 * @property integer $initiator_id
 * @property integer $status
 * @property integer $company_id
 * @property string $role
 * @property array $meta
 *
 * @package sorokinmedia\user\entities\UserInvite
 */
abstract class AbstractUserInvite extends ActiveRecord implements RelationInterface, UserInviteInterface
{
    public const STATUS_NEW = 10;
    public const STATUS_APPROVED = 30;
    public const STATUS_REJECTED = 40;


    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'user_invite';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['user_email'], 'email'],
            [['status'], 'default', 'value' => self::STATUS_NEW]
        ];
    }
}