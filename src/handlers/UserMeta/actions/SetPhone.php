<?php

namespace sorokinmedia\user\handlers\UserMeta\actions;

use sorokinmedia\user\entities\UserMeta\json\UserMetaPhone;
use sorokinmedia\user\entities\UserMeta\UserMetaInterface;
use Throwable;
use yii\db\Exception;

/**
 * Class SetPhone
 * @package sorokinmedia\user\handlers\UserMeta\actions
 *
 * @property UserMetaPhone $user_meta_phone
 */
class SetPhone extends AbstractAction
{
    public $user_meta_phone;

    /**
     * SetPhone constructor.
     * @param UserMetaInterface $userMeta
     * @param UserMetaPhone $userMetaPhone
     */
    public function __construct(UserMetaInterface $userMeta, UserMetaPhone $userMetaPhone)
    {
        $this->user_meta_phone = $userMetaPhone;
        parent::__construct($userMeta);
    }

    /**
     * @return bool
     * @throws Throwable
     * @throws Exception
     */
    public function execute(): bool
    {
        $this->user_meta->setPhone($this->user_meta_phone);
        return true;
    }
}
