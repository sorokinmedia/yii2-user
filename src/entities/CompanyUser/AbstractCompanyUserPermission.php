<?php
namespace sorokinmedia\user\entities\CompanyUser;

use yii\base\Model;

/**
 * Class AbstractCompanyUserPermission
 * @package sorokinmedia\user\entities\CompanyUser
 *
 * абстракт для json поля с дополнительными разрешениями
 *
 * @property string $id
 * @property string $name
 */
abstract class AbstractCompanyUserPermission extends Model
{
    public $id;
    public $name;
}