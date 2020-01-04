<?php

namespace sorokinmedia\user\handlers\UserInvite\actions;


use common\components\invite\entities\UserInvite\UserInvite;
use sorokinmedia\user\entities\UserInvite\AbstractUserInvite;
use sorokinmedia\user\forms\InviteForm;
use Yii;
use yii\db\Exception;

/**
 * Class InviteNewUser
 * @package sorokinmedia\user\handlers\UserInvite\actions
 */
class InviteNewUser extends AbstractAction
{
    /** @var InviteForm */
    protected $form;

    /** @var AbstractUserInvite */
    protected $invite;

    /**
     * Invite constructor.
     * @param InviteForm $form
     */
    public function __construct(InviteForm $form)
    {
        $this->form = $form;
    }

    /**
     * @return bool
     * @throws Exception
     * @throws \yii\base\Exception
     */
    public function execute(): bool
    {
        $this->invite = new UserInvite([
            'user_email' => $this->form->email,
            'status' => UserInvite::STATUS_NEW,
            'initiator_id' => $this->form->initiator->id,
            'company_id' => $this->form->company->id,
            'role' => $this->form->role,
            'meta' => $this->form->meta
        ]);

        if (!$this->invite->save()) {
            throw new Exception(Yii::t('app', 'Изменения не сохранены'));
        }

        if (!$this->invite->sendNotificationsToNewUser()) {
            throw new \yii\base\Exception(Yii::t('app', 'Уведомления не отправлены'));
        }

        return true;
    }
}
