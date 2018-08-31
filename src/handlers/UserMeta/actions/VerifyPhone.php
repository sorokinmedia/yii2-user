<?php
namespace sorokinmedia\user\handlers\UserMeta\actions;

/**
 * Class VerifyPhone
 * @package sorokinmedia\user\handlers\UserMeta\actions
 */
class VerifyPhone extends AbstractAction
{
    /**
     * @return bool
     * @throws \yii\db\Exception
     */
    public function execute() : bool
    {
        $this->user_meta->verifyPhone();
        return true;
    }
}