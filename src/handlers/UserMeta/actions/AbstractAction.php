<?php
namespace sorokinmedia\user\handlers\UserMeta\actions;

use sorokinmedia\user\handlers\UserMeta\interfaces\ActionExecutable;
use sorokinmedia\user\entities\UserMeta\UserMetaInterface;

/**
 * Class AbstractAction
 * @package sorokinmedia\user\handlers\UserMeta\actions
 *
 * @property UserMetaInterface $user_meta
 */
abstract class AbstractAction implements ActionExecutable
{
    protected $user_meta;

    /**
     * AbstractAction constructor.
     * @param UserMetaInterface $userMeta
     */
    public function __construct(UserMetaInterface $userMeta)
    {
        $this->user_meta = $userMeta;
        return $this;
    }
}