<?php

namespace sorokinmedia\user\handlers\User\interfaces;

use sorokinmedia\user\forms\SignUpFormExisted;

/**
 * Interface CreateExisted
 * @package sorokinmedia\user\handlers\User\interfaces
 *
 * метод для переноса пользователей между проектами
 * на вход получает username и password_hash
 * отличается тем, что не генерится новый хеш пароля
 * все остальное как в обычной регистрации
 */
interface CreateExisted
{
    /**
     * @param SignUpFormExisted $signup_form
     * @return bool
     */
    public function createExisted(SignUpFormExisted $signup_form): bool;
}
