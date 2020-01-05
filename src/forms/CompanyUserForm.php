<?php

namespace sorokinmedia\user\forms;

use sorokinmedia\user\entities\CompanyUser\AbstractCompanyUser;
use Yii;
use yii\base\Model;

/**
 * Class CompanyUserForm
 * @package common\components\company\forms
 *
 * @property int $company_id
 * @property int $user_id
 * @property string $role
 */
class CompanyUserForm extends Model
{
    public $company_id;
    public $user_id;
    public $role;

    /**
     * CompanyUserForm constructor.
     * @param array $config
     * @param AbstractCompanyUser|null $companyUser
     */
    public function __construct(array $config = [], AbstractCompanyUser $companyUser = null)
    {
        if ($companyUser !== null) {
            $this->company_id = $companyUser->company_id;
            $this->user_id = $companyUser->user_id;
            $this->role = $companyUser->role;
        }
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['company_id', 'user_id', 'role'], 'required'],
            [['company_id', 'user_id'], 'integer'],
            [['role'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'company_id' => Yii::t('sm-user', 'Компания'),
            'user_id' => Yii::t('sm-user', 'Пользователь'),
            'role' => Yii::t('sm-user', 'Роль'),
        ];
    }
}
