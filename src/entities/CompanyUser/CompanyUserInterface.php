<?php

namespace sorokinmedia\user\entities\CompanyUser;

use yii\db\ActiveQuery;
use yii\rbac\Role;

/**
 * Interface CompanyUserInterface
 * @package sorokinmedia\user\entities\CompanyUser
 *
 * сотрудник компании
 */
interface CompanyUserInterface
{
    /**
     * получить пользователя
     * @return mixed
     */
    public function getUser(): ActiveQuery;

    /**
     * получить компанию
     * @return mixed
     */
    public function getCompany(): ActiveQuery;

    /**
     * получить объект роли
     * @return mixed
     */
    public function getRoleObject(): ?Role;

    /**
     * трансфер данных из формы в модель
     * @return mixed
     */
    public function getFromForm(): void;

    /**
     * добавление модели в БД
     * @return bool
     */
    public function insertModel(): bool;

    /**
     * обновление модели в БД
     * @return bool
     */
    public function updateModel(): bool;

    /**
     * удаление модели из БД
     * @return bool
     */
    public function deleteModel(): bool;
}
