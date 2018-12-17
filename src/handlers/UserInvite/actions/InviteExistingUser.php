<?php

namespace sorokinmedia\user\handlers\UserInvite\actions;


use common\components\invite\entities\UserInvite\UserInvite;
use sorokinmedia\user\forms\InviteForm;
use yii\db\Exception;

/**
 * Class InviteExistingUser
 * @package sorokinmedia\user\handlers\UserInvite\actions
 */
class InviteExistingUser extends AbstractAction
{
    /** @var InviteForm */
    protected $form;

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
        $invite = new UserInvite([
            'user_email' => $this->form->email,
            'user_id' => $this->form->user->id ?? null,
            'status' => UserInvite::STATUS_NEW,
            'initiator_id' => $this->form->initiator->id,
            'company_id' => $this->form->company->id,
            'role' => $this->form->role
        ]);

        if (!$invite->save()) {
            throw new Exception(\Yii::t('app', 'Изменения не сохранены'));
        }

        if (!$invite->sendNotificationsToNewUser()) {
            throw new \yii\base\Exception(\Yii::t('app', 'Уведомления не отправлены'));
        }

        return true;
    }
}