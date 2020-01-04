<?php
namespace sorokinmedia\user\entities\Company;

use sorokinmedia\user\entities\User\UserInterface;
use yii\db\ActiveQuery;

/**
 * Interface CompanyInterface
 * @package sorokinmedia\user\entities\Company
 *
 * компания
 */
interface CompanyInterface
{
    /**
     * владелец компании
     * @return mixed
     */
    public function getOwner(): ActiveQuery;

    /**
     * список пользователей, входящих в компанию
     * @return mixed
     */
    public function getUsers(): ActiveQuery;

    /**
     * получить список ID сотрудников компании
     * @return array
     */
    public function getUserIdsArray() : array;

    /**
     * статический конструктор
     * @param UserInterface $owner
     * @param string $role
     * @return CompanyInterface
     */
    public static function create(UserInterface $owner, string $role) : CompanyInterface;

    /**
     * удаление основной модели и связанных CompanyUserInterface
     * @return bool
     */
    public function deleteModel(): bool;
}
