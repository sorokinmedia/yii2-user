<?php
namespace sorokinmedia\user\handlers\UserMeta\actions;

use sorokinmedia\user\entities\UserMeta\json\UserMetaFullName;
use sorokinmedia\user\entities\UserMeta\UserMetaInterface;

/**
 * Class SetFullName
 * @package sorokinmedia\user\handlers\UserMeta\actions
 *
 * @property UserMetaFullName $user_meta_full_name
 */
class SetFullName extends AbstractAction
{
    public $user_meta_full_name;

    /**
     * SetFullName constructor.
     * @param UserMetaInterface $userMeta
     * @param UserMetaFullName $userMetaFullName
     */
    public function __construct(UserMetaInterface $userMeta, UserMetaFullName $userMetaFullName)
    {
        $this->user_meta_full_name = $userMetaFullName;
        parent::__construct($userMeta);
    }

    /**
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function execute() : bool
    {
        $this->user_meta->setFullName($this->user_meta_full_name);
        return true;
    }
}