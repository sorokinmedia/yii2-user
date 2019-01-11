<?php
namespace sorokinmedia\user\entities\CompanyUser;

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
    public function getUser();

    /**
     * получить компанию
     * @return mixed
     */
    public function getCompany();

    /**
     * получить объект роли
     * @return mixed
     */
    public function getRoleObject();

    /**
     * трансфер данных из формы в модель
     * @return mixed
     */
    public function getFromForm();

    /**
     * добавление модели в БД
     * @return bool
     */
    public function insertModel() : bool;

    /**
     * обновление модели в БД
     * @return bool
     */
    public function updateModel() : bool;

    /**
     * удаление модели из БД
     * @return bool
     */
    public function deleteModel() : bool;
}