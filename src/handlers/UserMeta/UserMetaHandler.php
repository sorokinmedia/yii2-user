<?php
namespace sorokinmedia\user\handlers\UserMeta;

use sorokinmedia\user\handlers\UserMeta\interfaces\{Create, Update};
use sorokinmedia\user\entities\UserMeta\UserMetaInterface;

/**
 * Class UserMetaHandler
 * @package sorokinmedia\user\handlers\UserMeta
 *
 * @property UserMetaInterface $user_meta
 */
class UserMetaHandler implements Create, Update
{
    public $user_meta;

    /**
     * UserMetaHandler constructor.
     * @param UserMetaInterface $userMeta
     */
    public function __construct(UserMetaInterface $userMeta)
    {
        $this->user_meta = $userMeta;
        return $this;
    }

    /**
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function create() : bool
    {
        return (new actions\Create($this->user_meta))->execute();
    }

    /**
     * @return bool
     * @throws \yii\db\Exception
     */
    public function update() : bool
    {
        return (new actions\Update($this->user_meta))->execute();
    }
}